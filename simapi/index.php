<?php
include("../simsimi.class.php");
$sid = $_REQUEST['sid']?trim($_REQUEST['sid']):getenv('REMOTE_ADDR');
$sid = md5($sid);
$content = $_REQUEST['msg']?trim($_REQUEST['msg']):'';
$dir = '../data/'.substr($sid,-1).'/';
@mkdir($dir,0777,true);
$sim = new Simsimi(array('sid'=>$sid,'datapath'=>$dir.'sim_','proxy'=>'http://173.193.200.199:3128'));

function rjson($msg,$status=0) {
	$r = array('info'=>$msg,'status'=>$status);
	die(json_encode($r));
}
if (!$content) rjson('error');
$result = $sim->talk($content);
if ($result) {
	rjson($result,1);
} else {
	rjson('failed');
}
