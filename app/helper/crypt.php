<?php
abstract class Crypt {
	/**
	 * @desc		对指定的数据进行加密处理， 返回加密后的字符串
	 * @param 		mixed 		$data		加密的数据
	 * @param		string 		$key		加密密匙
	 * @return 		string					加密后字符串
	 */
	public static function en ( $data, $key ) {
		if ( empty($data) ) return null;
	    $prepCode = serialize($data);
	    $block    = mcrypt_get_block_size('des', 'ecb');
	    if ( ($pad=$block-(strlen($prepCode)%$block))<$block ) $prepCode .= str_repeat(chr($pad), $pad);
	    
	    $encrypt = mcrypt_encrypt(MCRYPT_DES, $key, $prepCode, MCRYPT_MODE_ECB);
	    return base64_encode($encrypt);
	}
	 
	/**
	 * @desc				对指定的密文字符串进行解密处理，返回解密后的数据
	 * @param 	string 		$str 		密文字符串
	 * @param 	stirng 		$key 		解密密匙
	 * @return 	mixed 					解密后的数据
	 */
	public static function de ( $str, $key ) {
	    $str   = base64_decode($str);
	    $str   = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
	    $pad   = ord($str[($len = strlen($str)) - 1]);
	    $block = mcrypt_get_block_size('des', 'ecb');
	    if ( $pad&&$pad<$block&&preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str) ) $str = substr($str, 0, strlen($str) - $pad);
	    return unserialize($str);
	}
}

class Helper_Crypt extends Crypt {
	private $_key = '';
	
	public function __construct ( $key ) { if ( empty($key) ) throw new Exception('key for crypt operation can not be empty! '); $this->_key = strval($key); }
	public function __destruct () { }
	
	public function crypt ( $data, $de=false ) { return $de ? (is_string($data) ? $this->de($data, $this->_key) : null) : $this->en($data, $this->_key); }
}