<h2><?=$_SESSION['username']?></h2>
<?php 
	show_messages($results);
	html_form('profile', array(
		'name',
		'email',
		'username'
		),array('profile',array('value'=>'update profile'))
	);
?>
<h3>Change Password</h3>
<?php
	html_form('profile', array(
		array('old', array('type'=>'password')),
		array('new', array('type'=>'password')),
		array('re-type', array('type'=>'password'))
		),array('password',array('value'=>'change password')),
		array('name'=>'change_password','id'=>'change_password')
	);
?>
<script type="text/javascript">
<?php 
	jq_fill_post_or_session_val('name');
	jq_fill_post_or_session_val('email');
	jq_fill_post_or_session_val('username');
?>
</script>