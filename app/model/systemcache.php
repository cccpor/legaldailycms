<?php
class SystemCache extends QDB_ActiveRecord_Abstract {
    static function __define(){return array('table_name'=>'system_cache');}
    static function meta(){return QDB_ActiveRecord_Meta::instance(__CLASS__);}
    static function pkv($uuid){return self::meta()->find('`uuid`=?',$uuid)->asArray()->getOne();}    
    static function find(){$args=func_get_args();return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);}

	static function generate ( $file, $class, $function, $args=array() ) {
		$ckey = basename($file) . '-' . $class . '-' . $function;
		foreach ( $args as $k => $v ) { if ( !is_string($v) && !is_numeric($v)) throw new Exception('参数类型不能为复合类型'); else $ckey .= '-' . $k . '=' . $v; }
		return $ckey;
	}
}