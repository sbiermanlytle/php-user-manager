<h2>Register a New Account</h2>
<?php 
	show_messages($results);
	html_form('register', array(
		'name',
		'email',
		'username',
		array('password',array('type'=>'password')),
		array('re-type',array('type'=>'password'))
	));
?>
<script type="text/javascript">
<?php
	jq_fill_post_val('name');
	jq_fill_post_val('email');
	jq_fill_post_val('username');
	jq_fill_post_val('password');
	jq_fill_post_val('re-type');
?>
</script>