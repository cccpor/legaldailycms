<?php
class MyApp {
    protected $_app_config; // 应用程序的基本设置

    /**
     * @desc 	构造函数
     * @param 	array 	$app_config
     * @return 	object 	构造应用程序对象
     */
    protected function __construct(array $app_config){
        global $g_boot_time;
        @set_magic_quotes_runtime(0); // 禁止 magic quotes		 
        
        if (get_magic_quotes_gpc()) { // 处理被 magic quotes 自动转义过的数据
            $in = array(& $_GET, & $_POST, & $_COOKIE, & $_REQUEST);
            while (list ($k, $v) = each($in)) {
                foreach ($v as $key => $val) {
                    if (! is_array($val)) {
                        $in[$k][$key] = stripslashes($val);
                        continue;
                    }
                    $in[] = & $in[$k][$key];
                }
            }
            unset($in);
        }

        set_exception_handler(array($this, 'exception_handler'));// 设置异常处理函数
        $this->_app_config = $app_config; // 初始化应用程序设置
        $this->_initConfig();
        Q::replaceIni('app_config', $app_config);

        date_default_timezone_set(Q::ini('l10n_default_timezone')); // 设置默认的时区
        if (Q::ini('runtime_session_provider')) { // 设置 session 服务
            Q::loadClass(Q::ini('runtime_session_provider'));
        }

        if (Q::ini('runtime_session_start')) session_start(); // 打开 session

        Q::import($app_config['APP_DIR']); // 导入类搜索路径
        Q::import($app_config['APP_DIR'] . '/model');
        Q::import($app_config['MODULE_DIR']);
        Q::register($this, 'app'); // 注册应用程序对象
    }
    function __destruct () {}

    /**
     * @desc 	返回应用程序类的唯一实例
     * @param 	array 	$app_config
     * @return 	MyApp
     */
    static function instance(array $app_config = null) {
        static $instance;
        if (is_null($instance)) {
            if (empty($app_config)) { die('INVALID CONSTRUCT APP'); }
            $instance = new MyApp($app_config);
        }
        return $instance;
    }

    /**
     * @desc 		返回应用程序基础配置的内容, 如果没有提供 $item 参数，则返回所有配置的内容
     * @param 		string 		$item
     * @return 		mixed
     */
    function config($item = null) {
        if ($item) return isset($this->_app_config[$item]) ? $this->_app_config[$item] : null;
        return $this->_app_config;
    }

    /**
     * @desc 		根据运行时上下文对象，调用相应的控制器动作方法
     * @param 		array 		$args
     * @return 		mixed
     */
    function dispatching(array $args = array()) {
        $context = QContext::instance();
        $udi = $context->requestUDI('array');

        if (!$this->authorizedUDI($this->currentUserRoles(), $udi)) { // 检查是否有权限访问
        	$response = $this->_on_access_denied();
        } else { // 控制器类名称 = 模块名_Controller_名字空间_控制器名
            $module_name = $udi[QContext::UDI_MODULE];
            if ($module_name != QContext::UDI_DEFAULT_MODULE && $module_name) {
                $dir = "{$this->_app_config['MODULE_DIR']}/{$module_name}/controller";
                $class_name = "{$module_name}_controller_";
            } else {
                $dir = "{$this->_app_config['APP_DIR']}/controller";
                $class_name = 'controller_';
            }

            $namespace = $udi[QContext::UDI_NAMESPACE];
            if ($namespace != QContext::UDI_DEFAULT_NAMESPACE && $namespace) {
                $class_name .= "{$namespace}_";
                $dir .= "/{$namespace}";
            }
            $controller_name = $udi[QContext::UDI_CONTROLLER];
            $class_name .= $controller_name;
            $filename = "{$controller_name}_controller.php";

            do { // 载入控制器文件
                try {
                    if (!class_exists($class_name, false)) {
                        Q::loadClassFile($filename, array($dir), $class_name);
                    }
                } catch (Q_ClassNotDefinedException $ex) {
                    $response = $this->_on_action_not_defined();
                    break;
                } catch (Q_FileNotFoundException $ex) {
                    $response = $this->_on_action_not_defined();
                    break;
                }

                $controller = new $class_name($this); // 构造控制器对象
                $action_name = $udi[QContext::UDI_ACTION];
                if ($controller->existsAction($action_name)) { // 如果指定动作存在，则调用
                    $response = $controller->execute($action_name, $args);
                } else { // 如果指定动作不存在，则尝试调用控制器的 _on_action_not_defined() 函数处理错误
                    $response = $controller->_on_action_not_defined($action_name);
                    if (is_null($response)) { // 如果控制器的 _on_action_not_defined() 函数没有返回处理结果则由应用程序对象的 _on_action_not_defined() 函数处理
                        $response = $this->_on_action_not_defined();
                    }
                }
            } while (false);
        }

        if (is_object($response) && method_exists($response, 'execute')) { // 如果返回结果是一个对象，并且该对象有 execute() 方法，则调用
            $response = $response->execute();
        } elseif ($response instanceof QController_Forward) { // 如果是一个 QController_Forward 对象，则将请求进行转发
            $response = $this->dispatching($response->args);
        }
        
        return $response;
    }

