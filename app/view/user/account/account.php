<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block('session'); ?><?php $this->_control('session', 'judgment-session', array('session'=>$session)); ?><?php $this->_endblock('session'); ?>
<?php $this->_block('sysinfo'); ?><?php $this->_control('sysinfo', 'judgment-sysinfo'); ?><?php $this->_endblock('sysinfo'); ?>
<?php $this->_block("topnav"); ?><?php $this->_control('topnav', 'judgment-topnav', array('active'=>'index')); ?><?php $this->_endblock("topnav"); ?>
<?php $this->_block('crumbs'); ?><?php $this->_control('crumbs', 'judgment-crumbs', array('link'=>$link)); ?><?php $this->_endblock('crumbs'); ?>
<?php $this->_block("pagefoot"); ?><?php Helper_Com::component('system::link::footlink'); ?><?php $this->_endblock("pagefoot"); ?>
<?php $this->_block("style"); ?>
<style type="text/css">
#userRegiste {border:1px solid #3b86a5; width:80%; margin:0px auto; margin-bottom:13px;}
#userinfoData { padding:25px; background-color:#e5edf0;margin:5px;}
#userinfoData li {padding:3px; width:50%;  margin:0px auto; text-align:left;}
#userinfoData li label {display:inline-block; width:90px; font-weight:bold; font-size:14px;}
#userinfoData li label span {}
#titleLine {border-bottom:1px solid #91bacc;padding:5px 0px 5px 5px; font-weight:bold; font-size:16px; }
li {text-align:left;}
</style>
<?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<form action="" method="post">
<fieldset>
	<legend>重新设定登陆信息</legend>
	<dl>
		<?php if ( !empty($error) ) : ?><dt>&nbsp;</dt><dd><?php echo $error; ?></dd><?php endif; ?>
		<dt>登陆名称：</dt>
		<dd><input type="text" name="username" id="username" value="<?php echo empty($init['username'])?'':$init['username']; ?>"/></dd>
		<dt>输入密码：</dt>
		<dd><input type="password" name="password" id="password" value=""/></dd>
		<dt>确认密码：</dt>
		<dd><input type="password" name="repassword" id="repassword" value=""/></dd>
		<dt>&nbsp;</dt>
		<dd><input type="submit" name="submit" id="submit" value="提交保存"/></dd>				
	</dl>
</fieldset>
</form>
<?php $this->_endblock('maincontent'); ?>