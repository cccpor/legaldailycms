<form id="system-message-form" method="post" action="<?php echo url("message::default/message"); ?>">
<fieldset>
<legend><?php echo empty($init['info'])?'':$init['info']; ?></legend>
<dl>
	<dt>收件人：</dt>
	<dd><input type="text" name="receiverName" value="<?php echo empty($init['receiverName'])?'':$init['receiverName']; ?>"/></dd>
	<dt>信息主题：</dt>
	<dd><input name="title" value="<?php echo empty($init['title'])?'':$init['title']; ?>" /></dd>
	<dt>信息内容：</dt>
	<dd><textarea name="content" onfocus="$(this).text('')"><?php echo empty($init['content'])?'':$init['content']; ?></textarea></dd>
	<dt>&nbsp;</dt>
	<dd>
		<input type="hidden" name="receiver" value="<?php echo empty($init['receiver'])?'':$init['receiver']; ?>">
		<input type="hidden" name="sender" value="<?php echo empty($init['sender'])?'':$init['sender']; ?>"/>
		<input type="submit" name="submit" value="发送信息"/>
	</dd>
</dl>
</fieldset>
</form>