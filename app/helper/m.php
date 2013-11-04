<?php
/**
 * @desc	基于qeephp的MYSQL查询代理类，系统主要的select查询通过该类进行
 * @author 	zhonglei
 * @usage :
 * $agt = new M(__CLASS__);
 * $agt->row($primary); // 
 * $agt->unique($col, $val, $order, $desc, '*'); // 
 * $agt->select('*')->where()->equal($col1, $val1)->like($col2, $val2)->nequal($col3, $val3)->lt($col4, $val4)->gt($col5, $val5)->in($col6, $val6)->order($col, $desc)->ok();
 * $agt->insert()->set($col1, $val1)->set($col2, $val2)->ok();
 * $agt->insert()->sets($data)->ok();
 * $agt->update()->set($col1, $val1)->set($col2, $val2)->where()->equal($col3, $val3)->ok();
 * $agt->delete()->set($col1, $val1)->where()->equal($col3, $val3)->ok();
 */
class M {
	private $_primary = ""; // TABLE的主键的名称
	private $_class   = ""; // 需要操作的类的名称， 大小写敏感
	private $_props   = ""; // 给定model的属性集合
	private $_meta    = ""; // 需要查询的类meta对象

	private $_op        = '';      // 要进行查询操作
	private $_data      = array(); // 属性， 值集合
	
	private $_orders    = array(); // SELECT查询结果集的排序依据属性名集合
	private $_cols      = array(); // 结果集合中包含的列的名称集合

	private $_condition = '';    // 查询条件语句
	private $_nequals = array(); // 使用'!='做SQL查询
	private $_equals  = array(); // 使用'='做SQL查询
	private $_likes   = array(); // 使用'LIKE'做SQL查询
	private $_ins     = array(); // 使用'IN'做SQL查询
	private $_gts     = array(); // 使用('>', '>=')做SQL查询
	private $_lts     = array(); // 使用('<', '<=')做SQL查询	


	/**
	 * @DESC		生成查询条件语句部分
	 */
	protected function prepare () {
		$this->_condition = ' TRUE ';
		if ( !empty($this->_nequals) ) foreach ( $this->_nequals as $nk => $nv ) $this->_condition .= " AND `{$nk}` != '%{$nv}%'";
		if ( !empty($this->_equals) )  foreach ( $this->_equals as $ek => $ev )  $this->_condition .= " AND `{$ek}` = '{$ev}' ";
		if ( !empty($this->_likes) )   foreach ( $this->_likes as $lk => $lv )   $this->_condition .= " AND `{$lk}` LIKE '%{$lv}%'";
		if ( !empty($this->_gts) )     foreach ( $this->_gts as $rk => $rv )     $this->_condition .= " AND `{$rk}`" . ($rv['inc']?'>=':'>') . "{$rv['critical']} ";
		if ( !empty($this->_lts) )     foreach ( $this->_lts as $rk => $rv )     $this->_condition .= " AND `{$rk}`" . ($rv['inc']?'<=':'<') . "{$rv['critical']} ";
		if ( !empty($this->_ins) )     foreach ( $this->_ins as $sk => $sv )     $this->_condition .= " AND `{$sk}` IN (" . join(',', $sv) . ") ";
	
		return $this;
	}	
	
	/**
	 * @DESC	constructor of class
	 * @param 	string	$class		需要操作的类的名称， 大小写敏感
	 * @param	string	$primary	操作的类抽象的数据表的数字主键属性名称
	 */
	public function __construct( $className, $primary='uuid' ) {
		$this->_primary = $primary;
		$this->_class   = $className;
		$this->_meta    = $className::meta();
		$this->_props   = (array)$this->_meta->props;
	}
	
	/**
	 * @desc	desctructor of class
	 */
	public function __destruct () {}

	/**
	 * @desc	判断给定的字段是否是给定数据表属性名
	 * @param	string	$col	数据表字段名称
	 * @return	boolean			给定字符串是给定数据模式的名， 返回true， 否则返回false
	 */
	public function colp ( $col ) { return isset($this->_props[$col]); }
	
