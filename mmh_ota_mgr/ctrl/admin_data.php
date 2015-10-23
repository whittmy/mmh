<?php
    require('./connect.php');
    
    $ac = $_GET['ac'];
    switch($ac){
        case 'del_app':
            $appid = $_POST['appid'];
            $modelId = $_GET['mid'];
            $oid = $_GET['oid'];
            connect();
            $sql = 'delete from ug_app where appID="'.$appid.'"';
            $rslt = mysql_query($sql) or die(mysql_error());
            close();   

            //if($rslt){
            //   echo '<div style="line-height:50%" align="center">  <h2 class="tlcls">É¾³ý³É¹¦</h2></div>';
            //}else{
            //   echo '<div style="line-height:50%" align="center">  <h2 class="tlcls">É¾³ýÊ§°Ü</h2></div>';
            //} 
            header('Location: '. "appInfo.php?mid=$modelId&oid=$oid");
            break;
        case 'add_app':
            //$oem = $_GET['oem'];
            //$model = $_GET['model'];
            $oid = $_GET['oid'];
            $modelId =      isset($_GET['mid'])? $_GET['mid'] : '';
            $cur_ver =      isset($_POST['cur_ver'])? $_POST['cur_ver'] : '';
            $target_ver =   isset($_POST['target_ver'])? $_POST['target_ver'] : '';
            $hash =         isset($_POST['hash'])? $_POST['hash'] :'';
            $descr =        isset($_POST['descr'])? $_POST['descr'] : '';
            $size =         isset($_POST['size'])? $_POST['size'] :'';
            $upg_url =      isset($_POST['upg_url'])? $_POST['upg_url'] :'';
            
            if(strlen($modelId)==0 
                || strlen($cur_ver)==0 
                || strlen($target_ver)==0
                || strlen($hash)==0
                || substr($upg_url, 0, 4)!='http'){
                    return;
                }
            connect();
            $sql = "insert into ug_app (modelID, cur_ver, target_ver,appSize,hashCode,url,intro) values ($modelId,'$cur_ver','$target_ver','$size','$hash','$upg_url','$descr')";
            mysql_query($sql) or  die(mysql_error()); 
            close();
            header('Location: '. "appInfo.php?mid=$modelId&oid=$oid");
            break;
        case 'del_oem':
            $oid = $_GET['oid'];
            connect();
            mysql_query('begin');
            
            $sql = 'select modelID from ug_model where oemID='.$oid;
            $rslt = mysql_query($sql) or  exit('{"status":"'.mysql_error().'"}'); 
            $mids = '';
            while($row = mysql_fetch_array($rslt)){
                $mids = $mids . $row['modelID'] . ',';
            }
            $mids = trim($mids, ',');
            
            $res1 = true;
            if(strlen($mids) > 0){
                $sql = "delete from ug_app where modelID in ($mids)";
                $res1 = mysql_query($sql) or  exit('{"status":"'.mysql_error().'"}'); 
            }
            
            $sql = 'delete from ug_customer where oemID='.$oid;
            $res2 = mysql_query($sql) or  exit('{"status":"'.mysql_error().'"}'); 
            
            $sql = 'delete from ug_model where oemID='.$oid;
            $res3 = mysql_query($sql) or  exit('{"status":"'.mysql_error().'"}'); 
            
            if($res1 && $res2 && $res3){
                mysql_query('commit');
            }
            else{
                mysql_query('rollback');
            }
            mysql_query('end');
            close();
            exit('{"status":"ok"}');
            break;
        case 'del_model':
            $mid = $_GET['mid'];
            connect();
            mysql_query('begin');
            $sql = 'delete from ug_model where modelID='.$mid;
            $res1 = mysql_query($sql) or  exit('{"status":"'.mysql_error().'"}'); 
            
            $sql = 'delete from ug_app where modelID='.$mid;
            $res2 = mysql_query($sql) or exit('{"status":"'.mysql_error().'"}'); 
            
            if($res1 && $res2){
                mysql_query('commit');
            }
            else{
                mysql_query('rollback');
            }
            mysql_query('end');
            close();
            exit('{"status":"ok"}');
            break;
    }
    //$s_conn
    //connect()
    
    //close()


?>