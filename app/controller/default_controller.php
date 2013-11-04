<?php
class Controller_Default extends Controller_Abstract {
    function actionIndex () {
    	$loginer = $this->_app->currentUser();
    	$loguid  = $loginer['uid'];
    	
    	if ( empty($loguid) ) return $this->_redirect(url('user::default/login'));
    	
    	$this->_view['title'] = '法制日报法制网内容管理系统&gt;&gt;首页';
    }
}