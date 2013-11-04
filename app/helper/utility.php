<?php
class Helper_Utility {
	/**
	 * @desc	截取中文字符
	 * @param	sting	$str		需截取字符
	 * @param	int		$len		截取字数
	 * @param	sting	$replace	后缀(可为空,默认为...)
	 * @return	sting
	 */
	static function cutstr($str, $len, $replace='...') { return mb_strlen($str, 'utf-8')>$len ? str_replace('&', '', mb_substr($str, 0, $len, 'utf-8') . $replace) : $str; }

	//取随机数
	static function rand ( $length, $numeric=0 ) {
		PHP_VERSION < '4.2.0' ? mt_srand((double) microtime() * 1000000) : mt_srand();
		$seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
		$hash = '';
		$max = strlen($seed) - 1;
		for ($i = 0; $i < $length; $i++) {
			$hash .= $seed[mt_rand(0, $max)];
		}
		return $hash;
	}

	/**
	 * @desc	随机生成一个字符串
	 * @param	int		$length	长度
	 * @param	int		$mode	生成模式: 1纯数字, 2纯小写字母, 3纯大写字母, 0数字大小字母混合
	 * @return	string
	 */
	static function randstr($length = 32, $mode = 0) {
		switch ($mode) {
			case '1': { $str = '1234567890';                                                     break; }
			case '2': { $str = 'abcdefghijklmnopqrstuvwxyz';                                     break; }
			case '3': { $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';                                     break; }
			default : { $str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; break; }
		}

		$rslt = '';
		$len  = strlen($str) - 1;
		$num  = 0;

		for ($i = 0; $i < $length; $i++) {
			$num = rand(0, $len);
			$a = $str[$num];
			$rslt = $rslt . $a;
		}
		return $rslt;
	}

	/**
	 * @description: converte array to an XML document. Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
	 * @param	array				$data			array
	 * @param	string				$rootNodeName	what you want the root node to be - defaultsto data.
	 * @param	SimpleXMLElement	$xml			should only be used recursively
	 * @return	string								array data in XML format
	 */
	public static function arrayToXML ($data, $rootNodeName='data', $xml=null) {
		if ( ini_get('zend.ze1_compatibility_mode')==1 ) ini_set('zend.ze1_compatibility_mode', 0);
		if ( $xml==null ) $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName></$rootNodeName>");

		foreach ($data as $key => $value) {
			if (is_numeric($key)) $key = "element"; //$key = preg_replace('/[^(a-z\_|\d)]/i', '', $key);
			if (is_array($value)) {
				$node = $xml->addChild($key);
				Helper_Utility::arrayToXML($value, $rootNodeName, $node);
			} else { //$value = htmlentities($value);
				$xml->addChild($key, is_null($value)?"":$value);
			}
		}

		return $xml->asXML();
	}

	/**
	 * @desc	使用冒泡排序算法， 对数组进行排序， 给定的数组的索引一定要连续并别从0开始
	 * @param	array	$array		需要排序的数组
	 * @param	string	$attr		排序时依赖的数组元素的属性， 若为空， 则直接比较原数组元素本身
	 * @return	array				按成排序的数组
	 */
	static function bubbleSort ( $array, $attr ) {
		if ( empty($array[0]) ) throw new Exception("Top Level Of Array Must With Numberic Index ");
		$returnArray = array();

		for ( $outterIndex=0; $outterIndex<count($array)-1; $outterIndex++ ) {
			for ( $innerIndex=$outterIndex+1; $innerIndex<count($array); $innerIndex++ ) {
				$outterValue = empty($attr) ? $array[$outterIndex] : $array[$outterIndex][$attr];
				$innerValue  = empty($attr) ? $array[$innerIndex]  : $array[$innerIndex][$attr];

				if ( $outterValue>=$innerValue ) {
					$tempItem            = $array[$outterIndex];
					$array[$outterIndex] = $array[$innerIndex];
					$array[$innerIndex]  = $tempItem;
				}
			}
		}

		return $array;
	}

	/**
	 * @desc	对输入数组， 根据给定的数组项属性， 进行逆序排序并返回， 返回数组的对应关系保持不变
	 * @param	hash	$array		数组键为系统数据主键， 值为简单整数
	 * @return	hash				排序后的hash
	 */
	static function hashSort ( $hash ) {
		$returnArray = array();
		$tempArray   = array();

		foreach ( $hash as $key => $value ) {
			$item = array();
			$item['key']   = $key;
			$item['value'] = $value;
			$tempArray[]   = $item;
		}

		$size = count ($tempArray);
		for ( $i=0; $i<$size-1; $i++ ) {
			for ( $j=$i+1; $j<=$size-1; $j++ ) {
				$prev = $tempArray[$i];
				$post = $tempArray[$j];

				if ( $prev['value']<$post['value'] ) {
					$temp = $prev;
					$tempArray[$i] = $post;
					$tempArray[$j] = $temp;
				}
			}
		}

		foreach ( $tempArray as $key => $value ) $returnArray[$value['key']] = $value['value'];

		return $returnArray;
	}

	/**
	 * @desc	判断给定的字符串是否是以$end结尾
	 * @param	string		$str	需要判断的字符串
	 * @param	string		$end	特征字串
	 * @return 	boolean				若$str以$end结尾， 返回true， 否则， 返回false
	 */
	public static function endWith ( $str, $end ) {
		if ( empty($end) ) return true;
		if ( empty($str) ) return false;

		$lenStr = mb_strlen($str, "utf-8");
		$lenEnd = mb_strlen($end, "utf-8");
		$keyPos = mb_strrpos($str, $end, null, "utf-8");
		$result = $keyPos===($lenStr-$lenEnd);

		return $result;
	}

	/**
	 * @desc		判断$str是否是以$key开始
	 * @param		string		$str		需要判断的字符串
	 * @param		string		$key		特征子串
	 * @return 		boolean					$str以$key开头， 返回true， 否则返回fasle
	 */
	public static function startWith ( $str, $key ) {
		if ( empty($key) ) return true;
		if ( empty($str) ) return false;

		$lenStr = mb_strlen($str, "utf-8");
		$lenKey = mb_strlen($key, "utf-8");
		$keyPos = mb_strpos($str, $key, null, "utf-8");
		$result = $keyPos===0;

		return $result;
	}
	
	/**
	 * @desc	根据给定的数据html文本
	 * @param	string		$tag		html标记的名称
	 * @param	string		$content	标记的内容
	 * @param	hash		$attrs		以assoc－array形式提供的html标记属性
	 * @return	string					html字符串
	 */
	public static function html ( $tag, $content, $attrs=array() ) {
		$return = '<' . $tag;
		foreach ( $attrs as $key => $attr ) $return .= " {$key}=" . '"' . addslashes($attr) . '"';
		$return .= empty($content) ? '/>' : ('>'.$content."</{$tag}>");
		return $return;
	}
	
	/**
	 * @desc	从指定结果集合中取出部分作为分页数据返回；
	 * @param	array		$dataArray		原始数据， 必须为数组
	 * @param	int			$page			分页信息， 页码
	 * @param	int			$number			分页信息， 每页显示多少条记录
	 * @return	hash						array('list'=>'分页中的数据', 'pagination'=>'包含分页信息的hash表');
	 */
	static function pagination ( $dataArray, $page=1, $number=10 ) {
		if ( $number<=0 ) $number = 10;
		if ( $page<=0   ) $page   = 1;
	
		if ( empty($dataArray) || gettype($dataArray)!='array' ) return array(
			'pagination' => array( 'record_count' => 0,
				'page_count'        => 0,
				'first'             => 1 ,
				'last'              => 0 ,
				'next'              => 0,
				'prev'              => 0 ,
				'current'           => 0 ,
				'page_size'         => $number ,
				'page_base'         => 1
			),
			'list' => array()
		);
	
		$recordCount = count($dataArray);
		$pageCount   = ceil($recordCount/$number);
		$lastPage    = $pageCount;
		$pagination  = array('record_count' => $recordCount,
			'page_count'        => $pageCount,
			'first'             => 1,
			'last'              => $lastPage ,
			'next'              => $page==$pageCount ? $page : $page+1 ,
			'prev'              => $page==1 ? 0 : $page-1 ,
			'current'           => $page ,
			'page_size'         => $number ,
			'page_base'         => 1 ) ;
	
		$list = array();
	
		$startCount = ($page-1)*$number;
		$endCount   = $startCount+$number;
	
		$count = 0;
		defined('LEGAL') OR Helper_Legaldef::def();
		foreach ( $dataArray as $key => $data ) {
			if ( $count>=$startCount && $count<$endCount ) {
				if ( abs($key-$count)<=100/*如果原数组键和循环控制变量相差不大*/ ) $list[] = $data; else $list[$key] = $data;
				$count++;
			} elseif ( $count<$startCount) {
				$count++;
				continue;
			} elseif ( $count>=$endCount ) {
				break;
			}
		}
	
		return array('pagination'=>$pagination, 'list'=>$list);
	}	
}