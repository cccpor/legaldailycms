<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?><?php echo $title; ?><?php $this->_endblock('title'); ?>
<?php $this->_block('session'); ?><?php $this->_control('session', 'user-session', array('session'=>$session)); ?><?php $this->_endblock('session'); ?>
<?php $this->_block('sysinfo'); ?><?php $this->_control('sysinfo', 'system-sysinfo'); ?><?php $this->_endblock('sysinfo'); ?>
<?php $this->_block("topnav"); ?><?php $this->_control('topnav', 'system-topnav', array('active'=>'')); ?><?php $this->_endblock("topnav"); ?>
<?php $this->_block('crumbs'); ?><?php $this->_control('crumbs', 'system-crumbs', array('link'=>$link)); ?><?php $this->_endblock('crumbs'); ?>
<?php $this->_block("pagefoot"); ?><?php Helper_Com::component('system::link::footlink'); ?><?php $this->_endblock("pagefoot"); ?>
<?php $this->_block("inccss"); ?><link rel="stylesheet" type="text/css" href="/js/jquery-ui/css/ui-lightness/jquery-ui-1.8.22.css"/><?php $this->_endblock("inccss"); ?>
<?php $this->_block("incjs"); ?>
<script type="text/javascript" src="/js/jquery-ui/js/jquery-ui-1.8.22.js"></script>
<script type="text/javascript" src="/js/comman/la.js"></script>
<?php $this->_endblock("incjs"); ?>
<?php $this->_block("script"); ?>
<script type="text/javascript">
$(function (){$('#birthday').datepicker({dateFormat:'yy-m-d',yearRange:'1930:',changeMonth:true,changeYear:true,gotoCurrent:true,showOtherMonths:true});});
</script>
<?php $this->_endblock("script"); ?>
<?php $this->_block('maincontent'); ?>
<form action="" method="post" id="user-perfect-form">
<fieldset>
<legend>完善个人信息</legend>
<dl>
	<?php if ( !empty($error) ) : ?>
	<dt>&nbsp;</dt>
	<dd><?php echo $error; ?></dd>
	<?php endif; ?>
	<dt>真实姓名：</dt>
	<dd><input type="text" name="realname" id="realname" value="<?php echo empty($init['realname']) ? "" : $init['realname']; ?>" /></dd>
	<dt>用户性别：</dt>
	<dd>
		<input type="radio" name="gender" id="genderBoy" value="0" <?php if ($init['gender']==0) echo 'checked="checked"'; ?>/>
		<label>男</label>
		<input type="radio" name="gender" id="genderGirl" value="1" <?php if ($init['gender']==1) echo 'checked="checked"'; ?>/>
		<label>女</label>
	</dd>
	<dt>电子邮件：</dt>
	<dd><input type="text" name="email" id="email" value="<?php echo empty($init['email']) ? "" : $init['email']; ?>" /></dd>
	<dt>手机号码：</dt>
	<dd><input type="text" name="mobile" id="mobile" value="<?php echo empty($init['mobile']) ? "" : $init['mobile']; ?>" /></dd>
	<dt>用户生日：</dt>
	<dd><input type="text" name="birthday" id="birthday" value="<?php echo empty($init['birthday']) ? "" : $init['birthday']; ?>" /></dd>
	<dt>所在地区：</dt>
	<dd><input type="text" name="regionName" onfocus="__LA.c('R', 'region', 'regionName', 'user-perfect-form')" id="regionName" value="<?php echo empty($init['regionName']) ? "" : $init['regionName']; ?>"/></dd>
	<dt>&nbsp;</dt>
	<dd>
		<input type="hidden" name="region" id="region" value="<?php echo empty($init['region'])?'':$init['region']; ?>"/>
		<input type="hidden" name="uid" id="user-uid" value="<?php echo empty($init['uid'])?'':$init['uid']; ?>"/>
		<input type="submit" value="提交保存"/>
	</dd>
</dl>
</fieldset>
</form>
<?php $this->_endblock('maincontent'); ?>