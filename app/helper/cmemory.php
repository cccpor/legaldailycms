<?php
/**
 * !!!REMEMBER!!! max size of plain 64bit element array is 35318
 * scale:100,   store:0.000249, fetch:0.000247
 * scale:1000,  store:0.001886, fetch:0.000275
 * scale:10000, store:0.007745, fetch:0.002498
 * scale:35318, store:0.026876, fetch:0.009707
 * API-HREF : http://php.net/manual/zh/book.memcached.php
 */
class Cmemory {
	private $_server;

	private $_host  = '127.0.0.1';
	private $_port  = '11211';
	private $_expr  = 0;
	
	public function __construct ( $host="", $port="" ) {
		if ( !empty($host) ) $this->_host = $host;
		if ( !empty($port) ) $this->_port = $port;
		
		defined('LEGAL') OR Helper_Legaldef::def();
		$ct = CMEMD;
		
		$this->_server = new $ct();
		$this->_server->connect($this->_host, $this->_port);	
	}
	
	public function __destruct() { $this->_server->close(); }
	
	// 保存数据
    public function store ( $key, $value, $expire=36000 ) {
    	return ( empty($key) || !is_string($key) ) ? false : $this->_server->set($key, $value, $expire);
	}
	
	// 取回缓存数据
    public function fetch ( $key ) {
    	return ( empty($key) || !is_string($key) ) ? false : $this->_server->get($key);
    }
    
    // 设置键的过期时间
    public function expire ( $key, $expire=0 ) { return false;
    	// return ( empty($key) || !is_string($key) || (int)$expire<0 ) ? false : $this->_server->expire($key, (int)$expire);	
    }
    
    // 删除指定键所缓存的数据
    public function delete ( $key ) { return ( empty($key) || !is_string($key) ) ? false : $this->_server->delete($key); }
    
    // 进行调用转移
    public function __call ( $func, $pargv ) {
    	switch ( count($pargv) ) {
    		case 0  : { return $this->_server->{$func}();                                                      break; }
    		case 1  : { return $this->_server->{$func}($pargv[0]);                                             break; }
    		case 2  : { return $this->_server->{$func}($pargv[0], $pargv[1]);                                  break; }
    		case 3  : { return $this->_server->{$func}($pargv[0], $pargv[1], $pargv[2]);                       break; }
    		case 4  : { return $this->_server->{$func}($pargv[0], $pargv[1], $pargv[2], $pargv[3]);            break; }
    		case 5  : { return $this->_server->{$func}($pargv[0], $pargv[1], $pargv[2], $pargv[3], $pargv[4]); break; }
    		default : { throw new Exception('FUNCTION NOT EXISTS'); }
    	}
    }
}

class Helper_Cmemory extends Cmemory {};