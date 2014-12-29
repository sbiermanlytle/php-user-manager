<h2>Reset Password</h2>
<?php $k = str_replace(array("/","+"),"k",create_salt()); ?>
<p>copy this key: <span><?=$k?></span></p>
<p>it will only be valid for the next <?=$GLOBALS['PASSWORD_KEY_LIFETIME']?> minutes</p>
<?php
	html_form('forgot-password', array(
		array('k', array('type'=>'hidden','no_label'=>true,
			'value'=>$k)),
		'email'
		),array('forgot-password',array('value'=>'forgot password'))
	);
?>