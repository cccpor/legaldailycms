<?php
class Controller_System_User extends Controller_Abstract {
	function actionIndex () {
		$cond = array();
		$uids = SystemUser::search($cond);
		
		$this->_view['list']  = $uids;
		$this->_view['title'] = '系统户用列表';
	}
	
	function actionForm () {
		$loginer = $this->_app->currentUser();
		
		$init  = array();
		$error = '';
		if ( $this->_context->requestMethod()=='POST' ) { // handle form post data 
			$post = $this->_context->post();
			$user = SystemUser::pkv($post['uid']);
			$uid  = $post['uid'];
			
			if ( ''!=$user ) {
				if ( !empty($post['password']) && $post['password']==$post['repasswd'] ) { 
					$password = SystemUser::changepw($uid, $post['password']);
				} 
				
				unset($post['password']);
				$init = $post;
				SystemAudit::update($post, 'SystemUser', $uid, $loginer['uid']);
			} else {
				if ( empty($post['password']) || empty($post['repasswd']) || $post['password']!=$post['repasswd'] ) throw new Exception('用户登录密码不能为空切确认密码和输入密码必须匹配！');
				
				$password = $post['password'];
				$repasswd = $post['repasswd'];
				
				SystemUser::add($post);
				return $this->_redirectMessage('提示', '添加用户成功！', url('system::user/index'), 5);				
			}
		}
		
		$uid  = $this->_context->get('uid', '');
		$user = SystemUser::pkv($uid);
		
		$init = empty($user) ? array() : $user;
		if ( !isset($init['group'])  ) $init['group']  = LEGAL_GROUP;
		if ( !isset($init['region']) ) $init['region'] = LEGAL_CHINA;
		
		$init['user-group']  = SystemTree::str($init['group']);
		$init['user-region'] = SystemTree::str($init['region']);
		
		$this->_view['init']  = $init;
		$this->_view['error'] = $error;
		$this->_view['title'] = '系统用户表单';
	}
}