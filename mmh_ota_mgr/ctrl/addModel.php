<?php
    require('./connect.php');
?>

<?php
	if(!isset($_GET['oid']) || strlen($_GET['oid'])===0){
		if(isset($_GET['oid']))
			echo('set!!!<br>');
		if(strlen($_GET['oid'])===0)
			echo('empty<br>');	
		exit('未指定oem！');
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
    <title>添加机型</title>
    <script src='./js/jquery-2.1.4.min.js'></script>
    
    <script language="javascript">
        $(function(){
            $(".models").click(function(){
                modelname = $(this).html();
                 if(confirm('您确定要删除该型号( '+modelname+' )?')){
                     //alert('已删除');
                     modelid = $(this).attr('mid');
                     $.ajax({
                        type: 'get',
                        cache: false,
                        dataType: 'json',
                        url: "admin_data.php?ac=del_model&mid="+modelid,
                        timeout: 3000,
                        success:function(r){
                            if(r.status == 'ok'){
                                //alert('删除成功!');
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
			var model = document.getElementById("addmodel");
			var chip = document.getElementById("chip");
			if(model.value.length == 0){
				alert('请输入机型名');
				model.focus(); 
				return false; 			
			}
			if(chip.value.length == 0){
				alert('请输入芯片名');
				chip.focus(); 
				return false; 			
			}			
			if(modelArr.in_array(model.value)){
				alert('机型已存在')
				model.value = '';
				model.focus(); 
				return false;	
			}
			alert("提交成功");
			setTimeout(reloadPage, 300);	
			return true;
		}

		
		//弹框完，重新刷新下本页
		function reloadPage(){
			window.location.reload();
			window.parent.leftFrame.location.reload(); 		
		}    
		
    </script>
    
</head>
<body>
	<div style="line-height:50%" align="center">  <h2 class="tlcls">在线升级管理系统</h2> by MMH </div>
    <br /><hr /><br />    
    <div>
    	<?php
			connect();
    		mysql_query('begin');
			
			$chipId = null;
			if((isset($_POST['chip']) && strlen($_POST['chip'])>0)
			   && (isset($_POST['addmodel']) && strlen($_POST['addmodel'])>0))
			{
				$sql = 'select chipID from ug_chip where chip=\''.$_POST['chip'].'\'';
				//exit($sql);
				$rslt = mysql_query($sql) or die(mysql_error().'<br>');
				$row = mysql_fetch_array($rslt);
				if($row && isset($row['chipID'])){
					$chipId = $row['chipID'];	
					mysql_free_result($rslt);
				}
				else{
					$sql = 'insert into ug_chip(chip) values (\''.$_POST['chip'].'\')';	
					//exit($sql);
					mysql_query($sql) or die(mysql_error().'<br>');
					$sql = 'SELECT LAST_INSERT_ID() as chipID';
					$rslt = mysql_query($sql) or die(mysql_error().'<br>');
					$row = mysql_fetch_array($rslt);
					if($row && isset($row['chipID'])){
						$chipId = $row['chipID'];	
						mysql_free_result($rslt);
					}					
				}
				
				if(strlen($chipId) === 0){
					mysql_query('rollback');
					mysql_query('end');
					close();
					exit ('false1');	
				}	
								
				$sql = 'insert into ug_model(model,oemID,chipID) values (\''.$_POST['addmodel'].'\','.$_GET['oid'].','.$chipId.')';	
				$rslt = mysql_query($sql) or die(mysql_error().'<br>');
				mysql_query('commit');
				mysql_query('end');
				close();
				exit ('true');				
			}		
			else{
				//客户
				$jsArea = '<script language="javascript">'."\n".'var modelArr=new Array();'."\n";
				$str = '';
				$oemName = '';
				$sql='select t1.customer, t2.model, t2.modelID from ug_customer t1 left join ug_model t2 on t1.oemID=t2.oemID where t1.oemID='.$_GET['oid'].' order by t2.modelID';
				$rslt = mysql_query($sql)  or die('1.'.mysql_error().'<br>');	
				$i = 1;
				while($row = mysql_fetch_array($rslt)){
					//$maxOemId = $row['oemID'];	
					$oemName = $row['customer'];
					$modelName = $row['model'];
                    $modelid = $row['modelID'];
					if(strlen($modelName)>0)
						$jsArea = $jsArea.'modelArr.push("'.$modelName.'");'."\n";
					$str = $str.'<u class="models" mid='.$modelid.' >'.$modelName.'</u>'."&nbsp;";
					
					if($i === 10){
						$str = $str.'<br>';
						$i = 0;
					}
					$i++;						
				}
				mysql_free_result($rslt);
				
				$jsArea = "\n\n".$jsArea.'</script>';	
				if(strlen($str) == 0 || $str=='<u></u>&nbsp;')
					$str = '无';
				
				if(strlen($oemName) == 0)
					$oemName = '请先添加客户';	
				echo '    客户：'.$oemName;
				echo '<br>';
				echo '    机型：'.$str;
				echo $jsArea;		
			}
			close();
    	?>
	
</div>

    <br />
    <form id="form1" name="form1" method="post" action=<?php echo '"addModel.php?oid='.$_GET['oid'].'"';?> target="out_hide" onsubmit="return pre_submit();">
      <span> 机型名： <input type="text" name="addmodel" id="addmodel" /></span><br />
       <span> <!--芯片名： --><input type="hidden" name="chip" id="chip" value='unkown' /></span><br />
      <input name="" type="submit" value="提交" />
    </form>
	<script language="javascript">
		document.getElementById("addmodel").focus();
	</script>
<br />
<!---				-->
<iframe name="out_hide" id="out_hide" width=300 style="visibility:hidden"></iframe>
</body>
</html>