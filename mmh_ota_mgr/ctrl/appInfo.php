<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<script language="javascript">   		
	//提交内容预处理
	function pre_submit(){
	        $r = confirm("警告！！ 您正在执行删除操作，删除后将无法恢复！！ 请确认是否执行删除……");
            if(!$r) {
               return false;
            }			
	}  		   			
</script>
</head>

<?php
	require('./connect.php');
?>

<body>
	<div style="line-height:50%" align="center">  <h2 class="tlcls">在线升级管理系统</h2> by MMH </div>
    <br /><hr />
<div>
	<a href=<?php echo '"addApp.php?oid='.$_GET['oid'].'&mid='.$_GET['mid'].'"'; ?> ><span  style="vertical-align:middle;"><img src="img/add.png" width="18" height="18" />新增</span></a>
</div>
<br />
  <table width="100%" border="1"  cellpadding="0" cellspacing="0" bordercolor="#07CCD3"  style="TABLE-LAYOUT: fixed" >
<tr  align="center" >
            <th style="WORD-WRAP: break-word;width:50px" scope="col">操作</th>
            <th style="WORD-WRAP: break-word;width:70px" scope="col">适应版本</th>
            <th style="WORD-WRAP: break-word;width:70px" scope="col">目标版本</th>
        	<th style="WORD-WRAP: break-word;width:90px" scope="col">时间</th>
            <th style="WORD-WRAP: break-word;width:50px" scope="col">大小</th>
            <!--
            <th style="WORD-WRAP: break-word;width:40px" scope="col">强制</th>
            -->
            <th style="WORD-WRAP: break-word;width:160px" scope="col">hash</th>
            <th style="WORD-WRAP: break-word;width:500px" scope="col">地址</th>
            <th style="WORD-WRAP: break-word" scope="col">描述</th>
         </tr>
        
	<?php
		connect();
		$sql = 'select appID,modelID,cur_ver,appSize,hashCode,target_ver,url,intro,tm from ug_app where modelID='.$_GET['mid'].' order by cur_ver desc, tm asc';
		$rslt = mysql_query($sql) or die();	
		
		$str='';
		while($row=mysql_fetch_array($rslt)){
			$str =$str. '<tr  align="center" >'."\n";
            $str = $str.'<td><form action="admin_data.php?ac=del_app&oid='.$_GET['oid'].'&mid='.$row['modelID'].'" target="_self" method="post"><input type="hidden" value='.$row['appID'].' name=appid><input type="submit" value="删除" onClick="return pre_submit()"></form></td>'."\n";
			$str = $str.'<td style="WORD-WRAP: break-word" >'.$row['cur_ver'].'</td>'."\n";		
            $str = $str.'<td style="WORD-WRAP: break-word" >'.$row['target_ver'].'</td>'."\n";		
			$str = $str.'<td style="WORD-WRAP: break-word" >'.$row['tm'].'</td>'."\n";			
			$str = $str.'<td style="WORD-WRAP: break-word" >'.$row['appSize'].'</td>'."\n";
			//$str = $str.'<td style="WORD-WRAP: break-word" >'.(($row['isforce']==1)?'是':'否').'</td>'."\n";			
			$str = $str.'<td style="WORD-WRAP: break-word" >'.$row['hashCode'].'</td>'."\n";
			$str = $str.'<td style="WORD-WRAP: break-word" >'.$row['url'].'</td>'."\n";
			$str = $str.'<td style="WORD-WRAP: break-word" align="left" >'.$row['intro'].'</td>'."\n";
			$str = $str.'</tr>'."\n";
		}
		mysql_free_result($rslt);
	  	close();

		echo $str;
	?>
</table>
    <br />    



</body>
</html>