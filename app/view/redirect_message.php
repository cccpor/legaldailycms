<?php $this->_extends('./_layouts/basic_layout'); ?>
<?php $this->_block('title'); ?>系统跳转消息！<?php $this->_endblock('title'); ?>
<?php $this->_block('script'); ?>
<script type="text/javascript">$(function(){setTimeout("window.location.href ='<?php echo $redirect_url; ?>';", <?php echo $redirect_delay * 1000; ?>);});<?php echo $hidden_script; ?></script>
<?php $this->_endblock('script'); ?>
<?php $this->_block('style'); ?><style type="text/css">
a { color:gray; text-decoration:none; }
#body-wrapper { text-align:left; border:10px solid #eee; border-radius:10px; overflow:hidden; box-shadow:0 20px 20px gray; filter:Shadow(Color='gray',Direction='135',Strength='2');}
#body-wrapper>div.link { padding:10px; text-align:right; padding-right:40px; }
#body-wrapper>div.title { height:40px; line-height:40px; color:white; font-weight:bold; font-size:25px; padding-left:20px; background-color:#FCB034; }
#body-wrapper>div.message{ height:80px; line-height:80px; padding:20px; background-color:#333; color:white; font-size:18px; }
</style><?php $this->_endblock('style'); ?>
<?php $this->_block('maincontent'); ?>
<div id="body-wrapper">
	<div class="title"><?php echo $message_caption; ?></div>
	<div class="message"><?php echo nl2br(h($message_body)); ?></div>
	<div class="link"><a href="<?php echo $redirect_url; ?>">如果您的浏览器没有自动跳转，请点击这里</a></div>
</div>
<?php $this->_endblock('maincontent'); ?>
