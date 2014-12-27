<p>Welcome to the Homepage</p>
<?php if($app->AUTHENTICATED) : ?>
	<a href="logout">logout</a>
	<a href="edit">edit</a>
<?php else : ?>
	<a href="login">login</a>
	<a href="register">register</a>
<?php endif; ?>