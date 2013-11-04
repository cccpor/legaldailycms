<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?><?php echo $title; ?><?php $this->_endblock('title'); ?>
<?php $this->_block('session'); ?><?php $this->_control('session', 'user-session', array('session'=>$session)); ?><?php $this->_endblock('session'); ?>
<?php $this->_block('sysinfo'); ?><?php $this->_control('sysinfo', 'system-sysinfo'); ?><?php $this->_endblock('sysinfo'); ?>
<?php $this->_block("topnav"); ?><?php $this->_control('topnav', 'system-topnav', array('active'=>'')); ?><?php $this->_endblock("topnav"); ?>
<?php $this->_block('crumbs'); ?><?php $this->_control('crumbs', 'system-crumbs', array('link'=>$link)); ?><?php $this->_endblock('crumbs'); ?>
<?php $this->_block("pagefoot"); ?><?php Helper_Com::component('system::link::footlink'); ?><?php $this->_endblock("pagefoot"); ?>
<?php $this->_block('script'); ?><script type="text/javascript"></script><?php $this->_endblock('script'); ?>
<?php $this->_block("style");?><style type="text/css">
form { background-color:white; clear:both; padding-top:10px; }
form>dl {  clear:both; }
.link-list-wrapper { }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<div id="message-body-wrapper">
	<ul class="link-list-wrapper">
		<li><a href="<?php echo url("message::default/index");?>">收件箱</a></li>
		<li><a href="<?php echo url("message::default/outbox"); ?>">发件箱</a></li>
		<li>信件详情</li>
		<li class="active" style="border-right:none;">写站内信</li>					
	</ul>
	<form name="message-form" id="message-form" method="post" action="">
	<dl>
		<?php if (!empty($errorMessage)): ?>
		<dt>&nbsp;</dt>
		<dd><?php echo $errorMessage; ?></dd>
		<?php endif; ?>
		<dt>收件用户：</dt>
		<dd><?php echo $receiverName; ?></dd>
		<dt>信件主题：</dt>
		<dd><input type="text" name="title" id="title" value="" /></dd>
		<dt>信件内容：</dt>
		<dd><textarea name="content" id="content" rows="10" cols="60" ></textarea></dd>
		<dt>&nbsp;</dt>
		<dd>
			<input type="hidden" name="receiver" id="receiver" value="<?php echo $receiver; ?>"/>
			<input type="submit" name="submit" value="提交发送" />
		</dd>
	</dl>
	<div style="height:20px;">&nbsp;</div>
	</form>
</div>
<?php $this->_endblock('maincontent'); ?>