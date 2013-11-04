<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript"></script><?php $this->_endblock("script"); ?>
<?php $this->_block("style"); ?><style type="text/css">
a { color:black; }
p.ops-wrapper { font-size:13px; text-align:right; border-bottom:1px solid gray; margin-bottom:10px; padding:5px; }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<h2 class="func-title"><?php echo $title; ?></h2>
<p class="ops-wrapper"><a target="_blank" href="<?php echo url('system::user/form'); ?>">添加用户</a></p>
<?php if ( !empty($list) ) : ?>
<table class="list-table">
<?php 
	foreach ( $list as $key => $uid ) : 
	$user = SystemUser::pkv($uid); 
	if ( empty($user) ) : continue; endif; 
	$group  = SystemTree::pkv($user['group']);
	$region = SystemTree::pkv($user['region']);
?>
<tr>
	<td colspan="4">
		<label>登陆名称：</label>
		<a href="<?php echo url('system::user/form', array('uid'=>$uid)); ?>" title="<?php echo $user['username']; ?>"><?php echo $user['username']; ?></a>
	</td>
</tr>
<tr>
	<td>
		<label>注册日期：</label>
		<?php echo $user['create']; ?>
	</td>
	<td>
		<label>最后登陆：</label>
		<?php echo $user['last']; ?>
	</td>
	<td>
		<label>所属群组：</label>
		<a href="<?php echo url('system::user/index', array('group'=>$user['group'])); ?>" title="<?php echo $group['name']; ?>"><?php echo $group['name']; ?></a>
	</td>
	<td>
		<label>所属地区：</label>
		<?php echo $region['name']; ?>
	</td>	
</tr>
<?php endforeach; ?>
</table>
<?php $this->_control('pagination', 'service-pagination', array('base'=>$base, 'pagination'=>$pagination)); ?>
<?php else: ?>
<?php endif; ?>
<?php $this->_endblock("maincontent"); ?>