	/**
	 * @DESC	增加一个相似查询条件
	 * @param	string		$col		设置相似查询条件的属性名
	 * @param	mixed		$value		设置相似查询条件的关键值
	 * @return	object		this
	 */
	public function like ( $col, $value ) {
		if ( $this->colp($col) && isset($value) ) $this->_likes[$col] = $value;
		return $this;
	}

	/**
	 * @DESC	增加一个相等查询条件
	 * @param	string		$col		设置相等查询条件的属性名
	 * @param	mixed		$value		设置相等查询条件的关键值
	 * @return	object		this
	 */
	public function equal ( $col, $value ) {
		if ( $this->colp($col) && isset($value) ) $this->_equals[$col] = $value;
		return $this;
	}

	/**
	 * @DESC	增加一个不等查询条件
	 * @param	string		$col		设置不相等查询条件的属性名
	 * @param	mixed		$value		设置不相等查询条件的关键值
	 * @return	object		this
	 */
	public function nequal ( $col, $value ) {
		if ( $this->colp($col) && isset($value) ) $this->_nequals[$col] = $value;
		return $this;
	}

	/**
	 * @DESC	增加一个集合查询条件
	 * @param	string		$col		设置集合查询条件的属性名
	 * @param	mixed		$value		设置集合查询条件的属性名允许的取值集合
	 * @return	object		this
	 */
	public function in ( $col, $values ) {
		if ( $this->colp($col) ) {
			$this->_ins[$col] = empty($values) ? array(0) : $values;
		}
		return $this;
	}

	/**
	 * @DESC	增加一个大于（等于）查询条件
	 * @param	string		$col		设置范围查询条件的属性名
	 * @param	mixed		$value		设置范围查询条件的开始值
	 * @param	boolean		$inc		结果集中是否包含边界值
	 * @return	object		this
	 */
	public function gt ( $col, $value, $inc=false ) {
		if ( $this->colp($col) ) {
			if ( isset($start) ) $this->_gts[$col]['critical'] = $value;
			if ( isset($inc) )   $this->_gts[$col]['inc']      = $inc;
		}
		return $this;		
	}

	/**
	 * @DESC	增加一个大于（等于）查询条件
	 * @param	string		$col		设置范围查询条件的属性名
	 * @param	mixed		$end		设置范围查询条件的结束值
	 * @param	boolean		$inc		结果集中是否包含边界值
	 * @return	object		this
	 */	
	public function lt ( $col, $value, $inc=false ) {
		if ( $this->colp($col) ) {
			if ( isset($start) ) $this->_lts[$col]['critical'] = $value;
			if ( isset($inc) )   $this->_lts[$col]['inc']      = $inc;
		}
		return $this;		
	}

	/**
	 * @DESC	增加一个结果集的排序规则
	 * @param	string		$col		TABLE的column名称
	 * @param	boolean		$desc		是否逆序排序
	 * @return	object		this
	 */
	public function order ( $col, $desc=false) { if ( $this->colp($col) ) $this->_orders[] = " `{$col}` ".($desc?' DESC ':' ASC '); return $this; }

	/**
	 * @DESC	设置结果集合中各条记录需要包含的属性名
	 * @param	array		$cols		结果集合中各条记录需要包含的属性名集合
	 * @return	object		this
	 */
	public function select ( $cols=array() ) {
		$this->_cols = array();
		$this->_op   = 'select';
		if ( is_array($cols) ) {
			foreach ( $cols as $k => $attr ) if ( $this->colp($attr) && !in_array($attr, $this->_cols) ) array_push($this->_cols, $attr);
		} elseif ( is_string($cols) && $this->colp($cols) ) { 
			if ( !in_array($cols, $this->_cols) ) $this->_cols[] = $cols;
		}
		return $this;
	}

	/**
	 * @DESC	1）创建, 2）更新 操作时设置需要设定的属性的（键/值）对， 前面对属性键/值集合的设置将被替换
	 * @param	string		$col		需要设置的属性的名字
	 * @param	mixed		$val		设置的目标值
	 * @return	object		this
	 */
	public function set ( $col, $val ) { return $this->sets(array($col=>$val)); }
	