    /**
     * @desc 	将用户数据保存到 session 中
     * @param 	mixed $user
     * @param 	mixed $roles
     */
    function changeCurrentUser($user, $roles) {
        $user['roles'] = implode(',', Q::normalize($roles));
        $_SESSION[Q::ini('acl_session_key')] = $user;
    }

    /**
     * @desc 	获取保存在 session 中的用户数据
     * @return 	array
     */
    function currentUser(){
        $key = Q::ini('acl_session_key');
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * @desc 	获取 session 中用户信息包含的角色
     * @return 	array
     */
    function currentUserRoles(){
        $user = $this->currentUser();
        return isset($user['roles']) ? Q::normalize($user['roles']) : array();
    }

    /**
     * @desc 	从 session 中清除用户数据
     */
    function cleanCurrentUser(){unset($_SESSION[Q::ini('acl_session_key')]);}

    /**
     * @desc 	检查指定角色是否有权限访问特定的控制器和动作
     * @param 	array 			$roles
     * @param 	string|array 	$udi
     * @return 	boolean
     */
    function authorizedUDI($roles, $udi){//将UDI封装为一个资源读取控制器的ACL(访问控制列表)通过 QACL 组件进行权限检查
        $roles = Q::normalize($roles);
        $udi = QContext::instance()->normalizeUDI($udi);
        $controller_acl = $this->controllerACL($udi);

        $acl = Q::singleton('QACL'); // 首先检查动作 ACT
        $action_name = strtolower($udi[QContext::UDI_ACTION]);
        if (isset($controller_acl['actions'][$action_name])){ // 如果动作的 ACT 检验通过，则忽略控制器的 ACT
            return $acl->rolesBasedCheck($roles, $controller_acl['actions'][$action_name]);
        }

        if (isset($controller_acl['actions'][QACL::ALL_ACTIONS])) { // 如果为所有动作指定了默认 ACT，则使用该 ACT 进行检查
            return $acl->rolesBasedCheck($roles, $controller_acl['actions'][QACL::ALL_ACTIONS]);
        }

        return $acl->rolesBasedCheck($roles, $controller_acl); // 否则检查是否可以访问指定控制器
    }

    /**
     * @desc 	获得指定控制器的 ACL
     * @param 	string|array $udi
     * @return 	array
     */
    function controllerACL($udi){
        if (!is_array($udi)){
            $udi = QContext::instance()->normalizeUDI($udi);
        }

        $path = 'acl_global';
        if ($udi[QContext::UDI_MODULE] && $udi[QContext::UDI_MODULE] != QContext::UDI_DEFAULT_MODULE){
            $path .= '/' . $udi[QContext::UDI_MODULE];
        }
        if ($udi[QContext::UDI_NAMESPACE] && $udi[QContext::UDI_NAMESPACE] != QContext::UDI_DEFAULT_NAMESPACE){
            $path .= '/' . $udi[QContext::UDI_NAMESPACE];
        }
        $acl = Q::ini($path);

        if (!is_array($acl)){
            return Q::ini('acl_default');
        }

        $acl = array_change_key_case($acl, CASE_LOWER);

        if (isset($acl[$udi[QContext::UDI_CONTROLLER]])){
            return (array)$acl[$udi[QContext::UDI_CONTROLLER]];
        }

        return isset($acl[QACL::ALL_CONTROLLERS]) ? (array)$acl[QACL::ALL_CONTROLLERS] : Q::ini('acl_default');
    }

    /**
     * @desc 	载入配置文件内容
     * @param 	array $app_config
     * @return 	array
     */
    static function loadConfigFiles(array $app_config) {
        $ext = !empty($app_config['CONFIG_FILE_EXTNAME']) ? $app_config['CONFIG_FILE_EXTNAME'] : 'yaml';
        $cfg = $app_config['CONFIG_DIR'];
        $run_mode = strtolower($app_config['RUN_MODE']);

        $files = array (
            "{$cfg}/environment.{$ext}"               => 'global',
            "{$cfg}/database.{$ext}"                  => 'db_dsn_pool',
            "{$cfg}/acl.{$ext}"                       => 'acl_global',
            "{$cfg}/environments/{$run_mode}.{$ext}"  => 'global',
            "{$cfg}/app.{$ext}"                       => 'appini',
            "{$cfg}/routes.{$ext}"                    => 'routes',
        );

        $replace = array();
        foreach ($app_config as $key => $value) {
            if (!is_array($value)) $replace["%{$key}%"] = $value;
        }

        $config = require(Q_DIR . '/_config/default_config.php');
        foreach ($files as $filename => $scope){
            if (!file_exists($filename)) continue;
            $contents = Helper_YAML::load($filename, $replace);
            if ($scope == 'global'){
                $config = array_merge($config, $contents);
            } else {
                if (!isset($config[$scope])) {
                    $config[$scope] = array();
                }
                $config[$scope] = array_merge($config[$scope], $contents);
            }
        }

        if (!empty($config['db_dsn_pool'][$run_mode])) {
            $config['db_dsn_pool']['default'] = $config['db_dsn_pool'][$run_mode];
        }

        return $config;
    }

    /**
     * @desc 	初始化应用程序设置
     */
    protected function _initConfig(){
        if ($this->_app_config['CONFIG_CACHED']){ // 载入配置文件
            $backend = $this->_app_config['CONFIG_CACHE_BACKEND']; // 构造缓存服务对象
            $settings = isset($this->_app_config['CONFIG_CACHE_SETTINGS'][$backend]) ? $this->_app_config['CONFIG_CACHE_SETTINGS'][$backend] : null;
            $cache = new $backend($settings);

            $cache_id = $this->_app_config['APPID'] . '_app_config'; // 载入缓存内容
            $config = $cache->get($cache_id);

            if (!empty($config)){
                Q::replaceIni($config);
                return;
            }
        }

        $config = self::loadConfigFiles($this->_app_config); // 没有使用缓存，或缓存数据失效
        if ($this->_app_config['CONFIG_CACHED']) $cache->set($cache_id, $config);

        Q::replaceIni($config);
    }

	/**
	 * @desc 	访问被拒绝时的错误处理函数
	 */
	protected function _on_access_denied(){
        $filename = str_replace(array('/', '\\'), '/', substr(__FILE__, strlen($this->_app_config['ROOT_DIR']) + 1));
        require($this->_app_config['APP_DIR'] . '/view/403.php');
	}

	/**
	 * @desc 	视图调用未定义的控制器或动作时的错误处理函数
	 */
	protected function _on_action_not_defined(){
        $filename = str_replace(array('/', '\\'), '/', substr(__FILE__, strlen($this->_app_config['ROOT_DIR']) + 1));
		require($this->_app_config['APP_DIR'] . '/view/404.php');
	}

	/**
	 * @desc 	默认的异常处理
	 */
	function exception_handler(Exception $ex) { QException::dump($ex); }
}

class MyAppException extends QException {}