<?php
class Controller_System_Database extends Controller_Abstract {
	function actionIndex () { // 使用给定的数据向给定类型的model中增加一条数据纪录
		$append = $this->_context->get('append');
		$login  = $this->_app->currentUser();
		$crypt  = new Helper_Crypt($login['salt']);
		$info   = $crypter->crypt($append, true);
		$url    = empty($info['url']) ? '/' : urldecode($info['url']);
		
		if ( !empty($info['class']) && $this->_context->requestMethod()=='POST' ) {
			$post = $this->_context->post();
			if ( !empty($info['class']) ) {
				switch ( strtoupper($info['op']) ) {
					case 'INSERT' : 
					case 'CREATE' : { // 将表单数据记入指定数据表
						break;
					}
					case 'UPDATE' : 
					case 'MODIFY' : { // 从指定数据表中更新一条数据纪录
						break;
					}
					case 'DELETE' :
					case 'ROMOVE' : { // 从指定数据表中删除一条数据记录
						
					}
				}
				foreach ( $info as $k => $v ) if ( !isset($post[$k]) ) $post[$k] = $v;
				SystemAudit::create($post, $info['class'], $loginer['uid']);
			}
		}
		
		return $this->_redirectMessage('提示', '对数据处理完毕', $url);
	}
}