<?php
class Datatype {
	/**
	 * @desc	验证给定的电子邮件地址是否是有效的电子邮件格式
	 * @param	string		$email		需要验证的电子邮件地址
	 * @return	boolean					若给定的电子邮件地址有效， 则返回true
	 */
	public static function vemail ( $email ) {
		$isValid = true;
		$atIndex = strrpos($email, '@');
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else {
			$domain    = mb_substr($email, $atIndex+1);
			$local     = mb_substr($email, 0, $atIndex, 'utf-8');
			$localLen  = mb_strlen($local, 'utf-8');
			$domainLen = mb_strlen($domain, 'utf-8');

      		if ($localLen<1 || $localLen>64 ) {
      			$isValid = false;
      		} elseif ( $domainLen<1 || $domainLen>255 ) {
      			$isValid = false;
			} elseif ( $local[0]=='.' || $local[$localLen-1]=='.' ) {
				$isValid = false;
			} elseif ( preg_match('/\\.\\./', $local) ) {
				$isValid = false;
			} elseif ( !preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) ) {
				$isValid = false;
			} elseif ( preg_match('/\\.\\./', $domain) ) {
				$isValid = false;
			} elseif ( !preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)) ) {
				if ( !preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)) ) $isValid = false;
			}
			
			if ( $isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")) ) $isValid = false;
		}
		
		return $isValid;
	}

	/**
	 * @desc	验证给定的字符串是否符合标准$t
	 * @param	string		$str		需要被验证的名字
	 * @param	int			$t			验证的标准, 0=>非空, 1=>只能包含汉字, 2=>只能包含汉字和数字， 3=>只能包含汉字，英文和数字， 4=>字母和数字
	 * @return	boolean					给定的字符串是否符合指定的标准
	 */
	public static function vstr ( $str, $t=0 ) { defined('LEGAL') OR Helper_Legaldef::def();
		$result = array();
		$return = false;
		try {
			switch ( $t ) {
				case STR_FORMAT_T      : { $return = !empty($str);                                                             break; }
				case STR_FORMAT_CN     : { $return = preg_match_all('/^([\x{4e00}-\x{9fa5}]+)$/u', $str, $result);             break; }
				case STR_FORMAT_NO     : { $return = preg_match_all('/^([0-9]+)$/u', $str, $result);                           break; }
				case STR_FORMAT_CNNO   : { $return = preg_match_all('/^([\x{4e00}-\x{9fa5}|0-9]+)$/u', $str, $result);         break; }
				case STR_FORMAT_ENNO   : { $return = preg_match_all('/^([A-Z|a-z|0-9]+)$/u', $str, $result);                   break; }
				case STR_FORMAT_CNNOEN : { $return = preg_match_all('/^([\x{4e00}-\x{9fa5}|A-Z|a-z|0-9]+)$/u', $str, $result); break; }
				default                : { $return = false;                                                                    break; }
			}
		} catch ( Exception $e ) { $return = false; }
		return (boolean)$return;
	}

	/**
	 * @desc	验证给定的字符串是否符合日期时间标准
	 * @param	string		$str		需要被验证的日期时间字符串
	 * @param 	boolean 	$inctime 	返回部分中是否包含时间信息
	 * @return	date(time)				给定的字符串中所包含的时间信息
	 */
	public static function vdt ( $str, $inctime=false ) {
		if ( empty($str) || trim($str)=='' ) return $inctime ? date('Y-m-d H:i:s') : date('Y-m-d');

		$matches = array();
		$cnt     = preg_match_all('/^(\d{1,4})-(\d{1,2})-(\d{1,2})(\s(\d{1,2}):(\d{1,2}):(\d{1,2}))?$/', $str, $matches);

		if ( $cnt<=0 ) {
			return $inctime ? date('Y-m-d H:i:s') : date('Y-m-d');
		} else {
			$year   = intval($matches[1][0]);
			$month  = intval($matches[2][0]);
			$day    = intval($matches[3][0]);
			$time   = intval($matches[4][0]);
			$hour   = intval($matches[5][0]);
			$minite = intval($matches[6][0]);
			$second = intval($matches[7][0]);

			if ( $year<=999 ) $year   = intval(substr(date('Y'), 0, 2))*100 + $year;
			if ( $year>9999 ) $year   = 9999; // 年份最大数值(假定程序在公元100世纪尾页前已经被替换)
			if ( $month>12 )  $month  = 12;   // 月份最大数值
			if ( $hour>23 )   $hour   = 23;   // 小时最大数值
			if ( $minite>59 ) $minite = 59;   // 分钟最大数值
			if ( $second>59 ) $second = 59;   // 秒最大数值

			if ( in_array($month, array(1, 3, 5, 7, 8, 10, 12)) ) { // 大月最大天数
				if ( $day>31 ) $day = 31;
			} elseif ( in_array($month, array(4, 6, 9, 11)) ) { // 小月最大天数
				if ( $day>30 ) $day = 30;
			} elseif ( $month==2 ) { // 二月最大天数
				$leapp = ( $year%400==0 || ($year%4==0 && $year%100!=0) ) ? true : false;
				if ( $leapp ) {
					if ( $day>29 ) $day = 29;
				} else {
					if ( $day>28 ) $day = 28;
				}
			}

			$month  = str_pad($month,  2, '0', STR_PAD_LEFT);
			$day    = str_pad($day,    2, '0', STR_PAD_LEFT);
			$hour   = str_pad($hour,   2, '0', STR_PAD_LEFT);
			$minite = str_pad($minite, 2, '0', STR_PAD_LEFT);
			$second = str_pad($second, 2, '0', STR_PAD_LEFT);

			return $year . '-' . $month . '-' . $day . ( $inctime ? (' ' . $hour . ':' . $minite . ':' . $second) : '');
		}
	}

	/**
	 * @desc	验证给定的电话或手机号码格式是否正确
	 * @param	string		$no		需要被验证的电话或手机号码
	 * @return	number				是否正确
	 */
	public static function vno ( $no ) { return preg_match('/^(((\d{3}))|(\d{3}-))?((0\d{2,3})|0\d{2,3}-)?[1-9]\d{6,8}$/', $no) ? true : false; }

	/**
	 * @desc	验证给定的internet地址格式是否正确
	 * @param	string		$url		需要被验证的internet地址
	 * @return	boolean					验证结果
	 */
	public static function vurl ( $url ) { return filter_var($url, FILTER_SANITIZE_URL); }
}

class Helper_Datatype extends Datatype {}