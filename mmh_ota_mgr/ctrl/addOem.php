<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
    <title>添加客户</title>
    
    <script src='./js/jquery-2.1.4.min.js'></script>
    <script language="javascript">
    $(function(){
        $(".deloem").click(function(){
            oemname = $(this).html();
            if(confirm('您确定要删除该客户( '+oemname+' )?')){
                oid = $(this).attr('oid');
                $.ajax({
                   type: 'get',
                   cache: false,
                   dataType: 'json',
                   url: "admin_data.php?ac=del_oem&oid="+oid,
                   timeout: 3000,
                   success:function(r){
                       if(r.status == 'ok'){
                           window.location.reload();
                           window.parent.leftFrame.location.reload(); 	
                       }
                       else{
                           alert('删除失败:'+ r.status);
                       }
                   }
               }); 
            }
        });
        
    });
    </script>
    
    
	<script language="javascript">
		Array.prototype.in_array = function(e)
		{
			for(i=0;i<this.length && this[i]!=e;i++);
			return !(i==this.length);
		}
	
		//提交内容预处理
		function pre_submit(){
			var inText = document.getElementById("adduser").value;
			if(inText.length == 0){
				alert('请输入客户名');
				document.getElementById("adduser").focus(); 
				return false; 			
			}
			if(oemArr.in_array(inText)){
				alert('客户名已存在')
				document.getElementById("adduser").value = '';
				document.getElementById("adduser").focus(); 
				return false;	
			}
			
			document.form1.submit();
			alert("提交成功");
			setTimeout(reloadPage, 300);	
			return true;
		}
		
		//弹框完，重新刷新下本页
		function reloadPage(){
			window.location.reload(); 	
			window.parent['leftFrame'].location.reload(); 						
		}    
		
		document.getElementById("adduser").focus();
    </script>
    
</head>
<body>
	<div style="line-height:50%" align="center">  <h2 class="tlcls">在线升级管理系统</h2> by MMH </div>
    
    <br /><hr /><br />
	<?php
		require('./connect.php');
    ?>

    <div>
    已有客户：
    	<?php
			connect();
    
			if(isset($_GET['adduser']) && strlen($_GET['adduser'])>0){
				//exit('get '.$_GET['adduser']);
				$sql = 'insert into ug_customer(customer) values (\''.$_GET['adduser'].'\')';	
				$rslt = mysql_query($sql) or die(mysql_error().'<br>');
				exit( 'ok');
			}			
			else{
				//客户
				$jsArea = '<script language="javascript">'."\n".'var oemArr=new Array();'."\n";
				$str = '';
				$sql='select oemID,customer from ug_customer order by oemID';
				$rslt = mysql_query($sql)  or die('1.'.mysql_error().'<br>');	
				$i = 1;
				while($row = mysql_fetch_array($rslt)){
					$oemId = $row['oemID'];	
					$oemName = $row['customer'];
					$jsArea = $jsArea.'oemArr.push("'.$oemName.'");'."\n";
					$str = $str.'<u class="deloem" oid='.$oemId.'>'.$oemName.'</u>'."&nbsp;";
					
					if($i === 10){
						$str = $str.'<br>';
						$i = 0;
					}
					$i++;						
				}
				mysql_free_result($rslt);
				
				$jsArea = "\n\n".$jsArea.'</script>';	
				if(strlen($str) == 0)
					$str = '无';
				echo $str;
				echo $jsArea;		
			}
			close();
    	?>
	
    </div>
    <br />
    <form id="form1" name="form1" method="get" action="addOem.php" target="out_hide" onsubmit="return pre_submit();">
      <span> 客户名： <input type="text" name="adduser" id="adduser" /></span>
      <input name="" type="submit" value="提交" />
    </form>
	<script language="javascript">
		document.getElementById("adduser").focus();
	</script>
<br />

<iframe name="out_hide" id="out_hide" width=300 style="visibility:hidden"></iframe>
</body>
</html>