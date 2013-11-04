<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?><?php echo $title; ?><?php $this->_endblock('title'); ?>
<?php $this->_block('session'); ?><?php $this->_control('session', 'user-session', array('session'=>$session)); ?><?php $this->_endblock('session'); ?>
<?php $this->_block('sysinfo'); ?><?php $this->_control('sysinfo', 'system-sysinfo'); ?><?php $this->_endblock('sysinfo'); ?>
<?php $this->_block("topnav"); ?><?php $this->_control('topnav', 'system-topnav', array('active'=>'')); ?><?php $this->_endblock("topnav"); ?>
<?php $this->_block('crumbs'); ?><?php $this->_control('crumbs', 'system-crumbs', array('link'=>$link)); ?><?php $this->_endblock('crumbs'); ?>
<?php $this->_block("pagefoot"); ?><?php Helper_Com::component('system::link::footlink'); ?><?php $this->_endblock("pagefoot"); ?>
<?php $this->_block('script'); ?><script type="text/javascript"></script><?php $this->_endblock('script'); ?>
<?php $this->_block("style");?><style type="text/css"></style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<div id="message-body-wrapper">
	<ul class="link-list-wrapper">
		<li><a href="<?php echo url("message::default/index");?>">收件箱</a></li>
		<li class="active"><a href="<?php echo url("message::default/outbox"); ?>">发件箱</a></li>
		<li>信件详情</li>
		<li>写站内信</li>					
	</ul>
	<table id="message-table">
		<?php if ( !empty($messageList) ) : ?>
		<tr>
			<th>收件时间</th>
			<th>收件人</th>
			<th>信件标题</th>
			<th>操作</th>
		</tr>
		<?php foreach ( $messageList as $key => $message) : ?>
		<tr>
			<td><?php echo $message['create']; ?></td>
			<td><?php echo $message['receiverName']; ?></td>
			<td><?php echo $message['title']; ?></td>
			<td>
				<a href="<?php echo url("message::default/detail", array('uuid'=>$message['uuid'])); ?>">查看详情</a>
				<a href="<?php echo url("message::default/delete", array('uuid'=>$message['uuid'])); ?>">删除</a>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php else: ?>
		<tr><td><p>暂无站内消息</p></td></tr>
		<?php endif; ?>
	</table>
	<?php $this->_control('pagination', 'entrust-case-pagination', $pagination, $base); ?>
</div>
<?php $this->_endblock('maincontent'); ?>