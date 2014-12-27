<?php $app->print_msgs($results); ?>
<form method="post" action="login" name="loginform" id="loginform">
<fieldset>
    <label for="login">Username/Email:</label><input type="text" name="login" id="login" /><br />
    <label for="password">Password:</label><input type="password" name="password" id="password" /><br />
    <input type="submit" name="login_btn" id="login_btn" value="login" />
</fieldset>
</form>
<a href="register">Create a new account</a>
<a href="forgot-password">Forgot Password?</a>
<script type="text/javascript">
<?php if(!empty($_POST['login'])) : ?>
	$('#login').val("<?php echo $_POST['login']; ?>");
<?php endif; ?>
</script>