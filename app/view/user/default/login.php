<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?><?php echo $itle; ?><?php $this->_endblock('title'); ?>
<?php $this->_block("style");?><style type="text/css">
#login-form { margin-top:100px; }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<form action="" id="login-form" method="post" enctype="multipart/form-data">
<fieldset><legend>系统登录表单</legend>
<dl>
	<?php if(!empty($error)): ?><dt>&nbsp;</dt><dd><?php echo $error; ?></dd><?php endif; ?>
	<dt>登陆名称：</dt>
	<dd><input type="text" name="username" id="username" value="<?php echo empty($init['username']) ? "" : $init['username']; ?>" /></dd>
	<dt>登陆密码：</dt>
	<dd><input type="password" name="password" id="password"/></dd>
	<dt>&nbsp;</dt>
	<dd><input type="submit" name="submit" value="登陆"/></dd>							
</dl>
</fieldset>
</form>
<?php $this->_endblock('maincontent'); ?>