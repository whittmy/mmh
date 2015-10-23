<?php
	include 'connect.php';
	include '../util/baidu_pan_parser.php';
    include '/home/web/data_center/api/util/myCache.php';//add cache    
	// $_GET['cur'], $_GET['model'], $_GET['oem'], $_GET['mac']
    
function curPageURL() 
{
    $pageURL = 'http';

    if ($_SERVER["HTTPS"] == "on") 
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80") 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } 
    else 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}    
    $cur_urlpath = curPageURL();

                    $t1 = time()-30*60;
                    $t2 = $t1 - 16*60*60-16;
                    $t3 = $t2 - 2*60*60-96;
                    $t4 = $t3 - 10*60;      
                    $t5 = time();                    
                    //$url = 'http://pan.baidu.com/s/1qtFb8'; 
                    $url = 'http://pan.baidu.com/s/1qYwF1';
                    $share_parse_url = baidu_pan_get_paser_url($url);
                    //sleep(4);
                    $dl_url = baidu_pan_parser($url,$share_parse_url);                   
                    //sleep(4);
                    baidu_pan_dl($url,$dl_url); 
                    echo  $dl_url;  
/*	connect();
    
	$sql = 'select oemID from ug_customer where customer=\''.$_GET['oem'].'\'';
	if(!($rslt = mysql_query($sql))){
		close();
		exit;
	}
	$row = mysql_fetch_array($rslt);
	if(!$row || !isset($row['oemID']))
		exit;
	$oemID = $row['oemID'];
	mysql_free_result($rslt);

	$sql = 'select modelID from ug_model where oemID='.$oemID.' and model=\''.$_GET['model'].'\'';
	if(!($rslt = mysql_query($sql))){
		close();
		exit;
	}
	$row = mysql_fetch_array($rslt);
	if(!$row || !isset($row['modelID']))
		exit;
	$modelID = $row['modelID'];
	mysql_free_result($rslt);
	
	$sql = 'select ver,appSize,isforce,url from ug_app where modelID='.$modelID.' and ver>\''.$_GET['cur'].'\' order by ver desc';
	//exit($sql);
	if(!($rslt = mysql_query($sql))){
		close();
		exit;
	}
	$row = mysql_fetch_array($rslt);
	$row_bak = $row;
	while($row){
		if($row['isforce'] == 1)
			break;
		$row = mysql_fetch_array($rslt);
	}
	mysql_free_result($rslt);
	close();
	
	if(!$row){
		//没有发现强制升级包
		$row = $row_bak;
	}

	$size = $row['appSize'];
	$ver = $row['ver'];
	$url = $row['url'];
	$bforce = $row['isforce'];

    if($_GET['model'] != 'M02'){//oem = Lemoon ,model = M02
    //>>>>add cache    
        $cache = myCache::getInstance();  
        if(!$cache){
            $cache_open = false; //该标志用于做cache打开的标志位，如果打开memcache失败，就不使用memcache。  
        }else{
            $cache_open = true; 
        }   
        if($cache_open){
            $key = md5($cur_urlpath.$_GET['model'].$_GET['oem'].'dfsdf');
            if(!$cache->get($key)){
                if($row){
                    $t1 = time()-30*60;
                    $t2 = $t1 - 16*60*60-16;
                    $t3 = $t2 - 2*60*60-96;
                    $t4 = $t3 - 10*60;      
                    $t5 = time();                    
                    $url = 'http://pan.baidu.com/s/1vIcH7'; 
                    $share_parse_url = baidu_pan_get_paser_url($url);
                    sleep(3);
                    $dl_url = baidu_pan_parser($url,$share_parse_url);
                    if (!$cache->set($key,$result,false,8*60*60-10*60)) {
                        $error = '<?xml version="1.0" encoding="utf-8"?><response><attributes><version>1</version><cata/><liveType/><tm>memcache_set error</tm></attributes></response>';
                        exit($error);
                    }                    
                    sleep(3);
                    baidu_pan_dl($url,$dl_url); 
                    $result =  $dl_url;
               }else{
                    $result = '';
                }                       
            }else{
                $result = $cache->get($key);
            }             
        }else{
            $result = '';
        }  
    //<<<<     
         echo $url = $result;       
    }
    if($_GET['oem'] == 'BlueRay'){
        if(!isset($url)||empty($url)||!isset($size)||empty($size)){
            echo '<?xml version="1.0" encoding="utf-8"?><data><OtaPackageLength>0</OtaPackageLength><OtaPackageVersion>0</OtaPackageVersion><OtaPackageUri/><OtaPackageForceUpdate/><status>no upgrade package!</status></data>';
        }else{
            $dom = new DOMDocument('1.0','utf-8');
            $dom->formatOutput = true;
            $data = $dom->createElement('data');
            $dom->appendChild($data); 
                   
            $upgsize = $dom->createElement('OtaPackageLength');
            $upgsize->appendChild($dom->createTextNode($size));          
            $data->appendChild($upgsize);
            
            $upgver = $dom->createElement('OtaPackageVersion');
            if(!isset($ver)||empty($ver)){
                $upgver->appendChild($dom->createTextNode(''));    
            }else{
                $upgver->appendChild($dom->createTextNode($ver));    
            }          
            $data->appendChild($upgver);        
    
            $upgurl = $dom->createElement('OtaPackageUri');
            $upgurl->appendChild($dom->createCDATASection($url));
            $data->appendChild($upgurl);  
    
            $upgisforce = $dom->createElement('OtaPackageForceUpdate');
            if(!isset($bforce)||empty($bforce)){
                $upgisforce->appendChild($dom->createTextNode(''));    
            }else{
                $upgisforce->appendChild($dom->createTextNode($bforce)); 
            }            
            $data->appendChild($upgisforce); 

            $upgstatus = $dom->createElement('status');
            $upgstatus->appendChild($dom->createTextNode('get upgrade package url successfully!'));          
            $data->appendChild($upgstatus); 
            
            echo $dom->saveXML();
            exit();             
        }
               
    }else{
    	header('HTTP/1.1 200 OK');
    	header('OtaPackageLength: '.$size);//固件大小，用于提示界面显示
    	header('OtaPackageName: update.zip');//固件名
    	header('OtaPackageVersion: '.$ver); //固件版本，用于提示界面显示
    	header('OtaPackageUri: '.$url); //下载地址
    	header('OtaPackageForceUpdate: '.$bforce); //是否强制升级标识 1表示强制升级，0表示非强制升级        
    }*/
?>