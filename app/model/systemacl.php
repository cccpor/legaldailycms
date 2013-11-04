<?php
class SystemACL extends QDB_ActiveRecord_Abstract implements Helper_IModel {
	static function find(){$args=func_get_args();return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);}
	static function meta(){return QDB_ActiveRecord_Meta::instance(__CLASS__);}	
    static function __define(){return array('table_name'=>'system_acl');}
	
	/**
	 * @desc	按照数据主键， 取数据纪录
	 * @param	int		$uuid		数据主键
	 * @return	mixed				若数据代表的纪录存在， 以assoc－array的形势返回纪录， 否则， 返回NULL
	 */
	public static function pkv ( $primary, $cols='*' ) { return self::meta()->find('`uuid`=?', $primary)->setColumns($cols)->asArray()->getOne(); }

	static function exam ( $data, $insert=false ) { 
		if ( empty($data['group']) ) return '设置目标群组不能为空！'; 
		if ( empty($data['service']) ) return '系统功能不能为空！';
	}
	
	static function fit ( $input ) { $data = $input; $data['create'] = date('Y-m-d H:i:s'); return $data; }
	static function search ( $condition, $order='uuid', $desc ) {
		$group = isset($condition['group'])     ? $condition['group']   : NULL;
		$service = isset($condition['service']) ? $condition['service'] : NULL;
		
		$agt = new Helper_M(__CLASS__);
		$rtn = array();
		$agt->select('uuid');
		if ( !is_null($group) )   $agt = $agt->equal('group', $group);
		if ( !is_null($service) ) $agt = $agt->equal('service', $service);
		
		foreach ( $agt->ok() as $k => $v ) $rtn[] = $v['uuid'];
		return $rtn;
	}
	
	/**
	 * @desc 	获取制定群组允许的服务的主键集合
	 * @param	int		$group		群组的主键
	 * @return	array				群组所被赋予的服务的主键数组
	 */
	static function groupPerms ( $group ) {
		if ( empty($group) || !is_numeric($group) ) return NULL;
		
		$rtn = array();
		foreach ( self::search(array('group'=>$group)) as $k => $v ) {
			$service = self::pkv($v, array('uuid', 'service'));
			$rtn[]   = $service['service'];
		}
		return $rtn;		
	}
}