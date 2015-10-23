<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
    <title></title>

    <link rel="StyleSheet" href="style/dtree.css" type="text/css" />

    <script type="text/javascript" src="js/dtree.js"></script>

</head>

<body>
	<?php
        require('./connect.php');
    ?>
    
    <p><a href="javascript: d.openAll();">全部展开</a>|<a href="javascript: d.closeAll();">全部关闭</a></p>
    <div class="dtree">
    	<?php
			//取客户和机型信息
			close();
            connect();
		
			$maxOemId = 0;			
			$dtreeStr = '<script type="text/javascript">'."\n".'<!--'."\n";
			//$dtreeStr = '';
			$dtreeStr = $dtreeStr.'d = new dTree("d");'."\n".'d.add(10000,-1,"所有客户机型");';	

            $num = 0;
            $sql = 'select max(oemID) maxid from ug_customer';
            $rslt = mysql_query($sql);
            if($row = mysql_fetch_array($rslt)){
                $num = $row['maxid']+1;
                mysql_free_result($rslt);
            }
            
			//客户
			$sql='select oemID,customer from ug_customer order by oemID';
			$rslt = mysql_query($sql)  or die('1.'.mysql_error().'<br>');	
			//$num = mysql_num_rows($rslt);
			while($row = mysql_fetch_array($rslt)){
				
				$oemName = $row['customer'];
				//d.add($maxOemId, 0, "$oemName");	
				$dtreeStr = $dtreeStr."\n".'d.add('.$row['oemID'].',10000,"'.$oemName.'");';

				$maxOemId = $row['oemID'] + $num;
				$target = 'mainframe.php?oid='.$row['oemID'].'&mid=-1';
				$dtreeStr = $dtreeStr."\n".'d.add('.$maxOemId.', '.$row['oemID'].', "'.'-机型管理-'.'", "'.$target.'", "", "mainFrame");';
			}
            //exit($dtreeStr);
			mysql_free_result($rslt);
			$maxOemId++;
			$dtreeStr = $dtreeStr."\n".'d.add('.($maxOemId+1).',10000,"-客户管理-","mainframe.php?oid=-1","点击可以添加新的客户","mainFrame");';
			
			//机型
			$sql = 'select oemID as oid, modelID as mid, model from ug_model order by oemID,modelID asc';
			$rslt = mysql_query($sql) or die('2.'.mysql_error().'<br>');

			while($row = mysql_fetch_array($rslt)){
				$maxOemId++;
				$pid=$row['oid'];
				$ndname=$row['model'];

				$target = 'mainframe.php?oid='.$row['oid'].'&mid='.$row['mid'];
				//d.add($maxOemId, $pid, "$ndname", "$target", "", "mainFrame");
				$dtreeStr = $dtreeStr."\n".'d.add('.$maxOemId.', '.$pid.', "'.$ndname.'", "'.$target.'", "", "mainFrame");';			
			}
			mysql_free_result($rslt);
			close();			

			$dtreeStr = $dtreeStr."\n".'document.write(d);';
			$dtreeStr = $dtreeStr."\n".'//-->'."\n".'</script>'."\n";
			echo $dtreeStr;
    	?>
    
    	<script type="text/javascript">
            <!----	
			
//                d = new dTree("d");
//        
//                d.add(10000,-1,'所有客户机型');
//                d.add(1,10000,"Node 1",'mainframe.php');
//				
//                d.add(2,10000,'Node 2','mainframe.php');
//                d.add(3,1,'Node 1.1','mainframe.php');
//                d.add(4,10000,'Node 3','mainframe.php');
//                d.add(5,3,'Node 1.1.1','mainframe.php');
//                d.add(6,5,'Node 1.1.1.1','mainframe.php');
//                d.add(7,0,'Node 4','mainframe.php');
//                d.add(8,1,'Node 1.2','mainframe.php','','mainFrame');
//                d.add(9,0,'My Pictures','mainframe.php','Pictures I\'ve taken over the years','','','img/imgfolder.gif');
//                d.add(10,9,'The trip to Iceland','mainframe.php','Pictures of Gullfoss and Geysir');
//                d.add(11,9,'Mom\'s birthday','mainframe.php');
//                d.add(12,0,'Recycle Bin','mainframe.php','','','img/trash.gif');
//        
//	            document.write(d);
				
            //-->
		</script>
    </div>
</body>
</html>