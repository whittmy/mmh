<?php
session_start();
if(isset($_SESSION['user']) && $_SESSION['user']==true){
	echo "";
}
else{
	$_SESSION['user']=false;
	echo "您无权访问该文件！请先登录";
	echo "<script>window.location='admin.html';</script>";
}
?>