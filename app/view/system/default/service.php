<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript"></script><?php $this->_endblock("script"); ?>
<?php $this->_block("style"); ?><style type="text/css"></style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<h2 class="func-title"><?php echo $title; ?></h2>
<form name="service-form" id="service-form" method="post" action="<?php echo url('system::default/service'); ?>">
<fieldset>
<legend>系统服务表单</legend>
<dl>
	<dt>功能模块：</dt><dd><input type="text" name="namespace" id="service-namespace" value="<?php echo empty($init['namespace'])?'':$init['namespace']; ?>"/></dd>
	<dt>控制器类：</dt><dd><input type="text" name="controller" id="service-controller" value="<?php echo empty($init['controller'])?'':$init['controller']; ?>"/></dd>
	<dt>执行函数：</dt><dd><input type="text" name="action" id="service-action" value="<?php echo empty($init['action'])?'':$init['action']; ?>"/></dd>
	<dt>功能名称：</dt><dd><input type="text" name="name" id="service-name" value="<?php echo empty($init['name'])?'':$init['name']; ?>"/></dd>
	<dt>功能描述：</dt><dd><input type="text" name="desc" id="service-desc" value="<?php echo empty($init['desc'])?'':$init['desc']; ?>"/></dd>
	<dt>&nbsp;</dt>
	<dd>
		<input type="hidden" name="uuid" id="service-uuid" value="<?php echo empty($init['uuid'])?'':$init['uuid']; ?>"/>
		<input type="button" name="savebtn" value="提交保存" onclick="this.form.submit();"/>
		<?php if ( !empty($init['uuid']) ) : ?>
		<input type="button" name="delbtn" value="删除服务" onclick="window.location.href='<?php echo url('system::default/delete', array('uuid'=>$init['uuid'])); ?>'"/>
		<?php endif; ?>
	</dd>
</dl>
</fieldset>
</form>
<?php $this->_endblock("maincontent"); ?>