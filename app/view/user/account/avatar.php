<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?><?php echo $title; ?><?php $this->_endblock('title'); ?>
<?php $this->_block('session'); ?><?php $this->_control('session', 'user-session', array('session'=>$session)); ?><?php $this->_endblock('session'); ?>
<?php $this->_block('sysinfo'); ?><?php $this->_control('sysinfo', 'system-sysinfo'); ?><?php $this->_endblock('sysinfo'); ?>
<?php $this->_block("topnav"); ?><?php $this->_control('topnav', 'system-topnav', array('active'=>'')); ?><?php $this->_endblock("topnav"); ?>
<?php $this->_block('crumbs'); ?><?php $this->_control('crumbs', 'system-crumbs', array('link'=>$link)); ?><?php $this->_endblock('crumbs'); ?>
<?php $this->_block("pagefoot"); ?><?php Helper_Com::component('system::link::footlink'); ?><?php $this->_endblock("pagefoot"); ?>
<?php $this->_block("script"); ?><script type="text/javascript"></script><?php $this->_endblock("script"); ?>
<?php $this->_block('maincontent'); ?>
<form action="" enctype="multipart/form-data" method="post">
<fieldset>
<legend>更改形象图片</legend>
<dl>
	<?php if ( !empty($error) ) : ?>
	<dt>&nbsp;</dt>
	<dd><?php echo $error; ?></dd>
	<?php endif; ?>
	<dt>现有头像：</dt>
	<dd><img src="<?php echo $init['avatar']; ?>" alt="user-avatar"/></dd>
	<dt>用户头像：</dt>
	<dd><input type="file" name="avatar" id="avatar" value=""/></dd>
	<dt>&nbsp;</dt>
	<dd>
		<input type="hidden" name="uid" id="user-uid" value="<?php echo empty($init['uid'])?'':$init['uid']; ?>"/>
		<input type="submit" value="提交保存"/>
	</dd>
</dl>
</fieldset>
</form>
<?php $this->_endblock('maincontent'); ?>