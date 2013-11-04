<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?><?php echo $title; ?><?php $this->_endblock('title'); ?>
<?php $this->_block('script'); ?><script type="text/javascript"></script><?php $this->_endblock('script'); ?>
<?php $this->_block("style");?><style type="text/css">
table.perm-table { width:100%; }
table.perm-table tr.double { background-color:#eee; }
table.perm-table th { text-align:left; vertical-align:top; border:1px solid #eee; font-size:15px; padding:5px; }
table.perm-table td { text-align:left; vertical-align:top; border:1px solid #eee; font-size:13px; padding:3px; }
table.perm-table td span.g { color:black; }
table.perm-table td span.u { color:red; }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block("maincontent"); ?>
<h3><?php echo $title; ?></h3>
<h4>用户群组操作权限信息数据表</h4>
<table class="perm-table">
<tr><th>群组名称</th><th>操作权限</th></tr>
<?php 
	$ops = array();
	$pad = '&nbsp;&nbsp;&nbsp;&nbsp;';
	foreach(SystemTree::path($user['group']) as $key => $guuid) : 
	$perms = SystemACL::groupPerms($guuid); 
?>
<tr <?php if($key%2==0): ?>class="double"<?php endif; ?>>
	<td>
		<span class="g"><?php echo str_repeat($pad, $key) . SystemTree::str($guuid); ?></span>
		<?php if ($guuid==$user['group']) : ?>
		<span class="u"><?php echo $user['username']; ?></span>
		<?php endif; ?>
	</td>
	<td>
		<?php foreach ($perms as $sk => $suuid): if (in_array($suuid,$ops)) continue; $service = SystemService::pkv($suuid); ?>
		<p><?php echo $service['name']; ?></p>
		<?php $ops[] = $suuid; endforeach; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<?php $this->_endblock('maincontent'); ?>