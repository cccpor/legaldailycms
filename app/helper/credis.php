<?php
/**
 * scale:100,     store:0.000438, fetch:0.000189
 * scale:1000,    store:0.000836, fetch:0.000561
 * scale:10000,   store:0.006762, fetch:0.002813
 * scale:100000,  store:0.066169, fetch:0.029094
 * scale:1000000, store:0.774953, fetch:0.272022
 * scale:3000000, store:2.384439, fetch:0.864995
 * API-HREF : http://www.cnblogs.com/ikodota/archive/2012/03/05/php_redis_cn.html
 */
class Credis {
	private $_ckey = 'system-cache-helper-credis-all-ckeys'; // 'string key', 所有缓存的string类型的数据的key集合
	private $_hkey = 'system-cache-helper-credis-all-ckeys'; // 'hash key',   所有缓存的hash类型的数据的key集合
	private $_skey = 'system-cache-helper-credis-all-ckeys'; // 'set key',    所有缓存的set类型的数据的key集合
	private $_lkey = 'system-cache-helper-credis-all-ckeys'; // 'list key',   所有缓存的list类型的数据的key集合

	private $_host = '127.0.0.1';
	private $_port = '6379';

	private $_server;

	public function __construct( $host='', $port='') {
		if ( !empty($host) ) $this->_host = $host;
		if ( !empty($port) ) $this->_port = $port;

		defined('LEGAL') OR Helper_Legaldef::def();

		$ct = CREDIS;
		$this->_server = new $ct();
		$this->_server->pconnect($this->_host, $this->_port);
	}

	public function __destruct() { /* $this->_server->close(); */ }

	// 保存数据
    public function store ( $key, $value, $adt=CRSTR ) {
    	if ( empty($key) || !is_string($key) ) return false;
    	switch ( $adt ) {   /* 保存缓存键 */                            /* 保存数据 */
    		case CRSET  : { $this->_server->sadd($this->_skey, $key); $this->ss($key, $value); break; }
    		case CRLIST : { $this->_server->sadd($this->_lkey, $key); $this->sl($key, $value); break; }
    		case CRHASH : { $this->_server->sadd($this->_hkey, $key); $this->sh($key, $value); break; }
    		default     : { $this->_server->sadd($this->_ckey, $key); $this->sc($key, $value); break; }
    	}
    	$this->expire($key, 36000);
		return $key;
	}

	// 取回缓存数据
    public function fetch ( $key ) {
    	if ( empty($key) || !is_string($key) || !($this->_server->exists($key)) ) return false;
    	elseif ( $this->_server->sismember($this->_ckey, $key) ) { return $this->fc($key); }
    	elseif ( $this->_server->sismember($this->_skey, $key) ) { return $this->fs($key); }
    	elseif ( $this->_server->sismember($this->_hkey, $key) ) { return $this->fh($key); }
    	elseif ( $this->_server->sismember($this->_lkey, $key) ) { return $this->fl($key); }
    }

    // 设置键的过期时间
    public function expire ( $key, $expire=0 ) {
    	if ( empty($key) || !is_string($key) || (int)$expire<0 || !($this->_server->exists($key)) ) return false;
    	$this->_server->expire($key, (int)$expire);
    }

    // 删除指定键所缓存的数据
    public function delete ( $key ) {
        if ( empty($key) || !is_string($key) || !($this->_server->exists($key)) ) { return false;}
    	elseif ( $this->_server->sismember($key, $this->_ckey) ) { $this->_server->srem($this->_ckey, $key); break; }
    	elseif ( $this->_server->sismember($key, $this->_skey) ) { $this->_server->srem($this->_skey, $key); break; }
    	elseif ( $this->_server->sismember($key, $this->_hkey) ) { $this->_server->srem($this->_hkey, $key); break; }
    	elseif ( $this->_server->sismember($key, $this->_lkey) ) { $this->_server->srem($this->_lkey, $key); break; }
    	return $this->_server->del($key);
    }

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

    // 简单数据类型(原子数据类型)的操作：字符串， 整数， 浮点数，
    protected function sc ( $key, $value ) { $this->_server->set($key, serialize($value)); }
    protected function fc ( $key ) {
    	try { $storeData = $this->_server->get($key); } catch ( Exception $e ) { $storeData = false; }
    	return false!==$storeData ? unserialize($storeData) : false;
    }

    // SET 存取
    protected function ss ( $key, $value ) { foreach ( $value as $k => $v ) $this->_server->sadd($key, $v); }
    protected function fs ( $key ) { return $this->_server->smembers($key); }

    // LIST 存取
    protected function sl ( $key, $value ) { foreach ( $value as $k => $v ) $this->_server->rpush($key, $v); }
    protected function fl ( $key ) { return $this->_server->lrange($key, 0, -1); }

    // HASH 存取
    protected function sh ( $key, $value ) { foreach ( $value as $k => $v ) $this->_server->hset($key, $k, $v); }
    protected function fh ( $key ) { return $this->_server->hgetall($key); }
}

class Helper_Credis extends Helper_Credis {}