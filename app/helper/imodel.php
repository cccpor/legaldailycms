<?php
interface Helper_IModel {
	/**
	 * @desc	需要更新或插入数据时对数据的有效性进行验证
	 * @param	hash		$data		插入或更新数据的信息， key为数据在数据模式中的字段名称， value为对应的值
	 * @return	string					验证失败时返回失败的信息字符串
	 */
	static function exam ( $data, $insert ); /*{ if ( empty($data['require_field']) ) return '所需信息不完整！'; }*/
	
	/**
	 * @desc	需要新添加数据时，对未提供值的字段提供默认值
	 * @param	hash		$input		插入或更新数据的信息， key为数据在数据模式中的字段名称， value为对应的值
	 */
	static function fit ( $input ); /* { $data = $input; if ( !empty($data['create']) ) $data['create'] = date('Y-m-d'); return $data; }*/

	/**
	 * @desc		搜索符合条件的记录， 并将其数字主键作为数组返回
	 * @param		hash		$condition		设置的搜索条件，key为属性的名字， value为属性的值
	 * @param		string		$order			排序的规则的名称
	 * @param		boolean		$desc			是否按照升序排序
	 * @return		array						符合条件的记录的数字主键列表
	 */
	static function search ( $condition, $order, $desc );	
}