<?php
class SystemService extends QDB_ActiveRecord_Abstract implements Helper_IModel {
	static function meta(){return QDB_ActiveRecord_Meta::instance(__CLASS__);}
	static function find(){$args=func_get_args();return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);}
	static function pkv($uuid,$attrs='*'){return self::meta()->find("`uuid`=?", $uuid)->asArray()->setColumns($attrs)->getOne();}
    static function __define(){return array('behaviors'=>'fastuuid','table_name'=>'system_service','props'=>array('uuid'=>array('readonly'=>true)),'attr_protected'=>'uuid');}
    
    static function str ( $uuid ) { $service = self::pkv($uuid); if ( !empty($service) ) return $service['name']; }
    
    /**
     * @desc	需要更新或插入数据时对数据的有效性进行验证
     * @param	hash		$data		插入或更新数据的信息， key为数据在数据模式中的字段名称， value为对应的值
     * @return	string					验证失败时返回失败的信息字符串
     */
    static function exam ( $data, $insert=false ) {
    	if ( empty($data['namespace']) || empty($data['controller']) || empty($data['action']) || empty($data['name']) ) return '所需信息不完整！';
    	
    	$exist = self::search(array('namespace'=>$data['namespace'],'controller'=>$data['controller'],'action'=>$data['action']));
    	if ( !empty($exist) ) return '指定的功能的URL路由信息已经被占用！';
    }
    
    /**
     * @desc	需要新添加数据时，对未提供值的字段提供默认值
     * @param	hash		$input		插入或更新数据的信息， key为数据在数据模式中的字段名称， value为对应的值
     */
    static function fit ( $input ) { $data = $input; if ( empty($data['create']) ) $data['create'] = date('Y-m-d H:i:s'); return $data; }

    /**
     * @desc		搜索符合条件的记录， 并将其数字主键作为数组返回
     * @param		hash		$condition		设置的搜索条件，key为属性的名字， value为属性的值
     * @param		string		$order			排序的规则的名称
     * @param		boolean		$desc			是否按照升序排序
     * @return		array						符合条件的记录的数字主键列表
     */
    static function search ( $condition=array(), $order="uuid", $desc=false ) {
    	$query = ' 1 ';
    	
    	if ( !empty($condition['ns']) ) $query .= " AND `namespace` = '{$condition['ns']}' ";
    	if ( !empty($condition['ct']) ) $query .= " AND `controller` = '{$condition['ct']}' ";
    	if ( !empty($condition['ac']) ) $query .= " AND `action` = '{$condition['ac']}' ";
    	if ( !empty($condition['kw']) ) $query .= " AND `name` LIKE '%{$condition['kw']}%' ";

    	$rslt   = array();
    	$desc   = $desc ? "DESC" : "ASC";
    	$result = self::meta()->find($query)->setColumns("uuid")->order(" `$order` $desc ")->asArray()->getAll();
    	foreach ( $result as $k => $v ) $rslt[] = $v['uuid'];
    	return $rslt;
    }
}