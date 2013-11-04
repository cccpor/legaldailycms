<?php
class Controller_User_Default extends Controller_Abstract {
	// 用户个人主页
	function actionIndex () {
		$loginer = $this->_app->currentUser();
		if ( empty($loginer['uid']) ) return $this->_redirect(url("user::default/login"), 0);
		
		$this->_view['user']  = $loginer;
		$this->_view['title'] = '用户个人主页';
	}
	
	// 用户登陆
	function actionLogin () {
		$currentUser = $this->_app->currentUser();
		$uid         = $currentUser['uid'];		
		
		if ( $this->_context->requestMethod()=="POST" ) {
			$username = $this->_context->post('username', '');
			$password = $this->_context->post('password', '');
			$error    = '';
			
			try {
				if ( empty($username) || empty($password) || !SystemUser::authentication($username, $password) ) throw new Exception('用户名或密码错误！');
				
				$user = SystemUser::getByUsername($username);
				$this->_app->changeCurrentUser($user, $user['group']);
				
				SystemUser::update( array('uid'=>$uid, 'last'=>date('Y-m-d H:i:s')) );
				return $this->_redirect(url('user::default/index'), 0);
			} catch ( Exception $e ) {
				$error = $e->getMessage();
			}
			
			$this->_view['error'] = $error;
		}
		
		$this->_view['title'] = '系统用户登陆！';
	}
}