<?php
class SystemTree extends QDB_ActiveRecord_Abstract implements Helper_IModel {
	public static $block = array(1, 2, 2, 2, 2, 2, 2, 2, 2, 2);
	
	static function find(){$args=func_get_args();return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args);}
	static function meta(){return QDB_ActiveRecord_Meta::instance(__CLASS__);}	
    static function __define(){return array('table_name'=>'system_tree');}

    /**
     * @desc		判断给定主键是否是系统主键
     * @param 		int		$uuid		数字字符串，长度为19位宽，左边第一位不能为0
     * @return		boolean				是否满足条件， 为0， 或者 全为数字并且长度为19位且左边第一位不能为0
     */
    private static function pkp ( $uuid ) { return $uuid==0 || ( strlen($uuid)==19 && is_numeric($uuid) ); }    
    
    /**
     * @desc		取所给定主键的下一个同级数据项的主键
     * @param		int		$uuid		树状结构主键
     * @return		mixed				数据项主键。若给定主键是子类所在级别的最大同类子类主键，返回NULL
     */
    private static function next ( $uuid ) {
    	$return = NULL;
    	if ( !self::pkp($uuid) ) {} elseif ( $uuid==0 ) { 
    		defined('LEGAL') OR Helper_Legaldef::def();
    		$return = SYSTREE_ORIGINAL;
    	} else {
    		$info = self::pkinfo($uuid);
    		if ( isset($info['next']) ) {
    			foreach ( $info['parts'] as $k => $v ) {
    				$return .= ($k==$info['deep'] ? $info['next'] : $v );
    			}
    		}
    	}
    	return $return;
    }    
    
    /**
     * @desc		取所给定主键的第一个直接子类的主键
     * @param		int		$uuid		给定的树状结构的主键
     * @return		int 				具有层次结构的主键
     */
    private static function first ( $uuid ) {
    	if ( !self::pkp($uuid) ) return NULL;
    	defined('LEGAL') OR Helper_Legaldef::def();
    	if ( $uuid==0 ) return SYSTREE_ORIGINAL;
    	$info = self::pkinfo($uuid); $return = '';
    	foreach ( $info['parts'] as $k => $part ) $return .= ($k==$info['deep']+1 ? str_pad('1', strlen($part), '0', STR_PAD_LEFT) : $part);
    	return $return;
    }

    /**
     * @desc		获取指定主键可用的第一个直接子类主键
     * @param 		$uuid		将作为父类的直接主键
     * @return		可用的直接
     */
    private static function available ( $uuid ) {
    	if ( !self::pkp($uuid) ) { return NULL;}
    	$available = self::first($uuid);
    	$last  = self::last($uuid);
    	while ( $available<=$last ) { 
    		if ( $available==NULL ) {
    			break;
    		} elseif ( ''==self::pkv($available) ) {
    			return $available; 
    		} else {
    			$available = self::next($available);
    		}
    	}
    	return NULL;
    }

    /**
     * @desc		取所给定主键的最后一个直接子类的主键
     * @param		int		$uuid		给定的树状结构的主键
     * @return		int 				具有层次结构的主键
     */
    private static function last ( $uuid ) {
    	$return = NULL;
    	if ( !self::pkp($uuid) ) {} elseif ( $uuid==0 ) {
    		foreach ( self::$block as $k => $w ) $return .= (($k==0) ? self::deepmax($k) : str_pad('', $w, '0'));
    	} else {
    		$info = self::pkinfo($uuid);
    		foreach ( $info['parts'] as $k => $part ) $return .= ($k==$info['deep']+1 ? self::deepmax($k) : $part);
    	}
    	return $return;
    }
    
    /**
     * @desc		取某一级别的最大子类数量
     * @param		$deep	级别深度
     * @throws				给定无效参数时，抛出异常，给定的不是无符号正整数或深度超出最大深度
     * @return		给定深度级别的最大子类数量
     */
    private static function deepmax ( $deep ) {
    	$deep = intval($deep);
    	
    	if ( $deep<0 ) throw new Exception('NONE-UNSIGNAL-INTEGER');
    	if ( $deep>count(self::$block)-1 ) throw new Exception('OVER-FLOW');
    	
    	return pow(10, self::$block[$deep])-1;
    }
    
    /**
     * @desc		取某一级别的最大子类数量
     * @param		$deep	级别深度
     * @throws				给定无效参数时，抛出异常，给定的不是无符号正整数或深度超出最大深度
     * @return		给定深度级别的最大子类数量
     */
    private static function deepmin ( $deep ) {
    	$deep = intval($deep);
    	 
    	if ( $deep<0 ) throw new Exception('NONE-UNSIGNAL-INTEGER');
    	if ( $deep>count(self::$block)-1 ) throw new Exception('OVER-FLOW');
    	 
    	return str_pad('1', self::$block[$deep], '0', STR_PAD_LEFT );
    }    
    
    /**
     * @desc	对给定主键进行解析
     * @param	int		$primary		19位长度的正整数主键
     * @return	array					deep,深度信息，parts，各部分的值，next，同级的下一个节点的有效ID					
     */
    private static function pkinfo ( $primary ) {
    	if ( !self::pkp($primary) || intval($primary)==0 ) return NULL;

    	$return = array();
    	$parts  = array();
    	$index  = 0;
    	$deep   = 0;
    	$next   = 0;
    		
    	foreach ( self::$block as $k => $v ) {
    		$part  = substr($primary, $index, $v);
    			
    		if ( intval($part)>0 ) {
    			$pmax = self::deepmax($k);
    			$deep = $k;
    			$last = $part;
    			$next = str_pad($last+1, $v, '0', STR_PAD_LEFT);
    			if ( $next>$pmax ) $next = NULL;
    		}
    			
    		$parts[] = $part;
    		$index = $index + $v;
    	}
    		
    	$return['parts'] = $parts;
    	$return['deep']  = $deep;
    	$return['next']  = $next;
    	
    	return $return;
    }
    
    /**
     * @desc		取指定主键所确定的纪录的子类（包含自身）主键的范围
     * @param		int		$uuid		指定的主键
     * @return		hash				assoc－array形式的数组， min=>（包含自身的）子类的主键的最小值 max=>（包含自身的）子类的主键的最大值
     */
    public static function range ( $uuid ) { return ( !self::pkp($uuid)||$uuid==0 ) ? array('min'=>0, 'max'=>0) : array('min'=>$uuid, 'max'=>self::next($uuid)); }

    /**
     * @desc		按照树状结构的深度返回节点块状结构的背景颜色
     * @param		int		$deep		正整数代表的树状结构的深度
     * @return 		string				＃rbg颜色值
     */
    private static function color ( $deep ) {
    	$max = 15;
    	$c = ( $max - intval($deep) );
    	switch ( $c ) {
    		case 15 : { return '#fff'; break; }
    		case 14 : { return '#eee'; break; }
    		case 13 : { return '#ddd'; break; }
    		case 12 : { return '#ccc'; break; }
    		case 11 : { return '#bbb'; break; }
    		case 10 : { return '#aaa'; break; }
    		case 9  :
    		case 8  :
    		case 7  :
    		case 6  :
    		case 5  : {
    			return "#{$c}{$c}{$c}";
    			break;
    		}
    		default : {
    			return '#555';
    				break;
    		}
    	}
    }    
    
    /**
     * @desc		给定主键的直接子类主键列表
     * @param		int		$uuid		给定的主键
     * @return		array				给定主键直接子类主键列表
     */
	public static function direct ( $primary ) { 
		$return = array();
		foreach ( self::meta()->find('`puuid`=?', $primary)->order('`uuid`')->asArray()->getAll() as $k => $v ) $return[] = $v['uuid'];
		return $return;
	}

	/**
	 * @desc		给定主键的所有子类主键列表
	 * @param		int		$uuid		给定的主键
	 * @param		boolean	$inc		子类是否包含自己
	 * @return		array				给定主键所有子类主键列表
	 */	
	public static function children ( $uuid, $inc=false ) {
		$return = array();
		if ( !self::pkp($uuid) ) {
			return NULL;
		} elseif ( $uuid==0 ) {
			foreach ( self::meta()->find()->asArray()->setColumns('uuid')->getAll() as $k => $v ) $return[] = $v['uuid'];
		} else {
			if ( $inc ) foreach ( self::meta()->find('`uuid`>=? AND `uuid`<?', $uuid, self::next($uuid))->asArray()->setColumns('uuid')->getAll() as $k => $v ) $return[] = $v['uuid'];
			else foreach ( self::meta()->find('`uuid`>? AND `uuid`<?', $uuid, self::next($uuid))->asArray()->setColumns('uuid')->getAll() as $k => $v ) $return[] = $v['uuid'];
		}
		
		return $return;		
	}	

	/**
	 * @desc	给定主键的字符串表述
	 * @param	int		$uuid		给定的主键
	 * @param	boolean	$full		是否是的完整的表述
	 * @return	string				给定主键的字符串表述
	 */
	public static function str ( $uuid, $full=false ) {
		if ( $full ) {
			$pathes = array();
			foreach ( self::path($uuid) as $key => $pk ) {
				$item = self::pkv($pk);
				array_push($pathes, $item['name']);
			}
			return join('.', $pathes);
		} else {
			$item = self::pkv($uuid);
			return empty($item['uuid']) ? '' : $item['name'];
		}
	}

	/**
	 * @desc	给定主键的路径信息
	 * @param	int		$uuid		给定的主键
	 * @return	array				给定主键的父类和祖先类的主键按照自上而下组成的列表
	 */	
	public static function path ( $uuid ) {
		if ( !self::pkp($uuid) ) return NULL;

		$return[] = $uuid;
		$item     = self::pkv($uuid);
		while ( !empty($item['uuid']) ) {
			$item = self::pkv($item['puuid']);
			if ( !empty($item['uuid']) ) $return[] = $item['uuid'];
			if ($item['puuid']==0) break;
		}

		return array_reverse($return);
	}
	
	/**
	 * @desc	按照数据主键， 取数据纪录
	 * @param	int		$uuid		数据主键
	 * @return	mixed				若数据代表的纪录存在， 以assoc－array的形势返回纪录， 否则， 返回NULL
	 */
	public static function pkv ( $primary ) { return self::meta()->find('`uuid`=?', $primary)->asArray()->getOne(); }
	
	/**
	 * @desc		在指定的数据主键后面插入一项纪录，插入纪录的name属性为$name
	 * @param		string	$name		插入数据的name属性值
	 * @param		int		$after		在该主键确定的后面插入，作为连续的主键
	 */
	public static function insert ( $data, $puuid=0 ) {
		if ( !self::pkp($puuid) ) throw new Exception('非法父类主键');
		
		$pinfo = self::pkinfo($puuid);
		if ( $pinfo['deep']>=count(self::$block) )  throw new Exception('指定父类为结构最底层');
		
		$uuid = self::available($puuid);
		if ( $uuid==NULL ) throw new Exception('指定主键子类已经达到最大的数量！');
		
		$deep    = isset($pinfo['deep']) ? $pinfo['deep']+1 : 0;
		$comment = empty($data['comment']) ? date('Y-m-d H:i:s') : $data['comment'];
		$record  = new self();
		foreach ( $data as $k => $v ) if ( isset($record->{$k}) ) $record->{$k} = $v;
		
		$record->uuid    = $uuid;
		$record->deep    = $deep;
		$record->puuid   = $puuid;
		$record->comment = $comment;
		
		$record->save();
		return $record->uuid;
	}
	
	/**
	 * @desc		删除指定主键的数据项和该数据项的子类数据项
	 * @param		int		$uuid		需要删除的数据项主键
	 */
	public static function delete ( $uuid ) {
		if ( !self::pkp($uuid) ) {
			return 0;
		} elseif ( $uuid==0 ) {
			return self::meta()->deleteWhere(); // 删除整张数据表
		} else {
			$next = self::next($uuid);
			if ( ''!=self::pkv($next) ) throw new Exception('指定的删除节点不是所在层级的最后一个节点！'); 
			return self::meta()->deleteWhere('`uuid`>=? AND `uuid`<? ', $uuid, self::next($uuid)); // 删除指定主键和其子类的纪录
		}
	}
	
	/**
	 * @desc 		生成树状数据结构的html代码
	 * @param		int		$puuid		从该根节点开始生成
	 * @return 		string				html代码
	 */
	public static function tree ( $puuid=0 ) {
		$return   = '';
		$node     = self::pkv($puuid);
		$children = self::direct($puuid);
		
		$bg    = self::color($node['deep']);
		$attrs = array('id'=>$puuid, 'style'=>"background-color:{$bg}");
		if ( !empty($node) ) $return .= Helper_Utility::html('p', mb_substr($node['name'], 0, 4, 'utf-8'), $attrs);
		if ( !empty($children) ) {
			$cstr = '';
			foreach ( $children as $k => $v ) $cstr .= Helper_Utility::html('li', self::tree($v));
			$attrs = array('style'=>"background-color:{$bg}", 'id'=>'children-'.$puuid);
			if ( $node['deep']>=1 ) $attrs['class'] = 'hidden-block';			
			$return .= Helper_Utility::html('ul', $cstr, $attrs);
		}
		
		return $return;
	}

	static function exam ( $data, $insert ) { return NULL; }	
	static function fit ( $input ) { return $input; }
	static function search ( $condition, $order, $desc ) { return NULL; }
}

