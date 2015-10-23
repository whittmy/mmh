<?php
	$s_conn = null;
	function connect(){
		global $s_conn;
		close();
		$s_conn = mysql_connect("localhost", "root", "lemoon8888", 1, "131072");
		mysql_set_charset('utf8');
		if(!$s_conn){
			exit('<option value=-1>连接数据库出错</option>');
		}	
		mysql_select_db('giec_upgrade');		
	}	
	function close(){
		global $s_conn;
		if($s_conn)
			mysql_close($s_conn);		
	}
?>