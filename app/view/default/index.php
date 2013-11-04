<?php $this->_extends('./_layouts/leftright_layout'); ?>
<?php $this->_block('title'); ?><?php echo $title; ?><?php $this->_endblock('title'); ?>
<?php $this->_block("style"); ?>
<style type="text/css">
</style>
<?php $this->_endblock("style"); ?>
<?php $this->_block('script'); ?><script type="text/javascript">
</script><?php $this->_endblock('script'); ?>
<?php $this->_block("left"); ?>
left
<?php $this->_endblock("left"); ?>
<?php $this->_block('right'); ?>
right
<?php $this->_endblock('right'); ?>