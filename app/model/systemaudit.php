<?php
class SystemAudit extends QDB_ActiveRecord_Abstract {
	static function meta(){return QDB_ActiveRecord_Meta::instance(__CLASS__);}
	static function find(){$args=func_get_args();return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);}
    static function __define(){return array('behaviors'=>'fastuuid','table_name'=>'system_audit','props'=>array('uuid'=>array('readonly'=>true)),'attr_protected'=>'uuid');}
    
    /**
     * @desc		向指定数据表中新增一条数据记录并纪录一条审计信息
     * @param		hash		$data		新的审计记录的数据， key为属性名， value为属性值
     * @param		int			$oper		进行操作的用户的主键
     * @param		string		$class		Model类的名称
     * @return		int						新增数据记录的主键
     */
    public static function create ( $data, $class, $oper, $comment='' ) {
    	if ( !class_exists($class) ) throw new Exception("指定模型类{$class}不存在！");
    	
    	$meta   = $class::meta();
    	$record = new $class();
    	
    	$pkn    = self::getPK($class);
    	$data   = $record->fit($data);
    	$error  = $record->validate($data, true);
    	
    	if ( !empty($error) ) throw new Exception($error);
    	foreach ( $data as $key => $value ) if ( isset($record->$key) && !empty($value) ) $record->$key = $value;

    	$record->save();
    	$pk = $record->uuid;
    	
    	$audit = array('uid'=>$oper, 'db'=>$meta->table->schema, 'table'=>$meta->table->prefix.$meta->table->name, 'pk'=>$pk, 'stuff'=>1, 'comment'=>$comment);
    	try { self::audit($audit); return $pk; } catch ( Exception $e ) { $meta->deleteWhere("`{$pkn}`=?", $pk); return NULL; }
    	return $pk;
    }

    /**
     * @desc	更新给定数据主键的记录
     * @param	hash		$data		需要更新的属性， key为属性的名称， value为更新的值
     * @param	int			$uuid		需要更新的数据的数字主键
     * @param	int 		$oper		进行操作的用户的主键
     * @return	boolean					操作是否成功
     */
    static function update ( $data, $class, $uuid, $oper ) {
    	if ( !class_exists($class) ) throw new Exception("指定模型类{$class}不存在！");
    	
    	$meta   = $class::meta();
    	$record = new $class();
    	$error  = $record->validate($data);
    	
    	$pk = self::getPK($class);
    	if ( !empty($error) ) throw new Exception($error); else $meta->updateDbWhere($data, "`{$pk}`=?", $uuid);
    	
    	$audit = array('uid'=>$oper, 'db'=>$meta->table->schema, 'table'=>$meta->table->prefix.$meta->table->name, 'pk'=>$uuid, 'stuff'=>2, 'comment'=>$comment);
    	self::audit($audit);    	
    }

    /**
     * @desc	删除指定主键的记录
     * @param	int		$uuid		需要删除的记录的数字主键
     * @return	boolean				操作是否成功
     */
    static function delete ( $class, $uuid, $oper ) {
    	if ( !class_exists($class) ) throw new Exception("指定模型类{$class}不存在！");
    	
    	$meta   = $class::meta();
    	$record = new $class();
    	$error  = $record->validate($data);
    	
    	$pk = self::getPK($class);
    	if ( !empty($error) ) throw new Exception($error); else $meta->deleteWhere("`{$pk}`=?", $uuid);
    	
    	$audit = array('uid'=>$oper, 'db'=>$meta->table->schema, 'table'=>$meta->table->prefix.$meta->table->name, 'pk'=>$uuid, 'stuff'=>3, 'comment'=>$comment);
    	self::audit($audit); 

    	return 1;
    }    
    
    /**
     * @desc		合法的操作类型数据
     * @return		array			合法的操作类型数据, key为需要存在数据表中的stuff的值， 值为对操作的描述
     */
    static function legalStuff () {
    	return array(
    		array("abbr"=>"ilegal", "label"=>"非法"),
    		array("abbr"=>"create", "label"=>"创建"),
    		array("abbr"=>"update", "label"=>"修改"),
    		array("abbr"=>"delete", "label"=>"删除")
    	);
    }
    
    /**
     * @desc			根据提供的数据， 向审计信息表中添加一项审计信息，uid(操作人主键), db(数据库名), table(数据表名), pk(数据项主键), stuff(操作的类型) 不能为空 
     * @param			hash		$audit		审计信息数据
     * @throws			Exception				提供的审计数据信息不完整或操作类型非法时抛出异常
     * @return			int						新添加审计项目主键
     */
    static function audit ( $audit ) {
    	if ( empty($audit['uid']) && empty($audit['db']) && empty($audit['table']) && empty($audit['pk']) && empty($audit['stuff'])) throw new Exception("审计信息异常");
    	if ( empty($audit['stuff']) || !in_array($audit['stuff'], array_keys(self::legalStuff())) ) throw new Exception("非法操作类型");
    	
    	$auditOBJ = new self();
    	foreach ( $audit as $k => $v ) if ( isset($auditOBJ->{$k}) && isset($v) ) $auditOBJ->{$k} = $v; $auditOBJ->time = date('Y-m-d H:i:s');
    	
    	$auditOBJ->save();
    	return $auditOBJ->uuid;
    } 

    /**
     * @desc	获取指定model类的数字主键的名称
     * @param	string		$class		类名称
     * @return	string					指定model类的数字主键的名称			
     */
    private static function getPK ( $class ) {
    	$meta = $class::meta();
    	$pk   = array_pop($meta->idname);
    	return $pk;   	
    }
}