<?php
class SystemFile extends QDB_ActiveRecord_Abstract {
    static function __define(){return array('behaviors'=>'fastuuid','table_name'=>'system_file','props'=>array('uuid'=>array('readonly'=>true)),'attr_protected'=>'uuid');}
    static function find() {$args = func_get_args(); return QDB_ActiveRecord_Meta::instance(__CLASS__)->findByArgs($args); }
    static function meta() {return QDB_ActiveRecord_Meta::instance(__CLASS__);}

    // added by zhonglei @ 20130927 . true, compress the uploaded image, else DO NOT compress the uploaded file
    // static function store 
    // static function add 
    private static $_compressp = true; 
    
    /**
     * @desc 	返回对codes字段的解读信息数组
     * @return	array		对code字段的解读信息数组
     */
    static function codes () {
    	return array(
    		'0' => '系统未定义类型',
    		'1' => '系统用户的头像',
    		'2' => '律师职业证扫描件',
    		'3' => '审理机构裁判文书扫描件',
    		'4' => '成功案例相关文件'
    	);
    }

    /**
     * @desc	添加文件上传信息
     * @param	hash	$data		文件信息， 以assoc-array形式提供
     * @throws 	Exception			所必须信息未提供时抛出异常
     */
    static function add ( $uploader, $ifile, $code=0, $data=array() ) {
        if ( empty($uploader) || (int)$uploader<=0 || $ifile['size']<=0 ) throw new Exception('无效数据信息！');

        $pathinfo = pathinfo($ifile['name']);
        $extname  = empty($pathinfo['extension']) ? '' : '.'.$pathinfo['extension'];

        $storeDirectory = "data/" . date("Y");
        if ( !file_exists($storeDirectory) ) mkdir($storeDirectory);
        chmod($storeDirectory, 0777);
        $storeDirectory .= "/" . date("m");
        if ( !file_exists($storeDirectory) ) mkdir($storeDirectory);
        chmod($storeDirectory, 0777);
        $storeDirectory .= "/" . date("d");
        if ( !file_exists($storeDirectory) ) mkdir($storeDirectory);
        chmod($storeDirectory, 0777);

        $file = new self();
        $name = empty($data['name']) ? $pathinfo['filename'] : $data['name'];
        $file->path     = $storeDirectory;
        $file->code     = abs((int)$code);
        $file->type     = $ifile['type'];
        $file->size     = $ifile['size'];
        $file->name     = mb_substr($name, 0, 15, 'utf-8');
        $file->related  = empty($data['related']) ? 0                     : $data['related'];
        $file->comment  = empty($data['comment']) ? $pathinfo['basename'] : $data['comment'];
        $file->create   = date("Y-m-d h:i:s");
        $file->uploader = $uploader;

        $file->save();
        $uuid = $file->uuid;
        $path = $storeDirectory . '/' . $uuid  . $extname;

        try {
        	if ( $data['touchp'] ) self::$_compressp = false;
        	self::store($ifile['tmp_name'], $path);
        	if ( $data['touchp'] ) self::$_compressp = true;
        	self::update(array('path'=>'/'.$path), $uuid);
        	return $uuid;
        } catch ( Exception $e ) {
        	self::delete($uuid);
        	return 0;
        }
    }

    /**
     * @desc	对律师信息的更新
     * @param	hash	$data		以assoc-array形式提供的文件信息数据
     * @param	int		$uuid		需要更新的数据项的主键
     * @return	boolean				操作是否成功
     */
    static function update ( $data, $uuid ) {
    	if ( empty($uuid) || (int)$uuid<=0 ) return false;
    	self::meta()->updateDbWhere($data, '`uuid`=?', $uuid);
    	return true;
    }

    /**
     * @desc	删除指定主键的文件记录
     * @param	$uuid		需要删除的数据主键
     * @return 	boolean		操作是否成功
     */
    static function delete ( $uuid ) {
    	if ( (int)$uuid<=0 ) return false;
    	$file = self::meta()->find('`uuid`=?', $uuid)->asArray()->getOne();
    	if ( !empty($file) ) {
    		$path = mb_substr($file['path'], 1, null, 'utf-8');
    		unlink($path);
    		self::meta()->deleteWhere('`uuid`=?', $uuid);
    	}
    }

    /**
     * @desc	保存上传的文件
     * @param	$file	array	来自$_FILES超级数组的信息
     * @return			array	文件保存后的信息
     */
    static function store ( $temp, $storeName ) {
    	move_uploaded_file ( $temp, $storeName ); 
    	if ( self::$_compressp ) try { // 如果上传的是图片文件， 对图片进行压缩， 如果不是图片
    		$img   = new Helper_Img($storeName);
    		$cname = $img->cmpss();

    		if ( file_exists($cname) ) {
    			unlink($storeName);
    			rename($cname, $storeName);
    		}
    	} catch ( Exception $e ) {}

    	chmod($storeName, 0777);
    	return true;
    }

    // 获取用户头像
    // @param 	boolean 	是否返回完整的记录
    static function getAvatar ( $uid, $full=false ) { defined('LEGAL') OR Helper_Legaldef::def();
    	SystemUser::__define();
    	$file = self::meta()->find('`code`=? AND `uploader`=?', FILE_AVATAR, $uid)->setColumns($full?'*':'path')->order('`create` DESC')->asArray()->getOne();
    	return ( empty($file['path']) ? ($full?array():LEGAL_USER_AVATAR) : ($full?$file:$file['path']));
    }

    /**
     * @desc	系统允许上传的文件类型的后缀名， 不含点"."
     * @return	array		系统允许上传的文件的后缀名列表（大写， 不包含前导dot符号）
     */
    public static function allowedTypeExtensionArray () { return array("DOC", "DOCX", "PPT", "PPTX", "XLS", "XLSX", "PNG", "JPG", "JPEG", "BMP", "GIF", "TXT", "PDF", "ZIP", "BZ2", "TAR", "RAR", "GZ"); }
}