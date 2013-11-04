<form action="" method="post" enctype="multipart/form-data">
<fieldset>
<legend>用户基本信息表单</legend>
<dl>
	<?php if(!empty($error)): ?>
	<dt>&nbsp;</dt><dd><?php echo $error; ?></dd>
	<?php endif; ?>
	<dt>用户名称：</dt>
	<dd><input type="text" name="username" id="username" value="<?php echo empty($init['username'])?'':$init['username']; ?>"/></dd>
	<dt>注册邮箱：</dt>
	<dd><input type="text" name="email" id="register-email" value="<?php echo empty($init['email'])?'':$init['email']; ?>"/></dd>
	<dt>登陆密码：</dt>
	<dd><input type="password" name="password" id="password"/></dd>
	<dt>确认密码：</dt>
	<dd><input type="password" name="repassword" id="repassword"/></dd>
	<dt>&nbsp;</dt>
	<dd>
		<input type="hidden" name="uid" id="user-uid" value="<?php echo empty($init['uid'])?'':$init['uid']; ?>"/>
		<input type="submit" name="submit" id="submit" value="提交保存"/>
	</dd>
</dl>
</fieldset>
</form>