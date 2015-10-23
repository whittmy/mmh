<?php
    $dir = trim(dirname(__FILE__)).'/';
    //exit($dir.'commonphp/config.php');
    require($dir.'commonphp/config.php');
    
	$s_conn = null;
	function connect(){
		global $s_conn;
		close();
		$s_conn = mysql_connect($GLOBALS['HOST'], $GLOBALS['USER'], $GLOBALS['PASSWD'], 1, "131072");
		mysql_set_charset('utf8');
		if(!$s_conn){
			exit('<option value=-1>连接数据库出错</option>');
		}	
		mysql_select_db($GLOBALS['DB']);		
	}
	
	function close(){
		global $s_conn;
		if($s_conn)
			mysql_close($s_conn);		
	}
?>