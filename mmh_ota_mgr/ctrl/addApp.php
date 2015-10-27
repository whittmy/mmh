<?php
	if(!isset($_GET['oid']) || strlen($_GET['oid'])===0
	  || !isset($_GET['mid']) || strlen($_GET['mid'])===0)
		die('缺少参数信息！');
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script language="javascript">
		Array.prototype.in_array = function(e)
		{
			for(i=0;i<this.length && this[i]!=e;i++);
			return !(i==this.length);
		}
		
		function getRadioValue(name){
			var radioes = document.getElementsByName(name);
			for(var i=0;i<radioes.length;i++)
			{
				if(radioes[i].checked){
					return radioes[i].value;
				}
			}
			return false;
		}	

   		
		//提交内容预处理
		function pre_submit(){
            var target_ver = document.getElementById("target_ver");
			var cur_ver = document.getElementById("cur_ver");
			var intro = document.getElementById("descr");
			var iforce = getRadioValue("RadioGroup1");
            //var iupgurl = getRadioValue("uploadupg");

		
			var patrn=/^\d{2}\.\d{2}\.\d{2}$/; 
            if((cur_ver.value!='' && !patrn.exec(cur_ver.value)) || !patrn.exec(target_ver.value) ) {
                alert('版本号格式不正确');
                //model.focus(); 
                return false; 			
            }
            if(cur_ver.value != '' && verArr.in_array(cur_ver.value)){
                alert('填写的版本号已存在')
                document.getElementById("cur_ver").value = '';
                document.getElementById("cur_ver").focus(); 
                return false;	
            }	
			
			
			//if(iforce === false){
			//	alert('选择是否强制升级');
			//	chip.focus(); 
			//	return false; 			
			//}
            //
			//if(iupgurl === false){
			//	alert('请上传文件或提供文件url');
			//	chip.focus(); 
			//	return false; 			
			//}            	
			
            //if(iupgurl.value == 'Y'){
                var upgurl = document.getElementById("upg_url");
                var file='';
                if(upgurl.value.indexOf("http") == -1){
                    alert('非法url');
                    //chip.focus(); 
				    return false;                     
                }                
                
           //}else if(iupgurl.value == 'N'){
           //    var file = document.getElementById("file");
           //    var upgurl = '';
    		//	if(file.value.length == 0){
    		//		alert('请选择上传的升级包');
    		//		return false;	
    		//	}                
           //}             							
			
            //setTimeout(reloadPage, 300);				
			document.form1.submit.value="正在提交,请稍后";
			document.form1.submit.disabled=true;	
            //alert('begin to submit');
            return true;
		}		   		
		
	</script>
</head>

<?php
	require('./connect.php');
	connect();
	
	$oemName='';
	$sql = 'select customer from ug_customer where oemID='.$_GET['oid'];
	$rslt = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($rslt);
	if($row && isset($row['customer'])){
		$oemName = $row['customer'];
		mysql_free_result($rslt);
	}
	else{
		close();
		die('错误的客户信息！！');
	}
		
	$modelName='';		
	$sql = 'select model from ug_model where modelID='.$_GET['mid'];
	$rslt = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($rslt);
	if($row && isset($row['model'])){
		$modelName = $row['model'];
		mysql_free_result($rslt);
	}
	else{
		close();
		die('错误的机型信息！！');
	}	
		
	if(strlen($oemName)===0 || strlen($modelName)===0){
		close();
		die('系统客户或机型信息错误');
	}
	
	$jsArea = '<script language="javascript">'."\n".'var verArr=new Array();'."\n";
	$verstr = '';	
		
	$sql = 'select cur_ver from ug_app where modelID='.$_GET['mid'].' order by cur_ver desc';	
	$rslt = mysql_query($sql)  or die('1.'.mysql_error().'<br>');	
	$i=1;
	while($row = mysql_fetch_array($rslt)){
		$cur_ver = $row['cur_ver'];
		$jsArea = $jsArea.'verArr.push("'.$cur_ver.'");'."\n";
		$verstr = $verstr.'<u>'.$cur_ver.'</u>'."&nbsp;";
		if($i === 10){
			$verstr = $verstr.'<br>';
			$i = 0;
		}
		$i++;
	}
	mysql_free_result($rslt);
	
	$jsArea = "\n\n".$jsArea.'</script>';	
	if(strlen($verstr) == 0)
		$verstr = '无';
	//echo $verstr;
	echo $jsArea;	
	close();		
