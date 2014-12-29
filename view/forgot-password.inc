<h2>Reset Password</h2>
<?php 
	$iv_size = get_iv_size();
    $iv = create_iv($iv_size);
    $k = substr(base64_encode($iv), 0, 43);
?>
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