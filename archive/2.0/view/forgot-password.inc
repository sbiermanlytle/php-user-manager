<h2>Reset Password</h2>
<?php 
	$iv_size = get_iv_size();
    $iv = create_iv($iv_size);
    $k = substr(base64_encode($iv), 0, 43);
?>
<p>copy and save this key: <span><?=$k?></span></p>
<?php
	html_form('forgot-password', array(
		array('k', array('type'=>'hidden','no_label'=>true,
			'value'=>$k)),
		'email'
		),array('forgot-password',array('value'=>'forgot password'))
	);
?>