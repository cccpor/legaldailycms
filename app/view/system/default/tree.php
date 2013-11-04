<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript"></script><?php $this->_endblock("script"); ?>
<?php $this->_block("style"); ?><style type="text/css"></style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<?php if ( !empty($list) ) : ?>
<table class="list-table">
<?php foreach ( $list as $key => $value ) : $service = SystemService::pkv($value); ?>
<tr>
	<td colspan="3">
		<label>服务名称：</label>
		<a href="<?php echo url("{$service['namespace']}::{$service['controller']}/{$service['action']}"); ?>" title="function preview" target="_blank"><?php echo $service['name']; ?></a>
	</td>
</tr>
<tr>
	<td><label>命名空间：</label><?php echo $service['namespace']; ?></td>
	<td><label>控制器类：</label><?php echo $service['controller']; ?></td>
	<td><label>执行函数：</label><?php echo $service['action']; ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php $this->_control('pagination', 'service-pagination', array('base'=>$base, 'pagination'=>$pagination)); ?>
<?php else: ?>
<?php endif; ?>
<?php $this->_endblock("maincontent"); ?>