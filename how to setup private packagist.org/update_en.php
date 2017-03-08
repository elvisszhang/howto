<html>
<head></head>
<body>
	<textarea style="width:800px;height:600px">
	<?php
	set_time_limit(0);
	$pwd = "*****";
	$pipes = array();
	$command = "sudo /usr/local/php/bin/php ./bin/satis build satis.json ./public";
	$desc = array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w'));
	$handle = proc_open($command, $desc, $pipes, $pwd);
	if (!is_resource($handle)) {
		fprintf(STDERR, "proc_open failed.\n");
		exit(1);
	}
	while( $ret=fgets($pipes[1]) ){
	   echo $ret;
	}
	fclose($pipes[0]);
	fclose($pipes[1]);
	fclose($pipes[2]);
	proc_close($handle);
	?>
	</textarea>
</body>
</html>