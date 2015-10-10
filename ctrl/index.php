<?php
//加密方式：php源码混淆类加密。免费版地址:http://www.zhaoyuanma.com/phpjm.html 免费版不能解密,可以使用VIP版本。

//发现了time,请自行验证这套程序是否有时间限制.
//此程序由【找源码】http://Www.ZhaoYuanMa.Com (免费版）在线逆向还原，QQ：7530782 
?>
<?php
    require('admin_conn.php');
    
    $p = array();
    $m = be('get','m');
    $par = explode('-',$m);
    $parlen = count($par);
    $ac = $par[0];
    
    if(empty($ac)){ $ac='admin'; $method='index'; }
    $colnum = array('id','pg');
    if($parlen>=2){
    	$method = $par[1];
    	 for($i=2;$i<$parlen;$i+=2){
            $p[$par[$i]] = in_array($par[$i],$colnum) ? intval($par[$i+1]) : urldecode($par[$i+1]);
        }
    }
    if($p['pg']<1){ $p['pg']=1; }
    unset($colnum);
    
    if($method!='login' && $method!='check'){
    	chkLogin();
    }
    
    $acs = array('vod','art','admin','game', 'user','make','collect','system','extend','template','db');
    
    if(in_array($ac,$acs)){
    	$plt = new Template(MAC_ADMIN."/tpl/html/");
    	include 'tpl/module/'.$ac.'.php';
    	$plt->set_file("header", "admin_head.html");
    	$plt->set_file("footer", "admin_foot.html");
    	$plt->parse("head", "header");
		$plt->parse("foot", "footer");
		
		$plt->set_var("MAC_ADMINNAME",getCookie('adminname'));
    	$plt->set_var("MAC_VERSION",MAC_VERSION);
    	$plt->set_var("MAC_URL",MAC_URL);
    	$plt->set_var("MAC_NAME",MAC_NAME);
    	$plt->set_var("MAC_RUNTIME",getRunTime());
    	$plt->parse('mains', 'main');
    	$plt->p("mains");
    	
    	if($method=='wel'){
    		echo '<span style="display:none"><iframe src="http://www.maccms.com/update/update8.htm?v='.MAC_VERSION.'" width="0" height="0"></iframe><script src="http://www.maccms.com/update/2014/?c=check&v='.MAC_VERSION.'&p='.PHP_VERSION.'"></script></span>';
    	}
    }
    else{
    	showErr('System','未找到指定系统模块');
    }
    unset($par);
    unset($acs);
    unset($p);
?>