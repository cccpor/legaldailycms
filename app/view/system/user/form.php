<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript">
function checkform(obj){ obj.form.submit(); }
</script><?php $this->_endblock("script"); ?>
<?php $this->_block("incjs"); ?><script type="text/javascript" src="/js/la.js"></script><?php $this->_endblock("incjs"); ?>
<?php $this->_block("style"); ?><style type="text/css">
</style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<h2 class="func-title"><?php echo $title; ?></h2>
<form method="post" name="user-form" id="user-form" action="">
<fieldset>
<legend>系统用户表单</legend>
<dl>
	<?php if (!empty($error)): ?>
	<dt>&nbsp;</dt><dd><?php echo $error; ?></dd>
	<?php endif; ?>
	<dt>登陆名称：</dt><dd><input type="text" name="username" id="user-username" value="<?php echo empty($init['username'])?'':$init['username']; ?>"/></dd>
	<dt>登陆密码：</dt><dd><input type="password" name="password" id="user-password" value=""/></dd>
	<dt>确认密码：</dt><dd><input type="password" name="repasswd" id="user-repasswd" value=""/></dd>
	<dt>所在地区：</dt><dd><input type="text" name="user-region" id="user-region" onfocus="__LA.c('region-uuid', 'user-region', 'user-form', '<?php echo empty($init['region'])?'':$init['region']; ?>')" value="<?php echo empty($init['user-region'])?'':$init['user-region']; ?>"/></dd>
	<dt>所属群组：</dt><dd><input type="text" name="user-group"  id="user-group" onfocus="__LA.c('group-uuid', 'user-group', 'user-form', '<?php echo empty($init['group'])?'':$init['group']; ?>')" value="<?php echo empty($init['user-group'])?'':$init['user-group']; ?>"/></dd>
	<dt>&nbsp;</dt>
	<dd>
		<input type="hidden" name="uid" id="user-uid" value="<?php echo empty($init['uid'])?'':$init['uid']; ?>"/>
		<input type="hidden" name="region" id="region-uuid" value="<?php echo empty($init['region'])?'':$init['region']; ?>"/>
		<input type="hidden" name="group"  id="group-uuid"  value="<?php echo empty($init['group']) ?'':$init['group'];  ?>"/>
		<input type="button" value="提交保存" onclick="checkform(this)"/>
	</dd>
</dl>
</fieldset>
</form>
<?php $this->_endblock("maincontent"); ?>