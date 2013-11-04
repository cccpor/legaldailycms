<?php
class SystemMessage extends QDB_ActiveRecord_Abstract {
    static function __define () {
        return array (
            'behaviors'      => 'fastuuid',
            'table_name'     => 'system_message',
            'props'          => array ( 'uuid' => array('readonly' => true) ),
            'attr_protected' => 'uuid'
        );
    }

    /**
     * @desc	获取用户的所有未读消息列表
     * @param	int		$receiver	消息的接受者
     * @return	array				用户的所有未读消息列表
     */
    static function getUserUnreadMessageList ( $receiver ) {
    	return self::meta()->find(" `receiver` = ? ", $receiver)->asArray()->getAll();
    }
    
    /**
     * @desc		insert new record into data table
     * @param		hash	$messageData	hash array that contains all the information of new message
     * @return		int						newly inserted record's uuid
     */
    static function addMessage ( $messageData ) {
    	$title    = $messageData['title'];
    	$content  = $messageData['content'];
    	$sender   = $messageData['sender'];
    	$receiver = $messageData['receiver'];
    	$reply    = $messageData['reply'];
    	
    	if ( empty($sender) || empty($receiver) ) throw new Exception ( "发件人收件人信息不能为空！" );
    	if ( empty($title) || empty($content) )   throw new Exception ( "信息不能为空！" );
    	
    	$message           = new self(); 
    	$message->title    = $title;
    	$message->content  = $content;
    	$message->sender   = $sender; 
    	$message->receiver = $receiver; 
    	$message->create   = date("Y-m-d H:i:s");
    	$message->status   = 0;
    	$message->reply    = empty($reply) ? 0 : $reply;
    	$message->save();
    	return $message->uuid;
    }
    
    /**
     * @desc	get messge record by primary key
     * @param 	int		$uuid		primary key of the data table
     * @return	hash				the message record that we want to get in hash format
     */
    static function getMessageByUUID ( $uuid ) {
    	return self::meta()->find(" uuid = ? ", $uuid)->asArray()->getOne();
    }
    
    /**
     * @desc	get unread messages by given condition
     * @param	hash	$condition		set search condition in hash format
     * @return	array					message list in array format
     */
    static function getUnreadMessageListByCondition ( $condition ) {
    	$receiver      = $condition['receiver'];
    	
    	if ( empty($receiver) ) return array();
    	
    	$startDatetime = empty($condition['start'])  ? date("Y-m-d H:i:s", mktime(0, 0, 0, 0, 0, 0)) : $condition['start'];
    	$endDatetime   = empty($condition['end'])    ? date("Y-m-d H:i:s", time())                   : $condition['end'];
    	$sender        = empty($condition['sender']) ? ""                                            : $condition['sender'];
    	$page          = empty($condition['page'])   ? 1                                             : $condition['page'];
    	$number        = empty($condition['number']) ? 10                                            : $condition['number'];
    	
    	$meta = self::meta();
    	
    	if ( empty($sender) ) {
    		$meta = $meta->find(" receiver = ? and `create` > ? and `create` <= ? and status = 0", $receiver, $startDatetime, $endDatetime);
    	} else {
    		$meta = $meta->find(" sender = ? and receiver = ? and `create` > ? and `create`<= ? and status = 0", $sender, $receiver, $startDatetime, $endDatetime);
    	}
    	
    	$meta->limitPage($page, $number);
    	
    	return array('list'=>$meta->asArray()->getAll(), 'pagination'=>$meta->getPagination());
    }
 
    /**
     * @desc	get send messages by given condition
     * @param	hash	$condition		set search condition in hash format
     * @return	array					message list in array format
     */
    static function getSendMessageListByCondition ( $condition ) {
    	$sender = $condition['sender'];
    	
    	if ( empty($sender) ) return array();
    	
    	$startDatetime = empty($condition['start'])    ? date("Y-m-d H:i:s", mktime(0, 0, 0, 0, 0, 0)) : $condition['start'];
    	$endDatetime   = empty($condition['end'])      ? date("Y-m-d H:i:s", time())                   : $condition['end'];
    	$receiver      = empty($condition['receiver']) ? ""                                            : $condition['receiver'];
    	$page          = empty($condition['page'])     ? 1                                             : $condition['page'];
    	$number        = empty($condition['number'])   ? 10                                            : $condition['number'];
    	
    	$meta = self::meta();
    	
    	if ( empty($receiver) ) {
    		$meta = $meta->find(" `sender` = ? and `create` > ? and `create` <= ? and status = 0", $sender, $startDatetime, $endDatetime);
    	} else {
    		$meta = $meta->find(" sender = ? and receiver = ? and `create` > ? and `create`<= ? and status = 0", $sender, $receiver, $startDatetime, $endDatetime);
    	}
    	
    	$meta->limitPage($page, $number);

    	return array('list'=>$meta->asArray()->getAll(), 'pagination'=>$meta->getPagination());
    }
	
	/**
	 * @desc	删除站内信
	 * @param	$uuid	int			需要删除的站内信
	 * @return			boolean		操作是否成功
	 */
	static function deleteMessage ( $uuid ) {
		self::meta()->deleteWhere( ' uuid = ? ', $uuid ) ;
	}
    
    static function find() {
        $args = func_get_args();
        return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);
    }

    static function meta() {
        return QDB_ActiveRecord_Meta::instance(__CLASS__);
    }
}