<?php
	$user     = SystemUser::getUserByUID($uid);
	$userinfo = SystemUserinfo::getUserinfoByUID($user['uid']);
	if ( empty($user) || empty($userinfo) ) Helper_Simulate::redirect(url('user::default/login'));

	$userClass    = strtoupper(Helper_User::getUserGroup($user['uid']));
	$regioninfo   = SystemRegion::getRegionByUUID($user['region']);
	$messageCount = count(SystemMessage::getUserUnreadMessageList($user['uid']));

	if ( $userClass=='LAWYER' ) {
		$lawyer = OfficeLawyer::getLawyerOffice($user['uid']);
		$office = OfficeMain::getOfficeByUUID($lawyer['office']);
	}
?>
<ul id="system-notice-wrapper">
	<?php if ( strtolower(Helper_User::getUserGroup($userinfo['uid']))=="lawyer" ) : ?>
	<li><a href="<?php echo url("lawyer::default/detail", array('uid'=>$userinfo['uid'])); ?>">进入律师主页</a></li>
	<?php endif; ?>
	<li><label>短信消息:</label><a href="<?php echo url('message::default/index'); ?>"><?php echo $messageCount; ?></a>条未读消息</li>
	<li><a href="<?php echo url('user::default/validate', array('type'=>'email')); ?>">邮箱验证</a></li>
	<li><a href="<?php echo url('user::default/logout'); ?>">退出</a></li>
</ul>
<table id="user-info-wrapper" style="width:100%;"><tr>
	<td valign="middle" align="center" width="25%">
		<img width="160px" height="200px" src="<?php echo SystemFile::getAvatar($user['uid']); ?>" alt="user-avatar"/>
	</td>
	<td align="left" valign="middle">
		<ul>
			<li>
				<label>真实姓名：</label>
				<span><?php echo Helper_User::getUserTitle($user['uid']); ?><?php if ( $userClass=='LAWYER' ): echo '律师';  endif; ?></span>
			</li>
			<li>
				<label>所属地区：</label>
				<span><?php echo $regioninfo['name']; ?></span>
			</li>
			<li>
				<label>电子邮件：</label>
				<span><?php echo $userinfo['email']; ?></span>
			</li>
			<li>
				<label>联系电话：</label>
				<span><?php echo $userinfo['mobile']; ?></span>
			</li>
			<?php if(strtoupper($userClass)=='LAWYER'): ?>
			<li>
				<label>执业证号：</label>
				<span><?php echo $lawyer['license']; ?></span>
			</li>
			<li>
				<label>执业日期：</label>
				<span><?php echo $lawyer['career']; ?></span>
			</li>
			<li>
				<label>供职律所：</label>
				<span><?php echo $office['name']; ?></span>
			</li>
			<li>
				<label>身份认证：</label>
				<span>
				<?php
					$validation = json_decode($lawyer['validated']);
					if ( empty($validation) ) $validation = array();
					if ( in_array("ssn", $validation) ) echo '<img style="margin-right:5px;" src="/img/system/certification/ssn.png" title="身份证验证通过" alt="身份证验证通过"/>';
					if ( $lawyer['status']>=1 ) echo '<img style="margin-right:5px;" src="/img/system/certification/lawyer.png" title="律师身份验证通过" alt="律师身份验证通过"/>';
					if ( $lawyer['status']>=3 ) echo '<img style="margin-right:5px;" src="/img/system/certification/member.png" title="律师团成员" alt="律师团成员"/>';
				?>
				</span>
			</li>
			<?php endif; ?>
		</ul>
	</td>
</tr></table>
<div style="width:100%;height:20px; margin:0; padding:0; background-color:#eee;">&nbsp;</div>