<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?><?php echo $title; ?><?php $this->_endblock('title'); ?>
<?php $this->_block('session'); ?><?php $this->_control('session', 'user-session', array('session'=>$session)); ?><?php $this->_endblock('session'); ?>
<?php $this->_block('sysinfo'); ?><?php $this->_control('sysinfo', 'system-sysinfo'); ?><?php $this->_endblock('sysinfo'); ?>
<?php $this->_block("topnav"); ?><?php $this->_control('topnav', 'system-topnav', array('active'=>'')); ?><?php $this->_endblock("topnav"); ?>
<?php $this->_block('crumbs'); ?><?php $this->_control('crumbs', 'system-crumbs', array('link'=>$link)); ?><?php $this->_endblock('crumbs'); ?>
<?php $this->_block("pagefoot"); ?><?php Helper_Com::component('system::link::footlink'); ?><?php $this->_endblock("pagefoot"); ?>
<?php $this->_block('script'); ?><script type="text/javascript"></script><?php $this->_endblock('script'); ?>
<?php $this->_block("style");?><style type="text/css">
#message-content-wrapper { background-color:white; clear:both; padding:20px; }
#message-content-wrapper>dt { float:left; text-align:right; width:100px; }
#message-content-wrapper>dd { text-align:left; margin-bottom:10px; }
#message-content-wrapper>dd>span {}
#message-content-wrapper>dd>div { margin-left:50px; }
#message-content-wrapper>dd>a { padding:3px 15px; background-color:#ccc; color:white; font-weight:bold; font-size:15px; }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block("maincontent"); ?>
<div id="message-body-wrapper">
	<ul class="link-list-wrapper">
		<li><a href="<?php echo url("message::default/index");?>">收件箱</a></li>
		<li><a href="<?php echo url("message::default/outbox"); ?>">发件箱</a></li>
		<li class="active">信件详情</li>
		<li>写站内信</li>					
	</ul>
	<dl id="message-content-wrapper">
		<dt>消息主题：</dd>
		<dd><span><?php echo $message['title']; ?></span></dd>
		<dt>发件用户：</dt>
		<dd><span><?php echo $message['otherName']; ?></span></dd>
		<dt>消息主体：</dt>
		<dd><div><?php echo $message['content']; ?></div></dd>
		<?php if ($message['receiver']==$message['uid']): ?>
		<dt>&nbsp;</dt>
		<dd>
			<a href="<?php echo url('message::default/message', array('receiver'=>$message['other'])); ?>" >回复对方</a>
		</dd>
		<?php endif; ?>
	</dl>
</div>
<?php $this->_endblock('maincontent'); ?>