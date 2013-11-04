<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript">
function allowDrop ( ev ) {
	ev.preventDefault();
}

function onDragStart ( ev ) {
	ev.dataTransfer.setData("Text", ev.target.id);
}

function onDropStart ( ev ) {
	ev.preventDefault();
	var data = ev.dataTransfer.getData("Text");
	ev.target.appendChild(document.getElementById(data));
}
function savePermission ( btn ) {
	var permissions = new Array();
	$('#func-granded li').each(function(index,item){
		permissions.push($(this).attr('id'));
	});
	$('#permission-form').append($('<input type="hidden" name="permissions" value=""/>').val(permissions.join(',')));
	btn.form.submit();
}
$(function(){
	$('#tree-wrapper p').each(function(){
		$(this).bind('click', function(){
			var targetURL = "<?php echo url('system::system/acl'); ?>/group/"+($(this).attr('id'));
			window.location.href=targetURL;
		});
	});
});
</script><?php $this->_endblock("script"); ?>
<?php $this->_block("style"); ?><style type="text/css">
table.perm-table { width:100%; }
table.perm-table tr.double { background-color:#eee; }
table.perm-table th { text-align:left; vertical-align:top; font-size:15px; padding:5px; border:1px solid #ccc; }
table.perm-table td { text-align:left; vertical-align:top; font-size:13px; padding:3px; border:1px solid #ccc; }
table.perm-table td p { padding:2px 10px; }
table.perm-table td span.g { color:black; padding-left:20px; }

#tree-wrapper { font-size:12px; margin:5px 0; }
#tree-wrapper ul { float:left; }
#tree-wrapper p { float:left; width:60px; padding:1px 2px; }
#tree-wrapper li { clear:both; }
#grandings { border:none; background-color:#ccc; padding-top:20px; }
#op-wrapper { clear:both; border-bottom:1px solid gray; text-align:right; padding:5px 20px; }
</style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<h2 class="func-title"><?php echo $title; ?></h2>
<div id="tree-wrapper"><?php echo SystemTree::tree($gr); ?></div>
<div id="op-wrapper"><a href="<?php echo url('system::system/tree', array('uuid'=>$gr)); ?>" target="_blank">编辑，添加，删除群组，点击这里</a></div>
<div class="blank">&nbsp;</div>
<table class="perm-table">
<tr><th>群组名称</th><th>已分配权限</th><th>可分配权限</th></tr>
<?php 
	$ops  = array();
	$pad  = '&nbsp;&nbsp;&nbsp;&nbsp;';
	$path = SystemTree::path($group['uuid']);
	foreach( $path as $key => $guuid) : 
	$perms = SystemACL::groupPerms($guuid); 
?>
<tr>
	<td>
		<span class="g"><?php echo str_repeat($pad, $key) . SystemTree::str($guuid); ?></span>
		<?php if ($guuid==$user['group']) : ?>
		<span class="u"><?php echo $user['username']; ?></span>
		<?php endif; ?>
	</td>
	<td ondrop="onDropStart(event)" ondragover="allowDrop(event)">
		<?php foreach ($perms as $sk => $suuid): ?>
		<p draggable="true"  ondragstart="onDragStart(event)" id="<?php echo $suuid; ?>"><?php echo SystemService::str($suuid); ?></p>
		<?php $ops[] = $suuid; endforeach; ?>
	</td>
	<?php if ($key==0): ?>
	<td ondrop="onDropStart(event)" ondragover="allowDrop(event)" rowspan="<?php echo count($path)+1; ?>" id="grandings">
	<?php foreach ( $grandings as $k => $v ): $granding = SystemService::pkv($v); ?>
	<p draggable="true" ondragstart="onDragStart(event)" id="<?php echo $granding['uuid']; ?>"><?php echo $granding['name']; ?></p>
	<?php endforeach; ?>	
	</td>
	<?php endif; ?>
</tr>
<?php endforeach; ?>
</table>
<div class="blank">&nbsp;</div>
<?php $this->_endblock("maincontent"); ?>