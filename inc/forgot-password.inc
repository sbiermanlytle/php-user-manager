<h2>Reset Password</h2>
<?php $app->print_msgs($results); ?>
<form method="post" action="forgot-password" name="passwordform" id="passwordform">
<fieldset>
    <label for="email">Email Address:</label><input type="text" name="email" id="email" /><br />
    <input type="submit" name="password_btn" id="password_btn" value="Reset Password" />
</fieldset>
</form>