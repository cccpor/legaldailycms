<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript">
$(function(){
	$('#tree-wrapper p').each(function(index,item){
		$(this).bind('click', function(){
			var uuid  = $(this).attr('id');
			var puuid = $('#'+$($(this).parents()[1]).attr('id')).prev().attr('id');
			$('#uuid').val(uuid);
			$('#puuid').val(puuid);
			$('#node-name').val($('#'+uuid).text());
			$('#pnode-name').val($('#'+puuid).text());
			$('#children-'+uuid).css('display', $('#children-'+uuid).css('display')=='none'?'block':'none');
		});
	});
});
</script><?php $this->_endblock("script"); ?>
<?php $this->_block("style"); ?><style type="text/css">
#tree-form { margin:5px 0; }
#tree-wrapper { font-size:12px; margin:5px 0; }
#tree-wrapper ul { float:left; }
#tree-wrapper p { float:left; width:60px; padding:1px 2px; }
#tree-wrapper li { clear:both; }
dt.selected { color:red; }
dt.selected+dd input[type='text'] { color:red; }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<h2 class="func-title"><?php echo $title; ?></h2>
<?php if ( !empty($error)) : ?>
<p class="warning"><?php echo $error; ?></p>
<?php endif; ?>
<form name="tree-form" id="tree-form" method="post" action="">
<fieldset>
<legend>树状数据编辑器</legend>
<dl>
	<dt>上级节点：</dt><dd><input type="text" name="pname" id="pnode-name" value=""/></dd>
	<dt class="selected">当前节点：</dt><dd><input type="text" name="name"  id="node-name"  value=""/></dd>
	<dt>新增节点：</dt><dd><input type="text" name="cname" id="cnode-name" value=""/></dd>
	<dt>&nbsp;</dt>
	<dd>
		<input type="hidden" name="action" id="action" value=""/>
		<input type="hidden" name="puuid"  id="puuid"  value=""/>
		<input type="hidden" name="uuid"   id="uuid"   value=""/>
		<input type="hidden" name="cuuid"  id="cuuid"  value=""/>
		<input type="button" name="add"    id="addbtn" value="增加节点" onclick="$('#action').val('add');this.form.submit();"/>
		<input type="button" name="del"    id="delbtn" value="删除选中" onclick="$('#action').val('del');this.form.submit();"/>
		<input type="button" name="mod"    id="modbtn" value="保存修改" onclick="$('#action').val('mod');this.form.submit();"/>
		<input type="button" name="mod"    id="modbtn" value="显示结构" onclick="window.location.href='<?php echo url('system::system/tree'); ?>/uuid/'+$('#uuid').val()"/>
		<input type="button" name="mod"    id="modbtn" value="完整视图" onclick="window.location.href='<?php echo url('system::system/tree'); ?>'"/>
	</dd>
</dl>
</fieldset>
</form>
<div id="tree-wrapper"><?php echo SystemTree::tree($puuid); ?></div>
<?php $this->_endblock("maincontent"); ?>