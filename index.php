<?php
include("simsimi.class.php");
session_start();
$sid = session_id();
$dir = 'data/'.substr($sid,-1).'/';
@mkdir($dir,0777,true);
$sim = new Simsimi(array('sid'=>$sid,'datapath'=>$dir.'sim_','proxy'=>'http://173.193.200.199:3128'));

function rjson($msg,$status=0) {
	$r = array('info'=>$msg,'status'=>$status);
	die(json_encode($r));
}
if (isset($_POST['content'])) {
	$content = trim($_POST['content']);
	if (!$content) rjson('请输入正确的内容');
	$result = $sim->talk($content);
	if ($result) {
		rjson($result,1);
	} else {
		rjson('我不知道你想表达什么',1);
	}
	
}

$simready = $sim->init();
?>
<!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="http://dodgepudding.github.io/webex/example/css/bootstrap.css" rel="stylesheet">
<link href="http://dodgepudding.github.io/webex/example/css/bootstrap-responsive.css" rel="stylesheet">
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>
<script src="http://dodgepudding.github.io/webex/compile.min.js"></script>
<title>聊天机器人</title>
<style>
.control-group {max-height:400px;overflow-y:auto;}
</style>
</head>
<body>
<div class="row">
	<div class="span10 offset1">
		<h2>地陪</h2>
	</div>
</div>
<div class="row">
	<div class="span10 offset1">
		<form class="form-horizontal ajax" method="post" callback="talkback(json)" action="index.php">
		<div class="control-group">
			<label class="control-label" for="talk">聊天内容:</label>
			<div id="talk" class="controls">
			<?php
			if ($simready) echo '<p><b>地陪: </b>hi</p>'
			 ?>
			</div>
		</div>
		<div class="control-group">
		 <label class="control-label" for="content">我：</label>
		 <div class="controls">
			<input class="input-xxlarge" required="required" type="text" name="content" id="content" value="" />
		 </div>
		</div>
		<div class="control-group">
		 <div class="controls">
		  	<label class="tips"></label>
			<button type="submit" class="btn">发送</button>
		 </div>
		</div>
		</form>
	</div>
</div>

<script type="text/javascript">
function talkback(json){
	if (json.status) {
		$('#talk').append('<p><b>我: </b>'+$('#content').val()+'</p>');
		$('#talk').append('<p><b>地陪: </b>'+json.info+'</p>');
		$('#content').val('');
	} else {
		$('.tips').html(json.info);
	}
}
</script>
</body>
