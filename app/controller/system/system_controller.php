<?php
class Controller_System_System extends Controller_Abstract {
	function actionIndex () { return $this->_redirect(url('system::default/index')); }
	
	function actionACL () {
		$loginer = $this->_app->currentUser();
		$loguid  = $loginer['uid'];
		
		if ( $this->_context->requestMethod()=='POST' ) { // do acl change 
			$post  = $this->_context->post();
			$group = $post['group'];
			
			if ( ''==SystemTree::pkv($group) ) return $this->_redirectMessage('提示', '指定的群组不存在', url('system::system/acl'));
			
			SystemACL::meta()->deleteWhere('`group`=?', $group); // 删除群组的权限
			foreach ( split(',', $post['permissions']) as $k => $v ) { // 重新给群组分配指定的权限
				if ( empty($v) ) continue;
				SystemAudit::create(array('group'=>$group, 'service'=>$v), 'SystemACL', $loguid);
			}
			// KNOCK KNOCK
			return $this->_redirectMessage('提示', '指定群组权限配置成功！', url('system::system/acl', array('group'=>$group)), 5);
		} else {
			$guuid = $this->_context->query('group', '');
			$group = SystemTree::pkv($guuid);
			
			$serivces = SystemService::search();
			$granded  = SystemACL::groupPerms($guuid);
			$granding = array_diff($serivces, empty($granded)?array():$granded);			
		}
		
		defined('LEGAL') OR Helper_Legaldef::def();
		$this->_view['grandings'] = $granding;
		$this->_view['grandeds']  = $granded;
		$this->_view['group']     = $group;
		$this->_view['path']      = SystemTree::path($guuid);
		$this->_view['gr']        = LEGAL_GROUP;
		$this->_view['title']     = '系统权限控制设置！';
	}
	
	function actionTree () {
		$puuid = $this->_context->query('uuid', 0);
		$error = '';
		
		if ( $this->_context->requestMethod()=='POST' ) {
			$post   = $this->_context->post();
			$action = $post['action'];
			
			$puuid = $post['puuid'];
			$cuuid = $post['cuuid'];
			$uuid  = $post['uuid'];
			
			$pname = $post['pname'];
			$cname = $post['cname'];
			$name  = $post['name'];
			
			try {
				switch ( strtoupper($action) ) {
					case 'MOD' : { // 对选中的节点进行更新
						SystemTree::meta()->updateDbWhere(array('name'=>$name), '`uuid`=?', $uuid); 
						break; 
					}
					case 'ADD' : { // 在选中的节点下增加新的节点
						if ( empty($uuid) ) $uuid = 0;
						SystemTree::insert(array('name'=>$cname), $uuid);
						break; 
					}
					case 'DEL' : { // 将选中的节点连同其所有子节点都删除掉
						SystemTree::delete($uuid);
						break; 
					}
					default    : { break; }
				}
			} catch ( Exception $e ) {
				$error = $e->getMessage();
			}
		}
		
		$this->_view['error'] = $error;
		$this->_view['puuid'] = $puuid;
		$this->_view['title'] = '系统树状结构视图';
	}
}