	/**
	 * @DESC	1）创建, 2）更新 操作时设置需要设定的属性的（键/值）对， 前面对属性键/值集合的设置将被替换
	 * @param	array		$data		需要设置的属性的键/值hash数组
	 * @return	object		this
	 */
	public function sets ( $data ) {
		if ( is_array($data) ) foreach ( $data as $k => $v ) if ( $this->colp($k) && isset($v) ) $this->_data[$k] = $v;
		return $this;
	}	

	/**
	 * @DESC	清除前面所设置的条件
	 * @return 	Helper_M		this
	 */
	public function clean () {
		if ( !empty($this->_data) ) $this->_data = array();
		// do not do this
		if ( !empty($this->_nequals) ) $this->_nequals = array();
		if ( !empty($this->_equals) )  $this->_equals  = array();
		if ( !empty($this->_ranges) )  $this->_ranges  = array();
		if ( !empty($this->_likes) )   $this->_likes   = array();
		if ( !empty($this->_ins) )     $this->_ins     = array();
		if ( !empty($this->_gts) )     $this->_gts     = array();
		if ( !empty($this->_lts) )     $this->_lts     = array();
		

		if ( !empty($this->_condition) ) $this->_condition = '';
		if ( !empty($this->_orders) )    $this->_orders    = array();
		if ( !empty($this->_cols) )      $this->_cols      = array();
		if ( !empty($this->_op) )        $this->_op        = '';
		
		return $this;
	}

	/**
	 * @DESC		获取所有结果集合数组， 集合中的任何一条记录只是简单的主键值或设置的属性/值对
	 * @param		boolean		$check		若为true， 返回将要执行的sql语句
	 * @return		mixed					符合条件的结果集合
	 */
	public function ok ( $check=false ) {
		switch ( strtoupper($this->_op) ) {
			case 'COUNT'  : { #TODO : 请检查这个做是否可行
				$this->prepare();
				return $this->_meta->where($this->_condition)->count(); 
				break; 
			}
			case 'INSERT' : {
				$obj = new $this->_class();
				foreach ( $this->_data as $k => $v ) $obj->{$k} = $v;
				$obj->save();
				return $obj->{$this->_primary}; 
				break; 
			}
			case 'UPDATE' : { 
				$this->prepare();
				return $this->_meta->updateDbWhere($this->_data, $this->_condition);
				break; 
			}
			case 'DELETE' : {
				$this->prepare();
				return $this->_meta->deleteWhere($this->_condition);				 
				break; 
			}
			case 'SELECT' : {
				$this->prepare();
				return $this->_meta->find($this->_condition)->order($this->_orders)->setColumns($this->_cols)->asArray()->getAll();
				break; 
			}
			default : { break; }
		}
	}

	public function where ()  { return $this; }
	public function count ()  { $this->_op = 'count';  return $this; }
	public function update () { $this->_op = 'update'; return $this; }
	public function delete () { $this->_op = 'delete'; return $this; }
	public function insert () { $this->_op = 'insert'; return $this; }
	
	/**
	 * @desc		根据数字主键，从数据表中获取一条记录
	 * @param		int 	$pk		数字主键
	 * @param		mixed 	$cols	需要选取的列的名字数组
	 */
	public function row ( $pk, $cols='*' ) { 
		$this->clean(); 
		$this->_op = 'select'; 
		$result = $this->select($cols)->where()->equal($this->_primary, $pk)->ok(); 
		return $result[0];
	}	

	/**
	 * @DESC		根据给定主键， 从数据表中取出一条记录
	 * @param		string	$col	条件属性名
	 * @param		mixed	$val	条件值
	 * @param		string	$order	排序规则属性名
	 * @param		boolean	$desc	排序规则是否是逆序
	 * @param		array	$cols	取出的记录所要包含的属性名集合
	 * @return		hash					
	 */
	public function unique ( $col, $val, $order, $desc=false, $cols=array() ) { 
		$this->clean(); 
		$this->_op = 'select'; 
		$result = $this->select($cols)->where()->equal($col,$val)->ok(); 
		return $result[0];
	}
}

class Helper_M extends M {}