<?php
class Controller_User_Account extends Controller_Abstract {
	function actionIndex () { return $this->_redirect(url('user::default/index')); }

	// 完善个人信息
	function actionPerfect () {
		$login = $this->_app->currentUser();
		$uid   = $login['uid'];
		Helper_Com::component('system::user::permission', array('uid'=>$uid, 'roles'=>array('user', 'lawyer', 'admin', 'editor')));

		$init  = array();
		$error = '';
		if ( $this->_context->requestMethod()=='POST' ) {
			$post = $this->_context->post();
			try {
				$region = $post['region'];
				$email  = $post['email'];
				if ( !empty($region) || !empty($email)) SystemUser::updateUser(array('uid'=>$uid, 'region'=>$region, 'email'=>$email));
				SystemUserinfo::updateUserinfo($post);
				return $this->_redirect(url('user::default/index'));
			} catch ( Exception $e ) { $error = $e->getMessage(); $init = $post; }
		} else {
			$user     = SystemUser::getUserByUID($uid);
			$region   = SystemRegion::getRegionByUUID($user['region']);
			$userinfo = SystemUserinfo::getUserinfoByUID($uid);

			$init = $userinfo;
			$init['region']     = $user['region'];
			$init['regionName'] = $region['name'];
		}

		$this->_view['init']  = $init;
		$this->_view['title'] = '完善用户信息';
		
		$session = $login;
		$session['message'] = $session['username'] . "/用户个人主页";
		
		$this->_view['session'] = $session;
		$this->_view['link']    = array(
			array("href"=>url("default::default/index"),  "label"=>"首页"),
			array("href"=>url("user::default/index"), "label"=>"用户个人首页"),
			array('href'=>url('user::account/perfect'), 'label'=>'完善基本信息')
		);		
	}

	//修改形象照片
	public function actionAvatar(){
		$login = $this->_app->currentUser();
		$uid   = $login['uid'];
		Helper_Com::component('system::user::permission', array('uid'=>$uid, 'roles'=>array('user', 'lawyer', 'admin', 'editor')));

		$init  = array();
		$error = '';
		if ( $this->_context->requestMethod()=='POST' ) {

			try {
				$picfile = $_FILES['avatar'];
				$type    = $picfile['type'];

				if ( $picfile['size']<=0 ) throw new Exception('上传图片不能为空！');
				if ( $picfile['size']>=1024*1024 ) throw new Exception('上传图片限定在1M以下！');
				if ( $type!='image/jpeg' && $type!='image/png' && $type=='image/gif' ) throw new Exception('请上传图片文件！');

				defined('LEGAL') OR Helper_Legaldef::def();
				$avatar = SystemFile::meta()->find('`code`=? AND `uploader`=?', FILE_AVATAR, $uid)->setColumns('uuid')->asArray()->getOne();
				$fuuid  = SystemFile::add($uid, $picfile, FILE_AVATAR);
				if ( !empty($fuuid) ) SystemFile::delete($avatar['uuid']);

				SystemUserinfo::updateUserinfo(array('avatar'=>SystemFile::getAvatar($uid), 'uid'=>$uid));
				return $this->_redirect(url('user::default/index'));
			} catch ( Exception $e ) { $error = $e->getMessage(); }
		} else {
			$init = SystemUserinfo::getUserinfoByUID($uid);
			$init['avatar'] = SystemFile::getAvatar($uid);
		}

		$this->_view['init']  = $init;
		$this->_view['error'] = $error;
		$this->_view['title'] = '修改形象图片';
		
		$session = $login;
		$session['message'] = $session['username'] . "/修改形象图片";
		
		$this->_view['session'] = $session;
		$this->_view['link']    = array(
			array("href"=>url("default::default/index"),  "label"=>"首页"),
			array("href"=>url("user::default/index"), "label"=>"用户个人首页"),
			array('href'=>url('user::account/avatar'), 'label'=>'修改形象图片')
		);		
	}

	// 修改用户名密码
	public function actionAccount () {
		$login = $this->_app->currentUser();
		$uid   = $login['uid'];
		Helper_Com::component('system::user::permission', array('uid'=>$uid, 'roles'=>array('user', 'lawyer', 'admin', 'editor')));

		$init  = array();
		$error = '';
		if ( $this->_context->requestMethod()=='POST' ) {
			$post = $this->_context->post();
			$post['uid'] = $uid;
			// update `legal_system_user` set `salt` = 'HEGK9MStTxuOZHOl', `password` = '8acb4e6723ae43c67e19a13e4f304e3c';
			try {
				if ( !empty($post['password']) && $post['password']!=$post['repassword'] ) throw new Exception('两次输入密码不一致！');
				if ( !empty($post['username']) || !empty($post['password']) ) {
					$result = SystemUser::updateLoginInfo($post);
					if ( !$result ) throw new Exception('修改用户名或密码失败！');
					$this->_app->cleanCurrentUser();
					return $this->_redirect(url('user::default/login'));
				} else return $this->_redirect(url('user::default/index'));
			} catch ( Exception $e ) { $error = $e->getMessage(); $init = $post; }
		} else {
			$init = SystemUser::getUserByUID($uid);
		}

		$this->_view['init']  = $init;
		$this->_view['error'] = $error;
		$this->_view['title'] = '修改登陆信息';
		
		$session = $login;
		$session['message'] = $session['username'] . "/用户个人主页";
		
		$this->_view['session'] = $session;
		$this->_view['link']    = array(
			array("href"=>url("default::default/index"),  "label"=>"首页"),
			array("href"=>url("user::default/index"), "label"=>"用户个人首页"),
			array('href'=>url('user::account/account'), 'label'=>'修改登陆信息')
		);		
	}
}