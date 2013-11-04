<?php
class SystemUser extends QDB_ActiveRecord_Abstract implements Helper_IModel {
	private static $_dftpw = '123456';
	private static function crypt ( $password, $salt ) { return md5($salt . md5($password)); }
	
    static function __define(){return array('behaviors'=>'fastuuid','table_name'=>'system_user','props'=>array('uid'=>array('readonly'=>true)),'attr_protected'=>'uuid');}
    static function find(){$args=func_get_args();return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);}
    static function pkv($uid){return is_numeric($uid)?self::meta()->find('`uid`=?',$uid)->asArray()->getOne():NULL;}
    static function meta(){return QDB_ActiveRecord_Meta::instance(__CLASS__);}
    
    // 新增加一个用户
    static function add ($data) {
	    if ( !self::validateUsername($data['username']) )   throw new Exception('用户名格式错误！要求格式为4-16个汉字，英文字母，数字0-9的组合！');
		if ( !Helper_Datatype::vstr($data['password'], 0) ) throw new Exception('密码错误：要求格式为6-16英文字母或数字组合');
	    if ( ''!=self::getByUsername($data['username']) )   throw new Exception("注册用户名{$data['username']}已经被占用！");

	    $salt     = Helper_Utility::randstr(16, 0);
	    $username = $data['username'];
	    $region   = intval($data['region'])<=0 ? LEGAL_CHINA   : $data['region'];
	    $group    = intval($data['group'])<=0  ? LEGAL_GROUP   : $data['group'];
	    $password = empty($data['password'])   ? self::$_dftpw : $data['password'];

	    $user           = new self();
	    $user->salt     = $salt;
	    $user->group    = $group;
	    $user->region   = $region;
	    $user->username = $username;
	    $user->create   = date("Y-m-d H:i:s");
	    $user->last     = date("Y-m-d H:i:s");
	    $user->password = self::crypt($password, $salt);

	    if ( !empty($data['email']) )    $user->email  = $data['email'];

	    $user->save();
	    return $user->uid;
    }

    /**
     * @desc	根据用户名获取用户信息
     * @param	string		$username		用户名称
     * @return	mixed						若存在，以hash形式返回指定用户名的用户信息，否则返回false
     */
    static function getByUsername($username){return empty($username)?false:self::meta()->find('`username`=?', $username)->asArray()->getOne();}

    /**
     * @desc	验证给定用户名和密码是否正确
     * @param	$username	string	用户登录名称
     * @param	$password	string	用户登录密码明文
     * @return				boolean	登录名和密码匹配，返回true，否则返回false；
     */
    static function authentication ( $username, $password ) {
    	if ( empty($username) || empty($password) ) return false;
    	
    	$user = self::meta()->find('`username`=?', $username)->asArray()->getOne();
    	return (!empty($user)&&self::crypt($password, $user['salt'])==$user['password']) ? true : false;
    }
    
    /**
     * @desc	更新用户信息
     * @param	hash 		$user	需要更新的用户数据
     * @return 	boolean				更新操作是否成功
     */
	static function update ( $user ) {
		try {
			$uid = $user['uid']; if ( empty($uid) ) throw new Exception ("用户ID不能为空！");
			unset($user['uid']); self::meta()->updateDbWhere($user, '`uid`=?', $uid);
		} catch ( Exception $e ) { return false; }
	}

	/**
	 * @desc	更新用户的登陆信息
	 * @param	$uid	int		需要更新密码的用户主键
	 * @param	$pw		string	用户新密码的明文
	 * @return			boolean	操作是否成功
	 */
	static function changepw ( $uid, $pw ) {
		$user = self::meta()->find('`uid`=?', $uid)->asArray()->getOne();
		return ( empty($uid) || empty($pw) ) ? false : self::update(array('uid'=>$uid, 'password'=>self::crypt($pw, $user['salt'])));
	}

	/**
	 * @desc	验证用户名是否符合规范：4至16个汉字，英文字符， 数字
	 * @param	string		$username		需要验证的用户名
	 * @return	boolean						是否符合
	 */
	static function validateUsername ( $username ) {
		$cnname = preg_match_all("/^([\x{4e00}-\x{9fa5}]{2,16})$/u", $username, $matches);
		$enname = preg_match_all("/^([\x{4e00}-\x{9fa5}|A-Z|a-z|0-9]{4,16})$/u", $username, $matches);

		return ($cnname>0||$enname>0);
	}
	
	/**
	 * @desc	需要更新或插入数据时对数据的有效性进行验证
	 * @param	hash		$data		插入或更新数据的信息， key为数据在数据模式中的字段名称， value为对应的值
	 * @return	string					验证失败时返回失败的信息字符串
	 */
	static function exam ( $data, $insert=false ) { 
		if ( empty($data['username']) ) return '用户登陆名称不能为空！'; 
		if ( empty($data['password']) ) return '用户登陆密码不能为空！';
	}
	
	/**
	 * @desc	需要新添加数据时，对未提供值的字段提供默认值
	 * @param	hash		$input		插入或更新数据的信息， key为数据在数据模式中的字段名称， value为对应的值
	 */
	static function fit ( $input ) { 
		$data = $input; 
		if ( empty($data['create']) ) $data['create'] = date('Y-m-d H:i:s');
		if ( empty($data['last']) )   $data['last']   = date('Y-m-d H:i:s');
		if ( empty($data['salt']) )   $data['salt']   = Helper_Utility::randstr(16, 2);
		if ( empty($data['group']) )  $data['group']  = LEGAL_GROUP;
		if ( empty($data['region']) ) $data['region'] = LEGAL_CHINA;
		return $data; 
	}

	/**
	 * @desc		搜索符合条件的记录， 并将其数字主键作为数组返回
	 * @param		hash		$condition		设置的搜索条件，key为属性的名字， value为属性的值
	 * @param		string		$order			排序的规则的名称
	 * @param		boolean		$desc			是否按照升序排序
	 * @return		array						符合条件的记录的数字主键列表
	 */
	static function search ( $cond, $order='last', $desc=true ) {
		$username = isset($cond['username']) ? $cond['username'] : NULL;
		$region   = isset($cond['region'])   ? $cond['region']   : NULL;
		$group    = isset($cond['group'])    ? $cond['group']    : NULL;
		
		$agt = new Helper_M(__CLASS__);
		$rtn = array();
		
		$agt->select('uid');
		if ( !is_null($username) ) $agt->like('username', $username);
		
		if ( !is_null($region) ) {
			$range = SystemTree::range($region);
			$agt->gt('region', $range['min'], true);
			$agt->lt('region', $range['max']);	
		}
		
		if ( !is_null($group) ) {
			$range = SystemTree::range($group);
			$agt->gt('group', $range['min'], true);
			$agt->lt('group', $range['max']);			
		}
		
		foreach ( $agt->ok() as $k => $v ) $rtn[] = $v['uid'];
		return $rtn;
	}
}