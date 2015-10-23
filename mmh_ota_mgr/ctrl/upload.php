<?php
	set_time_limit (0);
	ignore_user_abort(false);
	
	function mkdirs($dir)  {  
		if(!is_dir($dir)) {  
			if(!mkdirs(dirname($dir))){  
				return false;  
			}  
			if(!mkdir($dir,0777)){  
				return false;  
			}  
		}  
		return true;  
	} 
?>

<?php
    $urlPre = '';// 
    
    
	if(!isset($_GET['oem']) || strlen($_GET['oem'])===0
		|| !isset($_GET['model']) || strlen($_GET['model'])===0 
		|| !isset($_GET['mid']) || strlen($_GET['mid'])===0 )
		die('参数不全！！！');

	if ($_FILES["file"]["error"] > 0) {
	  die( "Error: " . $_FILES["file"]["error"] . "<br />");
	}
	else {
		$version = $_POST['ver'];
		$bforce = $_POST['RadioGroup1'];
		$intro = $_POST['descr'];
		$hashCode = '';
		
		$basePath = '/var/www/APPS';
		//$basePath = '/var/www/DownLoad/Multi';

		$oem = strtr($_GET['oem'], ' ', '_');
		$model = strtr($_GET['model'], ' ', '_');
		$destPath = $basePath.'/'.$oem.'/'.$model;
		
		$destName = $version.'.zip';	
		$destfullPath = $destPath.'/'.$destName;
		$urlPath = $urlPre .'/'.$oem.'/'.$model.'/'.$destName;
		
		echo "modelID: " . $_GET['mid'] . "<br />";
		echo "ver: " . $version . "<br />";
		echo "appSize: " . ($_FILES["file"]["size"]) . "<br />";
		echo "hashCode: " . '空' . "<br />";
		echo "isforce: " . $bforce . "<br />";
		echo "url: " . $urlPath . "<br />";
		echo "intro: " . $intro . "<br />"; 
		
	/*
		
		echo "Upload: " . $_FILES["file"]["name"] . "<br />";
		echo "Type: " . $_FILES["file"]["type"] . "<br />";
		echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";	
*/
		//检测并创建目录
		mkdirs($destPath);	
		
		if (file_exists($destfullPath)){
			echo $destfullPath . " already exists. ";
		}
		else{
			move_uploaded_file($_FILES["file"]["tmp_name"], $destfullPath);
			chmod($destfullPath,0777);
			echo "Stored in: " . $destfullPath.' successful!<br>';
			
			include 'connect.php';
			connect();
			$sql = 'insert into ug_app(modelID,ver,appSize,hashCode,isforce,url,intro) values ('.$_GET['mid'].',\''.$version.'\','.$_FILES["file"]["size"].',\''.$hashCode.'\','.$bforce.',\''.$urlPath.'\',\''.$intro.'\')';
			mysql_query($sql) or die('入库信息失败'.mysql_error());
			close();
			echo '上传成功！<br>';
			
		}
	}
?>