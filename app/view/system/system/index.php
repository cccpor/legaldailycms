<?php $this->_extends('../_layouts/basic_layout'); ?>
<?php $this->_block("title");?><?php echo $title;?><?php $this->_endblock("title"); ?>
<?php $this->_block("script"); ?><script type="text/javascript"></script><?php $this->_endblock("script"); ?>
<?php $this->_block("style"); ?><style type="text/css"></style><?php $this->_endblock("style"); ?>
<?php $this->_block('maincontent'); ?>
<h2 class="func-title"><?php echo $title; ?></h2>
<?php $this->_endblock("maincontent"); ?>