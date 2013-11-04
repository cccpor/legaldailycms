<?php
defined('DS')     OR define('DS', DIRECTORY_SEPARATOR);  // 路径分隔符
defined('LEGAL')  OR define('LEGAL', true);              // 系统静态变量定义 

defined('CMEMD')    OR define('CMEMD',   'Memcache'); // memcached 缓存服务名称
defined('CREDIS')   OR define('CREDIS',  'Redis');    // redis 缓存服务名称
defined ('MAXCMEM') OR define('MAXCMEM', 35318);      // memcached 所能缓存的最大的64位数字元素数组的最大长度

defined('STR_FORMAT_T')      OR define('STR_FORMAT_T',      0); // 字符串格式：非空
defined('STR_FORMAT_CN')     OR define('STR_FORMAT_CN',     1); // 字符串格式：全中文字符
defined('STR_FORMAT_NO')     OR define('STR_FORMAT_NO',     5); // 字符串格式：全数字
defined('STR_FORMAT_CNNO')   OR define('STR_FORMAT_CNNO',   2); // 字符串格式：仅限中文和数字 
defined('STR_FORMAT_ENNO')   OR define('STR_FORMAT_ENNO',   4); // 字符串格式：仅限英文字母和数字
defined('STR_FORMAT_CNNOEN') OR define('STR_FORMAT_CNNOEN', 3); // 字符串格式：仅限中文，英文字母和数字

defined('SYSTREE_ORIGINAL') OR define('SYSTREE_ORIGINAL',  1000000000000000000); // 系统树状数据结构初始化值
defined('LEGAL_REGION')     OR define('LEGAL_REGION',      1000000000000000000); // 系统树状数据结构：地域信息根节点
defined('LEGAL_GROUP')      OR define('LEGAL_GROUP',       3000000000000000000); // 系统群组根代码
defined('LEGAL_ROBOT')      OR define('LEGAL_ROBOT',       1000000000000000000); // 系统用户代码， 系统采集机器人
defined('LEGAL_CHINA')      OR define('LEGAL_CHINA',       1010100000000000000); // 系统树状数据结构：地域信息，中国国家代码
defined('LEGAL_CATE')       OR define('LEGAL_CATE',        2000000000000000000); // 系统树状数据结构：分类信息，案由根节点

defined('LEGAL_INVALID')       OR define('LEGAL_INVALID',     -1);                  // 系统无效的数字，状态，返回值
defined('LEGAL_UNVALIDATED')   OR define('LEGAL_UNVALIDATED', 0);                   // 系统内未经验证的数据状态

class Helper_Legaldef { public static function def () {}	}
// defined('LEGAL') OR Helper_Legaldef::def();