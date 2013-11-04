<?php
class Controller_System_Default extends Controller_Abstract {
	function actionIndex () {
		$page  = $this->_context->query('page', 1);
		$ns    = $this->_context->query('ns',   NULL);
		$kw    = $this->_context->query('kw',   NULL);
		
		$cond = array();
		if ( !is_null($kw) ) $cond['kw'] = $kw;
		if ( !is_null($ns) ) $cond['ns'] = $ns;
		$uuids = SystemService::search($cond);
		
		$paginfo = Helper_Utility::pagination($uuids, $page);
		
		$this->_view['base']       = url('system::default/index', array('f'=>'b'));
		$this->_view['list']       = $paginfo['list'];
		$this->_view['title']      = '系统服务列表';
		$this->_view['pagination'] = $paginfo['pagination'];
	}
	
	function actionService () {
		$loginer = $this->_app->currentUser();
		$loguid  = $loginer['uid'];
		
		$init  = array();
		$error = '';
		
		if ( $this->_context->requestMethod()=='POST' ) {
			$post = $this->_context->post();
			$init = $post;
			if ( empty($post['uuid']) ) {
				$uuid = SystemAudit::create($post, 'SystemService', $loguid);
			} else {
				$uuid = $post['uuid'];
				unset($post['uuid']);
				SystemAudit::update($post, 'SystemService', $uuid, $loguid);
			}
			return $this->_redirect(url('System::default/service', array('uuid'=>$uuid)), 0);
		} else {
			$uuid = $this->_context->get('uuid', NULL);
			if ( !empty($uuid) ) $init = SystemService::pkv($uuid);
		}
		
		$this->_view['init']  = $init;
		$this->_view['error'] = $error;
		$this->_view['title'] = '系统服务表单';
	}
	
	function actionDelete () {
		$loginer = $this->_app->currentUser();
		
		$uuid = $this->_context->query('uuid', 0);
		SystemAudit::delete('SystemService', $uuid, $loginer['uid']);
		return $this->_redirectMessage('提示', '指定数据纪录删除成功', url('system::default/index'));		
	}
}