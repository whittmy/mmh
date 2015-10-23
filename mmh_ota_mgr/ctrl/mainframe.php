<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
</head>

<body>
	<h2 align="center">在线升级管理系统</h2>
    
    <?php
		function addOem(){
			$str = '<script language="javascript">'."\n";
			$str = $str.'self.location=\'addOem.php\''."\n";
			$str = $str.'</script>'."\n\n";
			echo $str;
		}
		
		function addModel($oemId){
			$str = '<script language="javascript">'."\n";
			$str = $str.'self.location=\'addModel.php?oid='.$oemId.'\''."\n";
			$str = $str.'</script>'."\n\n";
			echo $str;			
		}
		
		function showAppInfo($oemId, $mid){
			$str = '<script language="javascript">'."\n";
			$str = $str.'self.location=\'appInfo.php?oid='.$oemId.'&mid='.$mid.'\''."\n";
			$str = $str.'</script>'."\n\n";
			echo $str;				
			
		}
		
	?>
    
    
	
	<?php
		if(isset($_GET['oid'])){
			if($_GET['oid']==-1 || empty($_GET['oid'])){
				//新增客户	
				//exit('新增客户');
				addOem();
			}
			else if(isset($_GET['mid'])){
				if($_GET['mid'] == -1  || empty($_GET['mid'])){
					//新增机型	
					addModel($_GET['oid']);
				}
				else{
					//查询机型的软件版本
					showAppInfo($_GET['oid'], $_GET['mid']);	
				}
			}
			else{
				exit('机型信息无效');	
			}
		}
		else{
			exit('欢迎光临');	
		}

    ?>	

</body>

</html>