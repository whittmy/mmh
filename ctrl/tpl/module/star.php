<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
$col_star=array('s_id', 's_name', 's_enname', 's_letter', 's_pic', 's_sex', 's_height', 's_area', 's_blood', 's_constellation', 's_birthday', 's_birthplace', 's_job', 's_hits', 's_dayhits', 's_weekhits', 's_monthhits', 's_addtime', 's_time', 's_hitstime','s_hide' ,'s_content');



elseif($method=='starinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$id=$p['id'];
	$flag=empty($id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_art;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}star where s_id='.$id);
		if($row){
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	
	

	$arr=array(
		array('a'=>'hide','c'=>$valarr['a_hide'],'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('a'=>'level','c'=>$valarr['a_level'],'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'type','c'=>$valarr['a_type'],'n'=>$typearrn,'v'=>$typearrv)
	);
	foreach($arr as $a){
		$colarr=$a['n'];
		$valarr=$a['v'];
		$rn=$a['a'];
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? 'selected': '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	unset($colarr);
	unset($valarr);
	unset($topicarr);
	unset($typearr);
}

else
{
	showErr('System','未找到指定系统模块');
}
?>