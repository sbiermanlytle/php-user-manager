<h2>Edit Your Account</h2>
<?php $app->print_msgs($results); ?>
<form method="post" name="edit_form" id="edit_form">
<fieldset>
    <label for="name">Name:</label><input type="text" name="name" id="name" /><br />
    <label for="email">Email:</label><input type="text" name="email" id="email" /><br />
    <label for="username">Username:</label><input type="text" name="username" id="username" /><br />
    <input action="edit" type="submit" name="save" id="save" value="save" />
</fieldset>
</form>

<h3>Change Password</h3>
<?php $app->print_msgs($p_results); ?>
<form method="post" name="change_password_form" id="change_password_form">
<fieldset>
    <label for="old_password">Current Password:</label><input type="password" name="old_password" id="old_password" /><br />
    <label for="new_password">New Password:</label><input type="password" name="new_password" id="new_password" /><br />
    <label for="p_v">Re-Type New Password:</label><input type="password" name="p_v" id="p_v" /><br />
    <input action="edit" type="submit" name="change_password" id="change_password" value="change password" />
</fieldset>
</form>
<script type="text/javascript">
	$('#name').val("<?php if(!empty($_POST['name'])) echo $_POST['name']; 
                           else echo $app->getUserData('name'); ?>");
  $('#email').val("<?php if(!empty($_POST['email'])) echo $_POST['email']; 
                           else echo $app->getUserData('email'); ?>");
	$('#username').val("<?php if(!empty($_POST['username'])) echo $_POST['username']; 
                           else echo $app->getUserData('username'); ?>");
</script>