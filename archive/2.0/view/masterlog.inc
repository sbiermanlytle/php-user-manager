<pre><?php include "../log/master.txt"; ?></pre>
<script type="text/javascript">
	update = function(){
		setTimeout(function(){
			if(document.hasFocus())
				location.reload();
			update();
		},2000);
	}; update();
</script>