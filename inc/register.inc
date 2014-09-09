<h2>Register a New Account</h2>
<?php $app->print_msgs($results); ?>
<form method="post" action="register" name="register_form" id="register_form">
<fieldset>
    <label for="name">Name:</label><input type="text" name="name" id="name" /><br />
    <label for="email">Email:</label><input type="text" name="email" id="email" /><br />
    <label for="username">Username:</label><input type="text" name="username" id="username" /><br />
    <label for="password">Password:</label><input type="password" name="password" id="password" /><br />
    <label for="p_v">Re-Type:</label><input type="password" name="p_v" id="p_v" /><br />
    <input type="submit" name="register" id="display_registration" value="create new account" />
</fieldset>
</form>
<script type="text/javascript">
<?php if(!empty($_POST['name'])) : ?>$('#name').val("<?php echo $_POST['name']; ?>");<?php endif; ?>
<?php if(!empty($_POST['email'])) : ?>$('#email').val("<?php echo $_POST['email']; ?>");<?php endif; ?>
<?php if(!empty($_POST['username'])) : ?>$('#username').val("<?php echo $_POST['username']; ?>");<?php endif; ?>
<?php if(!empty($_POST['password'])) : ?>$('#password').val("<?php echo $_POST['password']; ?>");<?php endif; ?>
<?php if(!empty($_POST['p_v'])) : ?>$('#p_v').val("<?php echo $_POST['p_v']; ?>");<?php endif; ?>
</script>