<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'sphinxapi.php';
class Search extends SphinxClient {
	public function __construct () { parent::__construct(); }
	public function __destruct() { parent::__destruct(); unset($this); }
	
	/**
	 * @desc		根据给定的关键词， 通过sphinx分词工具， 检索出系统含有给定关键词的judgment记录的主键
	 * @param		string	$key	给定的关键词
	 * @return		记录信息中含有给定关键词的judgment项的主键列表
	 */
	public function search ( $key, $index='judgment' ) {
		$return = array();
		$result = $this->Query ( $key, $index ); // echo $this->GetLastError() . '<br/>' . $this->GetLastWarning() . '<br/>';
		foreach ( $result['matches'] as $primary => $attr) $return[] = $primary;
		return $return;
	}

	/**
	 * @desc 	启动search守护进行
	 */
	public static function startDemon () {
		$command = '/usr/local/sphinx/bin/searchd --config /usr/local/sphinx/etc/sphinx.conf'; 
		return RootCommand::runCommand($command);
	}
	
	/**
	 * @desc	关闭search守护进程
	 */
	public static function endDemon () { 
		$command = '/usr/local/sphinx/bin/searchd --config /usr/local/sphinx/etc/sphinx.conf --stop';
		return RootCommand::runCommand($command);
	}
	
	/**
	 * @desc	监测sphinx搜索进程是否运行正常
	 * @return	boolean		true, 'SEARCHD'守护进程运行正常; 否则为false。
	 */
	public static function runp ( $command ) {
		$command = "ps aux | grep {$command} | grep -v grep";
		$output  = array();
		$last    = exec($command, $output);
		return empty($output) ? false : true ;
	}
	
	/**
	 * @desc	对指定的数据源建立索引
	 * @param	string		$source		索引源的名字
	 * @return	boolean					操作的结果
	 */
	public static function index ( $source ) {
		$command = "/usr/local/sphinx/bin/indexer {$source}";
		return RootCommand::runCommand($command);
	}
}

class RootCommand {
	public static function runCommand ( $command ) {
		$ouput   = array();
		$return  = 0;
		$command = "echo @c*z6688 | sudo -u root -S {$command}";
		exec($command, $output, $return);
		return $output;
	}
}

class Helper_Search extends Search {}
