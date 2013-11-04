<?php
class Helper_Com {
	/**
	 * @desc	加载视图模板文件， 使用指定的数据渲染模板
	 * @param	$data	hash		渲染模板所需要的数据， key为数据的名字， value为数据的值
	 * @param	$view	string		以::分割的确定模板文件的位置的信息, 比如"namespace::sub::view", 确定的文件位置为${_coms}/${namespace}/${sub$/${view}.php
	 */
	public static function component ( $view, $data=array() ) {
		if ( empty($view) ) return;
		defined('DCOM') or define('DCOM', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'_coms'.DIRECTORY_SEPARATOR);
		foreach ( $data as $name => $value ) if ( is_string($name) ) $$name = $value;
		$vf = DCOM . str_replace('::', DIRECTORY_SEPARATOR, $view) . '.php';
		include $vf;
	}
}