?>

	<div style="line-height:50%" align="center">  <h2 class="tlcls">在线升级管理系统</h2> by MMH </div>
    
    <br /><hr /><br />
<!--
需要设置：post_max_size，upload_max_filesize, 上传的临时文件夹upload_tmp_dir  --->
<br /><br />

<form id="form1" name="form1" enctype="multipart/form-data" method="post" action=<?php echo '"admin_data.php?ac=add_app&oid='.$_GET['oid'].'&oem='.$oemName.'&model='.$modelName.'&mid='.$_GET['mid'].'"'; ?> target="_self"  onsubmit="return pre_submit();" >
<table width="150%" border="1" cellspacing="3" cellpadding="0">
  <tr height="40">
    <td width="11%">客户：</td>
    <td width="89%"><?php echo $oemName; ?></td>
  </tr>
  <tr height="40">
    <td width="11%">机型：</td>
    <td width="89%"><?php echo $modelName; ?></td>
  </tr>
  <tr height="40">
    <td width="11%">现有版本：</td>
    <td width="89%"><?php echo $verstr; ?></td>
  </tr>
  <tr height="40">
    <td width="11%">适应版本：</td>
    <td width="89%"><input name="cur_ver" id="cur_ver" type="text" /> 如：02.20.30   (针对该版本进行升级,若全包则忽略)</td>
  </tr>
  <tr height=40>
     <td width="11%">目标版本:</td>
     <td width="89%"><input name="target_ver" id="target_ver" type="text" /> 如：02.20.30  （升级之后的版本号，必须）</td>
  </tr>
  <tr height=40>
    <td width="11%">大小：</td>
    <td width="89%"><input name="size" id="size" type="text" />
  </tr>
  <tr height=40>
     <td width="11%">HASH: </td>
     <td width="89%"><input name="hash" id="hash" type="text" size="32" /> </td>
  </tr>
  <!---
  <tr height="40">
    <td>强制升级：</td>
    <td>
          <input type="radio" name="RadioGroup1" value="1" id="RadioGroup1_0" /> 是
          <input type="radio" checked="checked" name="RadioGroup1" value="0" id="RadioGroup1_1" /> 否
    </td>
  </tr>
  --->
  <tr>
    <td>描述（选填）：</td>
    <td>
      <textarea name="descr" id="descr" cols="70" rows="5"></textarea> 
    </td>
  </tr>
  <!--
  <tr height="70">
    <td >是否上传升级包</td>
    <td >
          <input type="radio" name="uploadupg" value="N" id="uploadupg_0" onclick="document.getElementById('uploadfile').style.display='';document.getElementById('uploadurl').style.display='none';" /> 是
          <input type="radio" checked="checked" name="uploadupg" value="Y" id="uploadupg_1" onclick="document.getElementById('uploadfile').style.display='none';document.getElementById('uploadurl').style.display='';" /> 否
    </td>      
  </tr>
  <tr height="50"  style="display:none;" id="uploadfile">
    <td >上传升级包：</td>
    <td >
        <input type="file" id="file" name="file"/> 
    </td>   
  </tr>
  -->
  <tr height="50" id="uploadurl">
    <td >输入升级包存放路径：</td>
    <td >
        <input type="text" id="upg_url" name="upg_url" size="80" /> 
    </td>   
  </tr>  
</table>
 <input type="submit" name="submit" id="upload" value="提交" />
</form> 
 <br /> <br /> <hr />
<iframe id="noshow" width="100%" height="600" frameborder="0"></iframe>

<script language="javascript">
			
	function complate(){
		//回调方法
		alert('录入完成！');
		window.location.reload(); 	
	}
		
	//判断iframe是否读取完成
	function iframeLoaded(iframeEl,callback) {
		if(iframeEl.attachEvent) {
			iframeEl.attachEvent("onload", function() {
				if(callback && typeof(callback) == "function") {
					callback();
				}
			});
		} else {
			iframeEl.onload = function() {
				if(callback && typeof(callback) == "function") {
					callback();
				}
			}
		}
	}
	

//   iframeLoaded(document.getElementById("noshow"),complate) ;
	   
</script>