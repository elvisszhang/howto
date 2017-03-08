<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
PHP包更新结果：<br>
<textarea style="width:800px;height:600px">
<?php
set_time_limit(0);

//execute command
$pwd = "*****";
$pipes = array();
$command = "sudo /usr/local/php/bin/php ./bin/satis build satis.json ./public";
$desc = array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w'));
$handle = proc_open($command, $desc, $pipes, $pwd);
if (!is_resource($handle)) {
    fprintf(STDERR, "proc_open failed.\n");
    exit(1);
}

//output
while( $ret=fgets($pipes[2]) ){
   echo translate($ret);
}
echo PHP_EOL;
echo PHP_EOL;
while( $ret=fgets($pipes[1]) ){
   echo translate($ret);
}


///close handles
fclose($pipes[0]);
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($handle);

//translate to chinese
function translate($ret){
	$ret = str_replace(chr(08),'',$ret); //去除BS字符
	$ret = str_replace('Reading composer.json',PHP_EOL.'读入 composer.json',$ret);
	$ret = str_replace('Scanning packages','扫描组件包',$ret);
	$ret = str_replace('Creating local downloads in','创建本地下载包 ',$ret);
	$ret = str_replace('Writing web view','更新网页视图 http://packages.jrtk.net/',$ret);
	$ret = str_replace('Warning','警告',$ret);
	$ret = str_replace('Skipping','跳过',$ret);
	$ret = str_replace('Dumping','导出',$ret);
	$ret = str_replace('Writing','写入',$ret);
	$ret = str_replace('Deleted','删除',$ret);
	$ret = str_replace('Accessing','访问',$ret);
	$ret = str_replace('over http which is an insecure protocol','使用http协议是不安全的',$ret);
	$ret = str_replace('Pruning include directories','裁剪包含目录',$ret);
	$ret = str_replace('wrote packages to','组件包信息已写入',$ret);
	return $ret;
}
?>
</textarea>
</body></html>