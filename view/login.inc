<h2>Login</h2>
<?php 
	echo '<p>'.$result.'</p>';
	html_form('login', array(
		'email', array('password',array('type'=>'password'))
	));
?>
<a href="forgot-password">Forgot Password?</a>
<script type="text/javascript">
<?php jq_fill_post_val('email'); ?>
</script>