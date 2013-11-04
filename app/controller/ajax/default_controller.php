<?php
class Controller_Ajax_Default extends Controller_Abstract {
	function actionIndex () {
		defined('LEGAL') OR Helper_Legaldef::def();
		$uuid = $this->_context->get('uuid', 0);
		$page = $this->_context->get('page', 1);
		
		$path = $direct = array();
		foreach ( SystemTree::path($uuid)   as $key => $value ) $path[]   = SystemTree::pkv($value);
		foreach ( SystemTree::direct($uuid) as $key => $value ) $direct[] = SystemTree::pkv($value);
		
		$paginfo = Helper_Utility::pagination($direct, $page, 20);
		$data = array(
			'uuid'   => $uuid,
			'page'   => $page,
			'path'   => $path,
			'direct' => $paginfo['list']
		);
		
		die(Helper_Com::component('system::ajax::select', $data));
	}
	
	function actionTree () {
		$uuid     = $this->_context->query('uuid', 0);
		$type     = $this->_context->query('type', 'region');
		
		defined('LEGAL') OR Helper_Legaldef::def();
		if ( $uuid==0 ) $uuid = strtoupper($type)=='REGION' ? CHINA : 0;
		
		$pathes   = strtoupper($type)=='REGION' ? SystemRegion::fullPath($uuid) : CategoryCase::fullPath($uuid);
		$children = strtoupper($type)=='REGION' ? SystemRegion::getDirectChildren($uuid) : CategoryCase::getDirectChildren($uuid);
		
		$return = array('error'=>0, 'message'=>'OK', 'path'=>array(), 'children'=>array());
		
		foreach ( $pathes as $pk => $path ) {
			$item = strtoupper($type)=='REGION' ? SystemRegion::getRegionByUUID($path) : CategoryCase::getCategoryByUUID($path);
			$return['path'][] = array('uuid'=>$item['uuid'],'name'=>$item['name']);
		}
		
		foreach ( $children as $ck => $child ) {
			$return['children'][] = array('uuid'=>$child['uuid'], 'name'=>$child['name']);
		}
		
		die(json_encode($return));
	}
}