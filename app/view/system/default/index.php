<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript"></script><?php $this->_endblock("script"); ?>
<?php $this->_block("style"); ?><style type="text/css">
a { color:black; }
p.ops-wrapper { font-size:13px; text-align:right; border-bottom:1px solid gray; margin-bottom:10px; padding:5px; }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<h2 class="func-title"><?php echo $title; ?></h2>
<p class="ops-wrapper"><a target="_blank" href="<?php echo url('system::default/service'); ?>">注册功能</a></p>
<?php if ( !empty($list) ) : ?>
<table class="list-table">
<?php 
	foreach ( $list as $key => $value ) : 
	$service = SystemService::pkv($value);
	$access  = ''; 
?>
<tr>
	<td colspan="4">
		<label>服务名称：</label>
		<a href="<?php echo url("{$service['namespace']}::{$service['controller']}/{$service['action']}"); ?>" title="function preview" target="_blank"><?php echo $service['name']; ?></a>
	</td>
</tr>
<tr>
	<td>
		<label>命名空间：</label>
		<a href="<?php echo url('system::default/index', array('ns'=>$service['namespace'])); ?>"><?php echo $service['namespace']; ?></a>
	</td>
	<td>
		<label>控制器类：</label>
		<a href="<?php echo url('system::default/index', array('ct'=>$service['controller'])); ?>"><?php echo $service['controller']; ?></a>
	</td>
	<td>
		<label>执行函数：</label>
		<?php echo $service['action']; ?>
	</td>
	<td>
		<label>操作：</label>
		<a target="_blank" href="<?php echo url('system::default/service', array('uuid'=>$service['uuid'])); ?>">编辑</a>
	</td>
</tr>
<?php endforeach; ?>
</table>
<?php $this->_control('pagination', 'service-pagination', array('base'=>$base, 'pagination'=>$pagination)); ?>
<?php else: ?>
<?php endif; ?>
<?php $this->_endblock("maincontent"); ?>