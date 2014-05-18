<?php
// 设定参数
$phpfile = 'cron.php';// 需要执行的Cron文件名
$time = 1;// 间隔分钟数
// 关闭浏览器仍然执行
set_time_limit(0);
ignore_user_abort(true);
// 读取记录
$A=$B=$C=$D= 0;
$F = off;
include('cronlog.php');
if (time() - $A < 30)
	exit;
// 判断是否有进程在执行
if ($F == on)
	exit();
// 判断D是否为空
if ($D == 0){
	$D = $C;
	$C = $B;
	$B = $A;
	$A = time();
	writelog($A,$B,$C,$D,$F);
	include($phpfile);
	exit();
}
//	启动修正
$D = $C;
$C = $B;
$B = $A;
$A = time();
$E = ($A-$D)/3;
writelog($A,$B,$C,$D,$F);
// 计算运行次数
$time *= 60;
$i=round($E/$time);
if ($i <= 0){
	include($phpfile);
	exit();
}
if ($i > 60){
	$A=$B=$C=$D= 0;
	$F = off;
	writelog($A,$B,$C,$D,$F);
	exit();
}
// 防止多进程运行
$F = on;
writelog($A,$B,$C,$D,$F);
// 循环
$u=1;
while($u<=$i){
	include($phpfile);
	if ($A+$E-time()<120){
		$F = off;
		writelog($A,$B,$C,$D,$F);
	}
	if ($A+$E-time()<60)
		exit();
	sleep ($time);
	$u++;

}
exit();
// 自定义函数
function writelog($A,$B,$C,$D,$F){
	$file = '<?php'.PHP_EOL.'$A = '.$A.';'.PHP_EOL.'$B = '.$B.';'.PHP_EOL.'$C = '.$C.';'.PHP_EOL.'$D = '.$D.';'.PHP_EOL.'$F = '.$F.';'.PHP_EOL.'?>';
	file_put_contents("cronlog.php",$file);
}

?>