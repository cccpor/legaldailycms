<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>			
	<?php $this->_block('meta');?><?php $this->_endblock('meta');?>
	<title><?php $this->_block('title');?><?php $this->_endblock('title');?></title>
	<link rel="stylesheet" type="text/css" href="/css/basic.css"/>
	<script type="text/javascript" src="/js/jquery-ui/js/jquery-1.8.0.js"></script>
	<?php $this->_block('script');?><?php $this->_endblock('script');?>
	<?php $this->_block('style');?><?php $this->_endblock('style');?>
	<?php $this->_block('incjs');?><?php $this->_endblock('incjs');?>
	<?php $this->_block('inccss');?><?php $this->_endblock('inccss');?>
</head>
<body style="text-align:center;">
	<table id="LRWRAPPER"><tr>
	<td id="LWRAPPER"><?php $this->_block('left');?><?php $this->_endblock('left');?></td>
	<td id="RWRAPPER"><?php $this->_block('right');?><?php $this->_endblock('right');?></td>
	</tr></table>
</body>
</html>
