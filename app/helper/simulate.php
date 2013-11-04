<?php
class Helper_Simulate {
	/**
	 * @desc	simulate a post request like web browser to other server
	 * @param	$url			string		url string
	 * @param	$postDataArray	array		post data in hash array format
	 * @return					mixed		depending on the url return
	 */
	static function dopost ( $url, $postDataArray ) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataArray);

		$output = curl_exec($ch);

		curl_close($ch);
		return $output;
	}


	/**
	 * @desc	simulate a get request like web brower to other web server
	 * @param	$url	string	the target url
	 * @return			mixed	depending on the url
	 */
	static function doget ( $url ) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:24.0) Gecko/20100101 Firefox/24.0');
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$output = curl_exec($ch);

		curl_close($ch);
		return $output;
	}

	/**
	 * @desc	编码
	 * @param	string		$str	unicode 字符串
	 * @return	string				编码后的字符串
	 */
	function escape ( $str ) {
		preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $newstr);
		$ar = $newstr[0];
		foreach($ar as $k=>$v){
			if(ord($ar[$k])>=127){
				$tmpString=bin2hex(iconv("GBK","ucs-2//IGNORE",$v));
				if (!eregi("WIN",PHP_OS)){
					$tmpString = substr($tmpString,2,2).substr($tmpString,0,2);
				}
				$reString.="%u".$tmpString;
			}else{
				$reString.= rawurlencode($v);
			}
		}
		return $reString;
	}

	//解码为HTML实体字符
	function unescape ($source) {
		$decodedStr = "";
		$pos = 0;
		$len = strlen ($source);
		while ($pos < $len){
			$charAt = substr ($source, $pos, 1);
			if ($charAt == '%'){
				$pos++;
				$charAt = substr ($source, $pos, 1);
				if ($charAt == 'u'){
					$pos++;
					$unicodeHexVal = substr ($source, $pos, 4);
					$unicode = hexdec ($unicodeHexVal);
					$entity = "&#". $unicode . ';';
					$decodedStr .= utf8_encode ($entity);
					$pos += 4;
				}else{
					$hexVal = substr ($source, $pos, 2);
					$decodedStr .= chr (hexdec ($hexVal));
					$pos += 2;
				}
			}else{
				$decodedStr .= $charAt;
				$pos++;
			}
		}
		return $decodedStr;
	}
}