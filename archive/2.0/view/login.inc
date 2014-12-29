<h2>Login</h2>
<?php 
	show_messages($results);
	html_form('login', array(
		'email', array('password',array('type'=>'password'))
	));
?>
<a href="forgot-password">Forgot Password?</a>
<script type="text/javascript">
<?php jq_fill_post_val('email'); ?>
</script>