<?php
$dir = trim(dirname(__FILE__)).'/';
//exit($dir.'config.php');
require($dir.'config.php');

$submit=$_POST['submit'];
if($submit == "登陆"){
	$user = trim($_POST['user']);
	$pwd = trim($_POST['password']);

    //echo $user.', '.$pwd.'<br>';
    //print_r($GLOBALS['LOGIN_INFO']);
    //echo '<br>';
    //echo $GLOBALS['LOGIN_INFO'][$user];
    //exit;
    
    $skey = '';
    if(strlen($user)>0 && isset($GLOBALS['LOGIN_INFO'][$user])){
        $skey = $GLOBALS['LOGIN_INFO'][$user];
    }
    
	if(strlen($skey)>0 && $pwd == $skey)
	{
		session_start();
        setcookie('cookies', $_SESSION['user'], time() + 60*10, "/");
		echo $_SESSION['user']=true;
		echo $_SESSION['admin']=$user;
		//echo("<meta http-equiv=refresh content='0; url=../first.php'>");
		echo "<script>window.location='../index.php';</script>";
	}
	else{
		echo "<script>alert('用户名或密码错误！');</script>";
		echo("<meta http-equiv=refresh content='0; url=../admin.html'>");
	}
}
?>