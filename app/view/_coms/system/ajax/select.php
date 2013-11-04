<div class="select-wrapper" id="legal-system-select-wrapper">
	<p class="pannel"><a href="javascript:void(0);" onclick="__LA.d()" title="关闭">X</a></p>
	<ul class="path">
		<?php foreach ($path as $pk => $pv ): ?>
		<li><a href="javascript:void(0);" onclick="__LA.r('<?php echo $pv['uuid']; ?>', '<?php echo $pv['name']; ?>')" title="<?php echo $pv['name']; ?>"><?php echo $pv['name']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<ul class="direct">
		<?php foreach ( $direct as $dk => $dv ): ?>
		<li><a href="javascript:void(0);" onclick="__LA.r('<?php echo $dv['uuid']; ?>', '<?php echo $dv['name']; ?>')" title="<?php echo $dv['name']; ?>"><?php echo $dv['name']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<ul class="pagination"></ul>
</div>