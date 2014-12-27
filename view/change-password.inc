<h2>Change Password</h2>
<?php 
	show_messages($results);
	html_form('change-password', array(
		array('uri', array('type'=>'hidden','no_label'=>true,
			'value'=>($uri!='change-password' ? $uri : $_POST['uri']))),
		'key',
		'username',
		array('password', array('type'=>'password')),
		array('re-type', array('type'=>'password')),
		),array('change-password',array('value'=>'change password'))
	);

?>
<script type="text/javascript">
<?php 
	jq_fill_post_val('key');
	jq_fill_post_val('username');
?>
</script>
