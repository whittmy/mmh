<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
$col_cata=array('t_id','t_name','t_enname','t_pid','t_hide','t_sort','t_tpl','t_tpl_list','t_tpl_vod','t_tpl_play','t_tpl_down','t_key','t_des','t_title');
$col_topic=array('t_id','t_name','t_enname','t_sort','t_tpl','t_pic','t_content','t_key','t_des','t_title','t_hide','t_level','t_up','t_down','t_score','t_scoreall','t_scorenum','t_hits','t_dayhits','t_weekhits','t_monthhits','t_addtime','t_time');
$col_vod=array('d_id', 'd_name','d_pids','d_episode', /*'d_epname','d_eppic',*/ 'd_subname', 'd_enname', 'd_letter', 'd_color', 'd_pic', 'd_picthumb', 'd_picslide', 'd_starring', 'd_directed', 'd_tag', 'd_remarks', 'd_area', 'd_lang', 'd_year', 'd_type', 'd_type_expand', 'd_class', 'd_hide', 'd_lock', 'd_state', 'd_level', 'd_usergroup', 'd_stint', 'd_stintdown', 'd_hits', 'd_dayhits', 'd_weekhits', 'd_monthhits', 'd_duration', 'd_up', 'd_down', 'd_score','d_scoreall', 'd_scorenum', 'd_addtime', 'd_time', 'd_hitstime', 'd_maketime', 'd_content','d_playnote' ,'d_playfrom', 'd_playserver' /*, 'd_playurl', 'd_downfrom', 'd_downserver', 'd_downnote', 'd_downurl'*/);

$col_class=array('c_id','c_name','c_pid','c_hide','c_sort');

if($method=='cata'){
	$plt->set_file('main', $ac.'_'.$method.'.html');
    //顶级分类 t_pid须为0
    // 获取 顶层分类个数
	$sql = 'SELECT count(*) FROM {pre}vod_cata where t_pid=0';
	$nums = $db->getOne($sql);
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_cata;
	array_push($colarr,'t_span','t_count');

    //处理模板html中模块 list_cate
	$rn='cata';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);

    //获取所有顶层类别
    //并做从缓存中获取各个类别的具体信息。
	$sql = 'SELECT * FROM {pre}vod_cata WHERE t_pid=0 ORDER BY t_sort,t_id ASC';
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs)){
        //遍历每个顶层分类，
		$t_count=0;
		$t_span='';
		$pidarr = $MAC_CACHE['vodcata'][$row['t_id']];
		if(is_array($pidarr)){ //必须为数组
			$ids = $pidarr['childids'];
            // rocking
			//$t_count = $db->getOne('SELECT count(*) FROM {pre}vod WHERE d_type in('.$ids.')');
            //获取顶层分类包含的所有的影片数量
            $t_count = $db->getOne('SELECT count(*) from {pre}vod_r_type_dir where r_cid in('.$ids.')');
		}
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n]; //顶层分类的所有属性存入 $valarr
		}
		$valarr['t_span']=$t_span;
		$valarr['t_count']=$t_count;
		
		for($i=0;$i<count($colarr);$i++){ //尼玛，有必要和上面的那个循环分开处理吗
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
		$plt->set_if('rows_'.$rn,'isparent',true);
		
		
		$sql = 'SELECT * FROM {pre}vod_cata WHERE t_pid = \''.$row['t_id'].'\' ORDER BY t_sort,t_id ASC';
		$rs1 = $db->query($sql);
		while ($row1 = $db ->fetch_array($rs1)){
			$t_count=0;
			$t_span='&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;';
			$valarr=array();
            //rocking
			//$t_count = $db->getOne('SELECT count(*) FROM {pre}vod WHERE d_type='.$row1['t_id']);
            $t_count = $db->getOne('SELECT count(*) from {pre}vod_r_type_dir where r_cid='.$row1['t_id']);
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row1[$n];
			}
			$valarr['t_span']=$t_span;
			$valarr['t_count']=$t_count;
			
			for($i=0;$i<count($colarr);$i++){
				$n = $colarr[$i];
				$v = $valarr[$n];
				$plt->set_var($n, $v );
			}
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
			if($row1['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
			$plt->set_if('rows_'.$rn,'isparent',false);
		}
		unset($rs1);
	}
	unset($colarr);
	unset($valarr);
	unset($rs);
}

elseif($method == 'device_res_mgr'){
    //先设置其对应的模板文件, 这个'main'不曾在模板中出现过
    $plt->set_file('main', $ac.'_'.$method.'.html');

    //分类处理>>>>>>>>>
    $pidarr = $MAC_CACHE['vodcata'];
    $pidarrn = array();
    $pidarrv = array();
    foreach($pidarr as $arr1){
        $s='&nbsp;|—';
        if($arr1['t_pid']==0){
            array_push($pidarrn,$s.$arr1['t_name']);
            array_push($pidarrv,$arr1['t_id']);
            foreach($pidarr as $arr2){
                if($arr1['t_id']==$arr2['t_pid']){
                    $s='&nbsp;|&nbsp;&nbsp;&nbsp;|—';
                    array_push($pidarrn,$s.$arr2['t_name']);
                    array_push($pidarrv,$arr2['t_id']);
                }
            }
        }
    }

    $plt->set_block('main', 'list_cata', 'rows_cata');

    for($i=0;$i<count($pidarrn);$i++){
        $n = $pidarrn[$i];
        $v = $pidarrv[$i];
        $plt->set_var('v', $v );
        $plt->set_var('n', $n );
        $plt->parse('rows_cata','list_cata',true);
    }
    unset($pidarrn);
    unset($pidarrv);
    //<<<<<<<<<<



    //处理 list_device_res_mgr模块
    $rn='device_res_mgr';
    $plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);

    $colarr = array('t_id', 't_name', 't_span');
    $valarr = array();

    $sql = 'select * from {pre}vod_device_res_mgr order by t_id';
    $rs = $db->query($sql);

    ///$t_span='';
    $hadrslt = false;
    while ($row = $db ->fetch_array($rs)){
        $hadrslt = true;
        $t_span='';
        foreach($colarr as $col){
            $valarr[$col] = $row[$col];   //每次外循环，数组的元素数是不变的，如果键存在则替换
        }
        $valarr['t_span'] = $t_span;

        //设置模板里面的相关的字段
        foreach($valarr as $key=>$val){
            $plt->set_var($key, $val);
        }

        //处理完一行就显示一行，将该句理解为，添加一行并显示, 多次执行就添加多行，当然若有数据修改，则显示不同的数据
        $plt->parse('rows_'.$rn,'list_'.$rn,true);

        //set_if要在parse之后调用，方可影响到parse新增的行
        //set_if处理 模板中 <!-- IF lv0 --> true-case <!-- ELSE lv0 --> false-case <!-- ENDIF lv0 -->部分的条件语句。以控制是否显示
        //若lv0为true,实际显示 false-case部分的代码
        //!!!! set_if 好像仅仅对刚刚新增的条目中的设置有效!!!!
        //另外:在模板中尽可能少用else的控制，因为显得代码多，尽可能分解为多个if条件，如上面的if-else可以分解为两个条件， IF lv0、IF lv1(只是举例，和下面无关)
        //条件部分的语句，如果不人为去设置，其默认都显示
        $plt->set_if('rows_'.$rn,'lv0',true);
        $plt->set_if('rows_'.$rn,'lv1',false);
        $plt->set_if('rows_'.$rn,'lv2',false);
        $plt->set_if('rows_'.$rn,'lv2_sub',false);
        unset($valarr);


        ///>>>>>>>>>>>>>>>>>>>>  添加附属行(如子类、全部) lv1   >>>>>>>>>>>>>>>>>
        $lv1Arr = array('sub'=>'子类', 'all'=>'全部');
        $cataArr = $MAC_CACHE['vodcata'];

        foreach($lv1Arr as $k1=>$v1){
            $valarr = array();

            //自动添加其下面的一行
            $t_span = '&nbsp;&nbsp;&nbsp;&nbsp;';

            $valarr['t_parent'] = $row['t_name'];
            $valarr['t_id'] = '';
            $valarr['t_name'] ='<b>'. $v1.'</b>';//'子类';
            $valarr['t_span'] = $t_span;
            $valarr['t_belong'] = $k1;
            $valarr['t_iid'] = $row['t_id'];
            foreach($valarr as $key=>$val){
                $plt->set_var($key, $val);
            }
            $plt->parse('rows_'.$rn,'list_'.$rn,true);
            $plt->set_if('rows_'.$rn,'lv0',false);
            $plt->set_if('rows_'.$rn,'lv1',true);
            $plt->set_if('rows_'.$rn,'lv2',false);
            $plt->set_if('rows_'.$rn,'lv2_sub',false);

            //>>>>>>>>>>>>>> lv2 sub >>>>>>>>>>>>>>>>>
            $t_span = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

            if($k1 == 'sub'){
                $t_clses = $row['t_subclses'];
                $t_desc = $row['t_sub_desc'];
            }
            else if($k1 == 'all'){
                $t_clses = $row['t_allclses'];
                $t_desc = $row['t_all_desc'];
            }

            if($k1 == 'all'){
                $t_clses =  str_replace('_', ',', $t_clses);
                $t_descArr =  str_replace('_', ',', $t_descArr);
            }
            $t_clsesArr = explode(',', $t_clses);
            $t_descArr = explode(',', $t_desc);

            //解析ids的数组
            foreach($t_clsesArr as $k=>$v){
                $namestr = '';
                if(is_numeric($v)){
                    $namestr = $cataArr[$v]['t_name'];
                }
                else{
                    if($v==' ' || strlen($v)==0){
                        continue;
                    }
                    $idarr = explode('_', $v);
                    foreach($idarr as $id){
                        $namestr = $namestr. $cataArr[$id]['t_name'].'_';
                    }
                    $namestr = trim($namestr, '_');
                }
                if(!empty($namestr)){
                    $valarr['t_id'] = $v;
                    $valarr['t_name'] = $namestr;
                    $valarr['t_alias'] = $t_descArr[$k];
                    $valarr['t_span'] = $t_span;
                    $valarr['t_belong'] = $k1;

                    foreach($valarr as $key=>$val){
                        $plt->set_var($key, $val);
                    }
                    $plt->parse('rows_'.$rn,'list_'.$rn,true);
                    $plt->set_if('rows_'.$rn,'lv0',false);
                    $plt->set_if('rows_'.$rn,'lv1',false);
                    $plt->set_if('rows_'.$rn,'lv2',true);
                    if($k1 == 'sub'){
                        $plt->set_if('rows_'.$rn,'lv2_sub',true);
                    }
                    else{
                        $plt->set_if('rows_'.$rn,'lv2_sub',false);
                    }
                }
            }

            unset($valarr);
            unset($t_clsesArr);
            unset($t_descArr);
        }

        //>>>>>>>>>>>>>>>> lv1 all >>>>>>>>>>>>>>>>>>>>>
    }

    if(!$hadrslt)
        $plt->set_if('main','isnull',true);
    else
        $plt->set_if('main','isnull',false);

    unset($colarr);
    unset($valarr);
    unset($rs);
}

elseif($method == 'restype'){
    $plt->set_file('main', $ac.'_'.$method.'.html');

    $rn='restype';
    $plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);

    $colarr = array('t_id', 't_name');
    $sql = 'select * from {pre}vod_restype';
    $rs = $db->query($sql);
    $hasdata = false;
    while ($row = $db ->fetch_array($rs)){
        foreach($colarr as $col){
            $plt->set_var($col, $row[$col]);
        }
        $hasdata = true;
        $plt->parse('rows_'.$rn,'list_'.$rn,true);
    }

    if($hasdata)
        $plt->set_if('main','isnull',false);
    else
        $plt->set_if('main','isnull',true);

}

elseif($method=='catasaveall')
{
	$t_id = be('arr','t_id');
	$ids = explode(',',$t_id);
	foreach($ids as $id){
		$t_name = be('post','t_name' .$id);
		$t_enname = be('post','t_enname' .$id) ;
		$t_sort = be('post','t_sort' .$id);
		$t_tpl = be('post','t_tpl' .$id);
		$t_tpl_vod = be('post','t_tpl_vod' .$id);
		$t_tpl_play = be('post','t_tpl_play' .$id);
		$t_tpl_down = be('post','t_tpl_down' .$id);
		
		if (isN($t_name)) { $t_name='未知';}
		if (isN($t_enname)) { $t_enname='weizhi';}
		if (!isNum($t_sort)) { $t_sort=0;}
		if (isN($t_tpl)) { $t_tpl = 'vodlist.html';}
		if (isN($t_tpl_vod)) { $t_tpl_vod = 'vod.html';}
		if (isN($t_tpl_play)) { $t_tpl_play = 'vodplay.html';}
		if (isN($t_tpl_down)) { $t_tpl_down = 'voddown.html';}
		
		$db->Update ('{pre}vod_cata',array('t_name','t_enname', 't_sort','t_tpl','t_tpl_vod','t_tpl_play','t_tpl_down'),array($t_name,$t_enname,$t_sort,$t_tpl,$t_tpl_vod,$t_tpl_play,$t_tpl_down),'t_id='.$id);
	}
	updateCacheFile();
	redirect( getReferer() );
}

elseif($method=='catainfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$t_id=$p['id'];
	$pid=$p['pid'];
	$flag=empty($t_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_cata;
	array_push($colarr,'flag','backurl');
	
	$valarr['t_tpl']='vod_cata.html';
	$valarr['t_tpl_list']='vod_list.html';
	$valarr['t_tpl_vod']='vod_detail.html';
	$valarr['t_tpl_play']='vod_play.html';
	$valarr['t_tpl_down']='vod_down.html';
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}vod_cata where t_id='.$t_id);
		if($row){
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	else{
		$valarr['t_pid']=intval($pid);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	
	$rn='ptype';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	foreach($MAC_CACHE['vodcata'] as $a){
		if($a['t_pid']==0){
			$plt->set_var('n',$a['t_name']);
			$plt->set_var('v',$a['t_id']);
			$c= $a['t_id']==$valarr['t_pid'] ? 'selected' :'';
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	unset($colarr);
	unset($valarr);
}

elseif($method=='classsaveall')
{
	$c_id = be('arr','c_id');
	$ids = explode(',',$c_id);
	
	foreach($ids as $id){
		$c_name = be('post','c_name' .$id);
		$c_sort = be('post','c_sort' .$id);
		
		if (isN($c_name)) { $c_name='未知'; }
		if (!isNum($c_sort)) { $c_sort=0; }
		
		$db->Update ('{pre}vod_class',array('c_name','c_sort'),array($c_name,$c_sort),'c_id='.$id);
	}
	updateCacheFile();
	redirect( getReferer() );
}


elseif($method=='class')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$sql = 'SELECT count(*) FROM {pre}vod_cata where t_pid=0';
	$nums = $db->getOne($sql);
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$rn='type';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	$rn1='class';
	$plt->set_block('list_'.$rn, 'list_'.$rn1, 'rows_'.$rn1);
		
	$sql = 'SELECT * FROM {pre}vod_cata WHERE t_pid=0 ORDER BY t_sort,t_id ASC';
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs)){
		
		$plt->set_var('rows_'.$rn1);
		
		$colarr=$col_class;
		array_push($colarr,'c_span');
		$c_span='&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;';
		$sql = 'SELECT * FROM {pre}vod_class WHERE c_pid = \''.$row['t_id'].'\' ORDER BY c_sort,c_id ASC';
		$rs1 = $db->query($sql);
		while ($row1 = $db ->fetch_array($rs1)){;
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row1[$n];
			}
			$valarr['c_span']=$c_span;
			
			for($i=0;$i<count($colarr);$i++){
				$n = $colarr[$i];
				$v = $valarr[$n];
				$plt->set_var($n, $v );
			}
			$plt->parse('rows_'.$rn1,'list_'.$rn1,true);
			if($row1['c_hide']==1) { $plt->set_if('rows_'.$rn1,'ishide',true); } else { $plt->set_if('rows_'.$rn1,'ishide',false); }
		}
		unset($rs1);
		
		
		$colarr=$col_cata;
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['c_span']='';
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
		$plt->set_if('rows_'.$rn,'isparent',true);
	}
	unset($colarr);
	unset($valarr);
	unset($rs);
}

elseif($method=='classinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$c_id=$p['id'];
	$pid=$p['pid'];
	$flag=empty($c_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_class;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}vod_class where c_id='.$c_id);
		if($row){
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	else{
		$valarr['c_pid']=intval($pid);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	
	$rn='ptype';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	foreach($MAC_CACHE['vodcata'] as $a){
		if($a['t_pid']==0){
			$plt->set_var('n',$a['t_name']);
			$plt->set_var('v',$a['t_id']);
			$c= $a['t_id']==$valarr['c_pid'] ? 'selected' :'';
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	unset($colarr);
	unset($valarr);
}

elseif($method=='topic')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	$sql = 'SELECT count(*) FROM {pre}vod_topic where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}vod_topic where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY t_time DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_topic;
	array_push($colarr,'t_count');
	
	$rn='topic';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		$t_count = $db->getOne('SELECT count(*) FROM {pre}vod_relation WHERE r_type=2 and r_a='.$row['t_id']);
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['t_addtime'] = getColorDay($row['t_addtime']);
		$valarr['t_time'] = getColorDay($row['t_time']);
		$valarr['t_count'] = $t_count;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
	}
	unset($rs);
	$pageurl = '?m=vod-topic-pg-{pg}';
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='topicinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$t_id=$p['id'];
	$pid=$p['pid'];
	$flag=empty($t_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_topic;
	array_push($colarr,'flag','backurl');
	
	$valarr['t_tpl']='vod_topiclist.html';
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}vod_topic where t_id='.$t_id);
		if($row){
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	else{
		$valarr['t_pid']=intval($pid);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	
	$arr=array(
		array('a'=>'level','c'=>$valarr['t_level'],'t'=>1,'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'hide','c'=>$valarr['t_hide'],'t'=>1,'n'=>array('显示','隐藏'),'v'=>array(0,1))
	);
	foreach($arr as $a){
		$colarr=$a['n'];
		$valarr=$a['v'];
		$rn=$a['a'];
		$cv=$a['t']==0 ?'checked':'selected';
		
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? $cv: '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	unset($colarr);
	unset($valarr);
}

elseif($method=='topicdata')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$tid = intval($p['tid']);
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	$wd=$p['wd'];
	
	if(!empty($wd) && $wd!='可搜索(视频名称,视频主演)'){
		$where .= ' and (instr(d_name,\''.$wd.'\')>0  or instr(d_starring,\''.$wd.'\')>0) ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(视频名称,视频主演)');
	}
	$plt->set_var('tid',$tid);
	
	$pagesize=16;
	$sql = 'SELECT count(*) FROM {pre}vod where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$pagesize);
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_starring FROM {pre}vod where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY d_time DESC limit ".($pagesize * ($page-1)) .",".$pagesize;
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=array('d_id','d_name','d_starring','d_link');
	$rn='topicdata';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$pidarr = $GLOBALS['MAC_CACHE']['vodcata'][$valarr['d_type']];
		$d_link = "../".$tpl->getLink('vod','detail',$pidarr,$row);
		
		$d_link = str_replace("../".$MAC['site']['installdir'],"../",$d_link);
		if (substring($d_link,1,strlen($d_link)-1)=="/") { $d_link .= "index.". $MAC['app']['suffix'];}
		$valarr['d_link'] = $d_link;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		unset($pidarr);
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	$pageurl = '?m=vod-topicdata-pg-{pg}-tid-'.$tid.'-wd-'.urlencode($wd);
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,6,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='server')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$xp = '../inc/config/vodserver.xml';
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xp);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName('server');
	
	if($nodes->length==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$rn='server';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	$colarr=array('status','sort','from','show','des','tip');
	$num=0;
	foreach($nodes as $node){
		$num++;
		
		$status = $node->attributes->item(0)->nodeValue;
		$sort = $node->attributes->item(1)->nodeValue;
		$from = $node->attributes->item(2)->nodeValue;
		$show = $node->attributes->item(3)->nodeValue;
		$des = $node->attributes->item(4)->nodeValue;
		$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
		$status = $status=='1' ? '<font color=green>启用</font>' : '<font color=red>禁用</font>';
		
		$valarr=array($status,$sort,$from,$show,$des,$tip);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($xmlnode);
    unset($nodes);
    unset($doc);
	unset($colarr);
	unset($valarr);
}

elseif($method=='serverinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$xp = '../inc/config/vodserver.xml';
	
	$flag=empty($p['from']) ? 'add' : 'edit';
	$backurl=getReferer();
	
	if($flag=='edit'){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xp);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName("server");
		foreach($nodes as $node){
			$from = $node->attributes->item(2)->nodeValue;
			if ($p['from'] == $from){
				$status = $node->attributes->item(0)->nodeValue;
				$sort = $node->attributes->item(1)->nodeValue;
				$show = $node->attributes->item(3)->nodeValue;
				$des = $node->attributes->item(4)->nodeValue;
				$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
				break;
			}
		}
		unset($xmlnode);
    	unset($nodes);
    	unset($doc);
	}
	
	$colarr=array('flag','backurl','status','sort','from','show','des','tip');
	$valarr=array($flag,$backurl,$status,$sort,$from,$show,$des,$tip);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	
	$colarr=array('禁用','启用');
	$valarr=array('0','1');
	$rn='status';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		
		$c = $v==$status ? 'selected': '';
		$plt->set_var('v', $v );
		$plt->set_var('n', $n );
		$plt->set_var('c', $c );
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	
	unset($colarr);
	unset($valarr);
}

elseif($method=='player')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$xp = '../inc/config/vodplay.xml';
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xp);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName('play');
	
	if($nodes->length==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$rn='player';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	$colarr=array('status','sort','from','show','des','tip');
	$num=0;
	foreach($nodes as $node){
		$num++;
		
		$status = $node->attributes->item(0)->nodeValue;
		$sort = $node->attributes->item(1)->nodeValue;
		$from = $node->attributes->item(2)->nodeValue;
		$show = $node->attributes->item(3)->nodeValue;
		$des = $node->attributes->item(4)->nodeValue;
		$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
		$status = $status=='1' ? '<font color=green>启用</font>' : '<font color=red>禁用</font>';
		
		$valarr=array($status,$sort,$from,$show,$des,$tip);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($xmlnode);
    unset($nodes);
    unset($doc);
	unset($colarr);
	unset($valarr);
}

elseif($method=='playerup')
{
	$s = file_get_contents($_FILES['file1']['tmp_name']);
	$labelRule = buildregx('<([\s\S]+?)>([\s\S]+?)</\1>',"");
	preg_match_all($labelRule,$s,$iar);
	$arlen=count($iar[1]);
	for($m=0;$m<$arlen;$m++){
		$play[$iar[1][$m]] = $iar[2][$m];
	}
	unset($iar);
	
	
	if($play['from']!=''){
		$xp = '../inc/config/vodplay.xml';
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xp);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName('play');
		$flag='add';
		foreach($nodes as $node){
			if ($play['from'] == $node->attributes->item(2)->nodeValue){
				$flag='edit';
				break;
			}
		}
		unset($xmlnode,$nodes,$doc);
		fwrite(fopen('../player/'.$play['from'].'.js','wb'),$play['code']);
		
		$play['flag']=$flag;
		$play['tab']='vodplay';
		$play['code']='';
		$play['backurl'] = 'index.php?m=vod-player';
		$sHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head>	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	<title导入播放器代码</title></head>';
		$sHtml .= "<form id='formsubmit' name='formsubmit' action='admin_xml.php?ac=savexml' method='post'>";
		foreach($play as $k=>$v){
            $sHtml.= "<input type='hidden' name='".$k."' value='".$v."'/>";
        }
        $sHtml = $sHtml."<input type='submit' value='提交'></form>";
		$sHtml = $sHtml."<script>document.forms['formsubmit'].submit();</script>";
		echo $sHtml;
	}
	else{
		jump('?m=vod-player');
	}
}

elseif($method=='playerinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$xp = '../inc/config/vodplay.xml';
	
	$flag=empty($p['from']) ? 'add' : 'edit';
	$backurl=getReferer();
	
	if($flag=='edit'){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xp);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName("play");
		foreach($nodes as $node){
			$from = $node->attributes->item(2)->nodeValue;
			if ($p['from'] == $from){
				$status = $node->attributes->item(0)->nodeValue;
				$sort = $node->attributes->item(1)->nodeValue;
				$show = $node->attributes->item(3)->nodeValue;
				$des = $node->attributes->item(4)->nodeValue;
				$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
				break;
			}
		}
		unset($xmlnode);
    	unset($nodes);
    	unset($doc);
	}
	
	$colarr=array('flag','backurl','status','sort','from','show','des','tip');
	$valarr=array($flag,$backurl,$status,$sort,$from,$show,$des,$tip);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	
	$colarr=array('禁用','启用');
	$valarr=array('0','1');
	$rn='status';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		
		$c = $v==$status ? 'selected': '';
		$plt->set_var('v', $v );
		$plt->set_var('n', $n );
		$plt->set_var('c', $c );
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	
	unset($colarr);
	unset($valarr);
}

elseif($method=='batch')
{
	headAdmin2('视频批量操作');
	
	$ckdel = $p['ckdel'];
	$ckrq = $p['ckrq'];
	$cktj = $p['cktj'];
	$cklock = $p['cklock'];
	$ckhide = $p['ckhide'];
	$batch_hits1 = $p['batch_hits1'];
	$batch_hits2 = $p['batch_hits2'];
	$batch_level = $p['batch_level'];
	$batch_lock = $p['batch_lock'];
	$batch_hide = $p['batch_hide'];
	
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	$type=$p['type']; if(isN($type)){ $type=999; } else { $type=intval($type); }
	$topic=$p['topic']; if(isN($topic)){ $topic=999; } else { $topic=intval($topic); }
	$level=$p['level']; if(isN($level)){ $level=999; } else { $level=intval($level); }
	$hide=$p['hide']; if(isN($hide)){ $hide=999; } else { $hide=intval($hide); }
	$lock=$p['lock']; if(isN($lock)){ $lock=999; } else { $lock=intval($lock); }
	$state=$p['state']; if(isN($state)){ $state=999; } else { $state=intval($state); }
	$pic=$p['pic']; if(isN($pic)){ $pic=999; } else { $pic=intval($pic); }
	
	$id=$p['id'];
	$repeat=$p['repeat'];
	$repeatlen=$p['repeatlen'];
	$by=$p['by']; if(isN($by)) { $by='d_time'; }
	$wd=$p['wd'];
	$play=$p['play'];
	$down=$p['down'];
	$area=$p['area'];
	$lang=$p['lang'];
	$server=$p['server'];
	
	if($id!=''){
		$where .= ' and d_id='.$id;
	}
	if($type!=999){
		$where .=' and d_type='.$type.' ';
	}
	if($hide!=999){
		$where .=' and d_hide='.$hide.' ';
	}
	if($lock!=999){
		$where .=' and d_lock='.$lock.' ';
	}
	if($level!=999){
		$where .=' and d_level='.$level.' ';
	}
	if($topic!=999){
		$where .=' and d_id in(select r_b from {pre}vod_relation where r_type=2 and r_a='.$topic.') ';
	}
	if($state!=999){
		if($state==0){ $where.=' and d_state=0 '; } else{ $where.=' and d_state>0 '; }
	}
	if(!empty($play)){
		if($play=='no'){
			$where .=" and d_playfrom='' ";
		}
		else{
			$where .= ' AND instr(d_playfrom,\''.$play.'\')>0 ';
		}
	}
	if(!empty($down)){
		if($down=='no'){
			$where .=" and d_downfrom='' ";
		}
		else{
			$where .= ' AND instr(d_downfrom,\''.$down.'\')>0 ';
		}
	}
	if(!empty($server)){
		if($server=='no'){
			$where .=" and d_playserver='' ";
		}
		else{
			$where .= ' AND instr(d_playserver,\''.$server.'\')>0 ';
		}
	}
	
	if($pic!=999){
		if($pic==0){
	    	$where .= ' AND d_pic = \'\' ';
	    }
	    elseif($pic==1){
	    	$where .= ' AND instr(d_pic,\'ttp://\')>0 ';
	    }
	    elseif($pic==2){
	    	$where .= ' AND instr(d_pic,\'#err\')>0  ';
	    }
	}
    
	if(!empty($wd) && $wd!='可搜索(视频名称、视频主演)'){
		$where .= ' and ( instr(d_name,\''.$wd.'\')>0 or instr(d_starring,\''.$wd.'\')>0 ) ';
	}
	
	$pagesize=100;
	
	if($ckdel=='1'){
		$sql = $where=="" ? "truncate table {pre}vod " : "delete from {pre}vod where 1=1 ".$where;
	    $status = $db->query($sql);
	    showMsg('批量删除数据完成!',"index.php?m=vod-list");
	}
	elseif($ckdel=='2'){
		$sql = "SELECT count(*) FROM {pre}vod where 1=1 ".$where;
		$nums = $db->getOne($sql);
		$pagecount=ceil($nums/$pagesize);
		
	    $sql = "SELECT d_id,d_name,d_playfrom,d_playurl,d_playserver,d_playnote FROM {pre}vod where 1=1 ".$where . " ORDER BY d_id desc limit ".($pagesize * ($pagecount-1)) .",".$pagesize;
	    
		$rs = $db->query($sql);
		if($nums==0){
			showMsg ("数据处理完毕!", "index.php?m=vod-list");
		}
		else{
			echo "<font color=red>共".$nums."条数据包含".$play."播放器,共".$pagecount."页正在开始删除第".$pagecount."页数据</font><br>";
			$n=0;
			while ($row = $db ->fetch_array($rs))
			{
				$n++;
				$d_playfrom = $row["d_playfrom"];
				$d_playserver = $row["d_playserver"];
				$d_playurl = $row["d_playurl"];
				$d_playnote = $row['d_playnote'];
				
				$playfromarr = explode("$$$",$d_playfrom);
				$playserverarr = explode("$$$",$d_playserver);
				$playurlarr = explode("$$$",$d_playurl);
				$playnotearr = explode("$$$",$d_playnote);
				
				$new_playfrom = "";
				$new_playserver = "";
				$new_playurl = "";
				$new_playnote = "";
				
				$rc=false;
				for ($i=0;$i<count($playfromarr);$i++){
					if($playfromarr[$i]==$play){
						
					}
					else{
						if($rc){
							$new_playfrom .= "$$$";
							$new_playserver .= "$$$";
							$new_playurl .= "$$$";
							$new_playnote .= "$$$";
						}
						$new_playfrom .= $playfromarr[$i];
						$new_playserver .= $playserverarr[$i];
						$new_playnote .= $playnotearr[$i];
						$new_playurl .= str_replace("'","''",$playurlarr[$i]);
						$rc=true;
					}
				}
				
				$sql = "UPDATE {pre}vod set d_playfrom='".$new_playfrom."',d_playserver='".$new_playserver."',d_playnote='".$new_playnote."',d_playurl='".$new_playurl."' where d_id='".$row["d_id"]."'";
				$db->query($sql);
				echo $n.'.'.$row["d_name"] . "---ok<br>";
			}
			
			$url = '?m=vod-batch-type-'.$type.'-topic-'.$topic.'-level-'.$level.'-hide-'.$hide.'-lock-'.$lock.'-by-'.$by.'-pg-'.($page+1).'-wd-'.urlencode($wd).'-repeat-'.$repeat.'-repeatlen-'.$repeatlen.'-state-'.$state.'-pic-'.$pic.'-play-'.$play.'-down-'.$down.'-server-'.$server.'-area-'.urlencode($area).'-lang-'.urlencode($lang).'-ckdel-'.$ckdel;
			
			jump($url,3);
		}
		unset($rs);
	}
	elseif($ckrq=='1'|| $cktj=='1' || $cklock=='1' || $ckhide=='1'){
		$sql = "SELECT count(*) FROM {pre}vod where 1=1 ".$where;
		$nums = $db->getOne($sql);
		$pagecount=ceil($nums/$pagesize);
			
	    $sql = "SELECT d_id,d_name FROM {pre}vod where 1=1 ".$where . " ORDER BY d_id desc limit ".($pagesize * ($page-1)) .",".$pagesize;
	    
	    
		$rs = $db->query($sql);
		if($page>$pagecount){
			showMsg ("数据处理完毕!", "index.php?m=vod-list");
		}
		else{
			echo "<font color=red>共".$nums."条数据需要处理，共".$pagecount."页正在处理第".$page."页数据</font><br>";
			$n=0;
			while ($row = $db ->fetch_array($rs))
			{
				$n++;
				$des='';
				$sql1='';
				if($ckrq=='1'){
					$d_hits = rndNum($batch_hits1,$batch_hits2);
					$sql1 .= ' d_hits='.$d_hits;
					$des .= '&nbsp;随机人气：'.$d_hits.'；';
				}
				if($cktj=='1'){
					$sql1 .= $sql=='' ? '' : ',';
					$sql1 .= ' d_level='.$batch_level;
					$des .= '&nbsp;推荐值：'.$batch_level.'；';
				}
				if($cklock=='1'){
					$sql1 .= $sql=='' ? '' : ',';
					$sql1 .= ' d_lock='.$batch_lock;
					$des .= $batch_lock==1 ? '&nbsp;[锁定]' : '&nbsp;[解锁]';
				}
				if($ckhide=='1'){
					$sql1 .= $sql=='' ? '' : ',';
					$sql1 .= ' d_hide='.$batch_hide;
					$des .= $batch_hide==1 ? '&nbsp;[隐藏]' : '&nbsp;[显示]';
				}
				$sql = "UPDATE {pre}vod set ".$sql1." where d_id=".$row["d_id"];
				$db->query($sql);
				echo $n.'.'.$row["d_name"] . "-" . $des ."---ok<br>";
			}
			
			$url = '?m=vod-batch-type-'.$type.'-topic-'.$topic.'-level-'.$level.'-hide-'.$hide.'-lock-'.$lock.'-by-'.$by.'-pg-'.($page+1).'-wd-'.urlencode($wd).'-repeat-'.$repeat.'-repeatlen-'.$repeatlen.'-state-'.$state.'-pic-'.$pic.'-play-'.$play.'-down-'.$down.'-server-'.$server.'-area-'.urlencode($area).'-lang-'.urlencode($lang).'-ckrq-'.$ckrq.'-batch_hits1-'.$batch_hits1.'-batch_hits2-'.$batch_hits2.'-cktj-'.$cktj.'-batch_level-'.$batch_level.'-cklock-'.$cklock.'-batch_lock-'.$batch_lock.'-ckhide-'.$ckhide.'-batch_ckhide-'.$batch_ckhide;
			
			jump($url,3);
		}
		unset($rs);
	}
	else{
		showMsg ("参数不正确!", "index.php?m=vod-list");
	}
}

elseif($method=='list')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }

    //这些值的获取，是依据传入进来的’筛选条件‘。 (即通过搜索操作，执行该代码)
    //参数来源： vod_list.html中js函数getPar()
    // 参数的一致，还需同步修改本分支下面的'分页导航'部分的代码
    $pid = $p['pid']; if(isN($pid)){$pid='';} else { $pid=intval($pid);};
	$type= $p['type']; if(isN($type)){ $type=999; } else { $type=intval($type); }
    //exit('type='.$type);
	$topic=$p['topic']; if(isN($topic)){ $topic=999; } else { $topic=intval($topic); }
	$level=$p['level']; if(isN($level)){ $level=999; } else { $level=intval($level); }
	$hide=$p['hide']; if(isN($hide)){ $hide=999; } else { $hide=intval($hide); }
	$lock=$p['lock']; if(isN($lock)){ $lock=999; } else { $lock=intval($lock); }
	$state=$p['state']; if(isN($state)){ $state=5999; } else { $state=intval($state); }
	$pic=$p['pic']; if(isN($pic)){ $pic=999; } else { $pic=intval($pic); }
	
	$id=$p['id'];
    //exit($id.'');
	$repeat=$p['repeat'];
	$repeatlen=$p['repeatlen'];  //这个没有细细研究，但是其影响了sql语句，而且如果>0，查询速度会变慢的离谱
    ////////if(isN($repeatlen)){ $repeatlen = 1;}
    //exit('$repeatlen= '.$repeatlen);
	$by=$p['by']; if(isN($by)) { $by='d_time'; }
	$wd=$p['wd'];
	$play=$p['play'];
	$down=$p['down'];
	$area=$p['area'];
	$lang=$p['lang'];
	$server=$p['server'];


	if($id!=''){
		$where .= ' and d_id='.$id;
	}
    elseif($pid != ''){ //d_pids定义为 资源所属分类，以后都用pid标识
        //exit($id.', '.$pid);
        //若’筛选分类‘，则取资源的方式不同于之前的 d_type的筛选了。
        //需要用到表{pre}vod_r_type_dir
        $res = $db->query("select r_did from {pre}vod_r_type_dir where r_cid=$pid");
        $idarr = array();
        while ($row = $db ->fetch_array($res)) {
            $idarr[] = $row['r_did'];
        }
        $idStr = implode(',', $idarr);
        if(strlen($idStr)>0){
           $where .= " and d_id in ($idStr)";
        }
        unset($idarr);
    }

	if($type!=999){ //d_type定义为 资源的类型
		$where .=' and d_type='.$type.' ';
	}
	if($hide!=999){
		$where .=' and d_hide='.$hide.' ';
	}
	if($lock!=999){
		$where .=' and d_lock='.$lock.' ';
	}
	if($level!=999){
		$where .=' and d_level='.$level.' ';
	}
	if($topic!=999){
		$where .=' and d_id in(select r_b from {pre}vod_relation where r_type=2 and r_a='.$topic.') ';
	}
	if($state!=5999){
		if($state==0){ $where.=' and d_state=0 '; } else{ $where.=' and d_state>0 '; }
	}
	if(!empty($area)){
		$where .=" and d_area='".$area."' ";
	}
	if(!empty($lang)){
		$where .=" and d_lang='".$lang."' ";
	}
	
	if(!empty($play)){
		if($play=='no'){
			$where .=" and d_playfrom='' ";
		}
		else{
            //INSTR(字段名, 字符串) 返回字符串在字段中内容的位置(基于1)，未找到返回0
			$where .= ' AND instr(d_playfrom,\''.$play.'\')>0 ';
            //exit('::::'.$where);
		}
		
	}
	if(!empty($down)){
		if($down=='no'){
			$where .=" and d_downfrom='' ";
		}
		else{
			$where .= ' AND instr(d_downfrom,\''.$down.'\')>0 ';
		}
	}
	if(!empty($server)){
		if($server=='no'){
			$where .=" and d_playserver='' ";
		}
		else{
			$where .= ' AND instr(d_playserver,\''.$server.'\')>0 ';
		}
	}
	
	if($pic!=999){
		if($pic==0){
	    	$where .= ' AND d_pic = \'\' ';
	    }
	    elseif($pic==1){
	    	$where .= ' AND instr(d_pic,\'ttp://\')>0 ';
	    }
	    elseif($pic==2){
	    	$where .= ' AND instr(d_pic,\'#err\')>0  ';
	    }
	}
	$repeat_status=0;
	if($repeat == 'ok'){
        $repeat_field = ' d_name as `d_name1` ';
        $tmptab=',tmptable as `tmp` ';
        if($repeatlen>0){
        	$repeat_status=1;
			$repeat_field = ' left(d_name,'.$repeatlen.') as `d_name1` ';
		}
		else{
			$where .= ' AND d_name=`tmp`.d_name1 ';
		}
		if($page==1){
			//temporary
			$db->query('DROP TABLE IF EXISTS tmptable;');
			$tmpsql='create table IF NOT EXISTS `tmptable` as (SELECT ' . $repeat_field . ' FROM {pre}vod GROUP BY d_name1 HAVING COUNT(d_name1)>1); ';
			
			$db->query($tmpsql);
		}

        //rocking +
        $by = 'd_name';
    }
    $plt->set_var('repeatlen',$repeatlen);
    
	if(!empty($wd) && $wd!='可搜索(资源名称)'){
        if(preg_match('/(id\s*=\s*\d+)/', $wd, $matches)){
            if(isset($matches[1]) && !empty($matches[1])) {
                $tmp = str_replace('id', 'd_id', $matches[1]);
                $where .= ' and ' . $tmp . ' ';
            }
        }
        elseif(preg_match('/(sets\s*[><]{0,1}[=]{0,1}\d+)\s*/', $wd, $matches)){
            if(isset($matches[1]) && !empty($matches[1])){
                $tmp = str_replace('sets', 'd_episode', $matches[1]);
                $where .= ' and '.$tmp.' ';
            }
        }
        else{
            $where .= ' and ( instr(d_name,\''.$wd.'\')>0 or instr(d_starring,\''.$wd.'\')>0 ) ';
        }
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(资源名称)');
	}

	$topicarr = $MAC_CACHE['vodtopic'];
	$topicarrn = array();
	$topicarrv = array();
	foreach($topicarr as $arr){
		array_push($topicarrn,$arr['t_name']);
		array_push($topicarrv,$arr['t_id']);
	}
	$pidarr = $MAC_CACHE['vodcata'];
	$pidarrn = array();
	$pidarrv = array();
	foreach($pidarr as $arr1){
		$s='&nbsp;|—';
		if($arr1['t_pid']==0){
			array_push($pidarrn,$s.$arr1['t_name']);
			array_push($pidarrv,$arr1['t_id']);
			foreach($pidarr as $arr2){
				if($arr1['t_id']==$arr2['t_pid']){
					$s='&nbsp;|&nbsp;&nbsp;&nbsp;|—';
					array_push($pidarrn,$s.$arr2['t_name']);
					array_push($pidarrv,$arr2['t_id']);
				}
			}
		}
	}
    //资源类型>>>>>>>>>>>>>>>>>>>>>>>>>>>
    $typearr = $MAC_CACHE['restype'];
    $typearrn = array();//array('音频', '视频', '课件', '音频类别','视频类别' );
	$typearrv = array();//array(0,4,5,10,14);
    foreach($typearr as $arr1){
        array_push($typearrv, $arr1['t_id']); //key
        array_push($typearrn, $arr1['t_name']); //value
    }
    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

	$areaarr = explode(',',$MAC['app']['area']);
	$langarr = explode(',',$MAC['app']['lang']);
	
	$playarr = $GLOBALS['MAC_CACHE']['vodplay'];
	$playarrn = array();
	$playarrv = array();
	array_push($playarrn,'空播放组');
	array_push($playarrv,'no');
	foreach($playarr as $k=>$v){
		array_push($playarrn,$v['show']);
		array_push($playarrv,$k);
	}
	
	$donwarr = $GLOBALS['MAC_CACHE']['voddown'];
	$donwarrn = array();
	$donwarrv = array();
	array_push($donwarrn,'空下载组');
	array_push($donwarrv,'no');
	foreach($donwarr as $k=>$v){
		array_push($donwarrn,$v['show']);
		array_push($donwarrv,$k);
	}
	$serverarr = $GLOBALS['MAC_CACHE']['vodserver'];
	$serverarrn = array();
	$serverarrv = array();
	array_push($serverarrn,'空服务器组');
	array_push($serverarrv,'no');
	foreach($serverarr as $k=>$v){
		array_push($serverarrn,$v['show']);
		array_push($serverarrv,$k);
	}
	
	$arr=array(
		array('a'=>'hide','c'=>$hide,'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('a'=>'lock','c'=>$lock,'n'=>array('未锁定','已锁定'),'v'=>array(0,1)),
		array('a'=>'state','c'=>$state,'n'=>array('完结了','连载中'),'v'=>array(0,1)),
		array('a'=>'pic','c'=>$pic,'n'=>array('无图片','远程图片','同步出错图'),'v'=>array(0,1,2)),
		array('a'=>'level','c'=>$level,'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'by','c'=>$by,'n'=>array('编号','总人气','日人气','周人气','月人气'),'v'=>array('d_id','d_hits','d_dayhits','d_weekhits','d_monthhits')),
		array('a'=>'topic','c'=>$topic,'n'=>$topicarrn,'v'=>$topicarrv),
		array('a'=>'pid','c'=>$pid,'n'=>$pidarrn,'v'=>$pidarrv),
		array('a'=>'area','c'=>$area,'n'=>$areaarr,'v'=>$areaarr),
        array('a'=>'type','c'=>$type, 'n'=>$typearrn,'v'=>$typearrv), // n和v的数组应该一一对应，前者为值，后者为键
		array('a'=>'lang','c'=>$lang,'n'=>$langarr,'v'=>$langarr),
		array('a'=>'play','c'=>$play,'n'=>$playarrn,'v'=>$playarrv),
		array('a'=>'down','c'=>$down,'n'=>$donwarrn,'v'=>$donwarrv),
		array('a'=>'server','c'=>$server,'n'=>$serverarrn,'v'=>$serverarrv)
	);
    //这个部分对模板vod_list.html中的 检索模块的内容 以模块为单位进行更换。
	foreach($arr as $a){
		$colarr=$a['n'];
		$valarr=$a['v'];
		$rn=$a['a'];
//        if($rn == 'type'){
//            print_r($colarr);
//            echo '<br>------2 ';
//            print_r($valarr);
//            echo '<br>';
//        }
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? 'selected': '';
//            if($rn=='type'){
//                echo $v.', '.$n.'<br>';
//            }
			$plt->set_var('v', $v ); //value
			$plt->set_var('n', $n ); // 标签中的内容 和 'v' 一一对应
			$plt->set_var('c', $c ); // selected??， 依据 传入进来的参数来判断，如上面$arr数组中的键值'c'对应的变量
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	if($repeat == 'ok'){
		$plt->set_if('main','isrepeat',true);
	}
	else{
		$plt->set_if('main','isrepeat',false);
	}
	
	if($repeat_status==1){
		$sql='select count(*) from {pre}vod a INNER JOIN (SELECT d_id,left(d_name,'.$repeatlen.') as d_name1 from {pre}vod where CHAR_LENGTH(d_name)>='.$repeatlen.' ) b on a.d_id = b.d_id  INNER JOIN (select d_name1 from tmptable) c on b.d_name1 = c.d_name1 ';
		$nums = $db->getOne($sql);
		
		$sql='select a.`d_id`, `d_name`, `d_pids`, `d_episode`, `d_enname`, `d_color`, `d_pic`, `d_remarks`, `d_type`, `d_type_expand` ,`d_hide`, `d_lock`, `d_state`, `d_level`,  `d_hits`,  `d_addtime`, `d_time`, `d_maketime`, `d_playfrom`, `d_downfrom`,b.d_name1 from {pre}vod a INNER JOIN (SELECT d_id,left(d_name,'.$repeatlen.') as d_name1 from {pre}vod where CHAR_LENGTH(d_name)>='.$repeatlen.' ) b on a.d_id = b.d_id  INNER JOIN (select d_name1 from tmptable) c on b.d_name1 = c.d_name1 where 1=1 ORDER BY d_name asc  limit '.($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
		$rs = $db->query($sql);
	}
	else{
		$sql = 'SELECT count(*) FROM {pre}vod'.$tmptab.' where 1=1 '.$where;
		$nums = $db->getOne($sql);
		$sql = "SELECT `d_id`, `d_name`, `d_pids`,`d_episode`, `d_enname`, `d_color`, `d_pic`, `d_remarks`, `d_type`, `d_type_expand` ,`d_hide`, `d_lock`, `d_state`, `d_level`,  `d_hits`,  `d_addtime`, `d_time`,`d_maketime`, `d_playfrom`, `d_downfrom`  FROM {pre}vod".$tmptab." where 1=1 ";
		$sql .= $where;
		$sql .= " ORDER BY ".$by." DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
		$rs = $db->query($sql);
	}
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_vod;
	array_push($colarr,'d_link');
	
	$rn='vod';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
        //构造最基本的数据
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}

        //类别显示部分重新改写
		//$typearr = $MAC_CACHE['vodcata'][$row['d_type']];
        $pidarr = explode(',' , $row['d_pids']);
        $pidsStr = '';
        foreach($pidarr as $pidtmp){
            $pidtmp = trim($pidtmp);
            if(strlen($pidtmp) > 0){
                $pidsStr = $pidsStr . $MAC_CACHE['vodcata'][$pidtmp]['t_name'].',';
            }
        }
        $pidsStr = rtrim($pidsStr, ', ');
        //exit($pidsStr);


        //来源部分重写，d_playfrom,只是内部显示而已，我们要用明文
        $srcarr = explode("$$$", $row['d_playfrom'] );
        $srcStr = '';
        foreach($srcarr as $src){
            $src = trim($src);
            if(strlen($src) > 0){
                $srcStr = $srcStr . $MAC_CACHE['vodplay'][$src]['show'].',';
            }
        }
        $srcStr = rtrim($srcStr, ', ');

		$valarr['d_state'] = $row['d_state']==0 ? '' : '['.$row['d_state'].']';
		$valarr['d_time'] = $row['d_time']==0 ? '' : getColorDay($row['d_time']);
		$valarr['d_hide'] = $row['d_hide']==0 ? '' : '<font color=red>[隐]</font>';
		$valarr['d_lock'] = $row['d_lock']==0 ? '' : '<font color=red>[锁]</font>';

        //资源类型
        $valarr['d_type'] = $typearr[$row['d_type']]['t_name'];

        //分类
        $valarr['d_pids'] = $pidsStr;

		//$valarr['d_playfrom'] = str_replace("$$$",",",$row["d_playfrom"]);
        $valarr['d_playfrom'] = $srcStr;

		$valarr['d_downfrom'] = str_replace("$$$",",",$row["d_downfrom"]);
		
	 	$d_link = "../".$tpl->getLink('vod','detail',/*$typearr*/$pidarr,$row);
		$d_link = str_replace("../".$MAC['site']['installdir'],"../",$d_link);
		if (substring($d_link,1,strlen($d_link)-1)=="/") { $d_link .= "index.". $MAC['app']['suffix'];}
		$valarr['d_link'] = $d_link;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if(($GLOBALS['MAC']['view']['voddetail']==2 || $GLOBALS['MAC']['view']['vodplay']==2 || $GLOBALS['MAC']['view']['voddown']==2) &&($row['d_maketime']<$row['d_time'])) {
			$plt->set_if('rows_'.$rn,'ismake',true);
		}
		else{
			$plt->set_if('rows_'.$rn,'ismake',false);
		}
	}
	unset($rs);
	unset($colarr);
	unset($valarr);
	unset($topicarr);
	unset($typearr);
	unset($playarr);
	unset($downarr);
	unset($serverarr);
	
	$pageurl = '?m=vod-list-type-'.$type.'-pid-'.$pid.'-topic-'.$topic.'-level-'.$level.'-hide-'.$hide.'-lock-'.$lock.'-by-'.$by.'-pg-{pg}-wd-'.urlencode($wd).'-repeat-'.$repeat.'-repeatlen-'.$repeatlen.'-state-'.$state.'-pic-'.$pic.'-play-'.$play.'-down-'.$down.'-server-'.$server.'-area-'.urlencode($area).'-lang-'.urlencode($lang);
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,6,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='info')  //同步参见对应模板： vod_info.html中的相关注释
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$id=$p['id'];
	$flag=empty($id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_vod;
	array_push($colarr,'flag','backurl');

    //如果是编辑状态
    $playinfoarr = array();
    if($flag=='edit'){
        //取 $id对应的数据信息(vod中的影片信息，播放地址在其它表中)
		$row=$db->getRow('select * from {pre}vod where d_id='.$id);
		if($row){
			$valarr=array();
            //依据我们自己定义的想获取内容的列名($colarr)进行获取内容。
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);

        //取 vod_libs中的播放组、以及单集信息
        //playfrom即源的组合依旧放在vod中，但必须和和libs中的各个源(l_src)要一致
        $rs = $db->query('select * from {pre}vod_libs where l_pid='.$id.' order by l_idx');
        $line_divider = '|';
        while ($row = $db ->fetch_array($rs)){
            $src = $row['l_src'];
            if(isset($playinfoarr[$src]))
                $playinfoarr[$src] = $playinfoarr[$src]. '#'. $row['l_idx'].$line_divider.$row['l_name'].$line_divider.$row['l_pic'].$line_divider.$row['l_downurl'];
            else
                $playinfoarr[$src] = $row['l_idx'].$line_divider.$row['l_name'].$line_divider.$row['l_pic'].$line_divider.$row['l_downurl'];
        }
        unset($row);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	if($valarr['d_time']!=''){ $valarr['d_time']=date('Y-m-d H:i:s',$valarr['d_time']); }

    //至此，数据库表中的数据都取完并存入： $valarr数组， 列名(自定义的哦$colarr)作为键值
    //但，播放地址$valarr['d_playurl']并非我所需要的值，因为播放地址不是在该表中。
    //现在想来，网站在设计好缓存后，数据库的管理、存储、性能方面的考虑真的是多余的。 方便操作管理才是最重要的，否则增加工作量不说，性能还完全没有提升。
    //除非频繁访问数据库的，才需要考虑其设计

    //！！！！！ 为此，我做一个重大改变 ！！！！！！
    //将我的播放列表数据res_libs的内容，全部转存入 mmh_vod表内 ==>>>
    /*
         l_src => d_playfrom   所有来源集合， 用 $$$ 分隔，每个来源会对应一组d_playurl
         l_downurl => d_playurl  用$$$分隔来源， 每个来源下的url用#分隔，分隔后每个条目格式为 "集号<>url"
         l_downurl2 => d_playurl2(新增) 本地资源的备份, 格式同 d_playurl

        下面仅与剧集有关，不关乎来源，即：公共的
         l_pic => d_eppic(新增)   //用 # 分隔每集， 每集由 "集号<>url" 组成
         l_pic2=> d_eppic2（新增）  //本地资源的备份,格式同 d_eppic
         l_name => d_epname(新增)  //格式同 d_eppic
         l_playcnt => d_epplaycnt(新增) // 格式同 d_eppic               先清零，以后统计或许会用到
    */


	//此处替换模板中的所有相关标签，
    //标签名大部分为 数据库表中列名—— colarr=>$col_vod(最顶端自己定义的)
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
        //echo $n.', '.$v.'<br>';
		$plt->set_var($n, $v );
	}
	if($valarr['d_lock']==0){
		$plt->set_if('main','islock',false);
	}
	else{
		$plt->set_if('main','islock',true);
	}
	
	if($MAC['app']['expandtype']==0){
		$plt->set_if('main','isexpandtype',false);
	}
	else{
		$plt->set_if('main','isexpandtype',true);
	}

    //模板vod_info.html有关于这部分的对应注释。
    //该部分的处理是基于灵活配置考虑的， 通过xml文件来配置播放器相关数据
    //分析是发现，虽然取的数据都来自于config.php中$MAC_CACHE全局变量，
    //但其最初的数据源： inc/config/*.xml
    //下面三个变量返回的的都是 <option语句
	$select_play = makeSelectXml('vodplay','play',"");
	//$select_down = makeSelectXml('voddown','down',"");
	$select_server = makeSelectXml('vodserver','server',"");
	
	$plt->set_var('select_play',str_replace("'","\'",$select_play));
	//$plt->set_var('select_down',str_replace("'","\'",$select_down));
	$plt->set_var('select_server',str_replace("'","\'",$select_server));

    //处理分集标题
//    if(!empty($valarr['d_epname'])){
//        $epname = str_replace('#', Chr(13), $valarr['d_epname']);
//    }
//    $plt->set_var('epname',$epname);


    //处理分集图片url
//    if(!empty($valarr['d_eppic'])){
//        $eppic = str_replace('#', Chr(13), $valarr['d_eppic']);
//    }
//    $plt->set_var('eppic', $eppic);

	$playnum = 1;
	$rn='play';
    //这里的block，应该理解为’块', 特定注释块包含的部分(含注释)
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn); //控制 html模板中如 <!-- BEGIN list_play --> <!-- END list_play --> 之间部分的显示
	if(!empty($valarr['d_playfrom'])){
		$playfromarr = explode('$$$',$valarr['d_playfrom']);   //这个将作为我们的播放来源，按来源分成不同的播放地址组
	    $playserverarr = explode('$$$',$valarr['d_playserver']);
	    $playnotearr = explode('$$$',$valarr['d_playnote']);
	    //$playurlarr = explode('$$$',$valarr['d_playurl']);
        //
	    $i=0;
		foreach($playfromarr as $a){
			$playfrom = $playfromarr[$i];
			$playserver = $playserverarr[$i];
	    	$playnote = $playnotearr[$i];
	    	//$playurl = str_replace('#', Chr(13),$playurlarr[$i]);
            $playurl = str_replace('#', Chr(13), $playinfoarr[$a]);

            //播放器选项与 $playfrom关联
            //替换selcet中的option选项，已确定哪个是被选中的。
            //$playfrom的值要与option中的value值一致。
	    	$select_play_sel = str_replace('<option value=\''.$playfrom.'\' >','<option value=\''.$playfrom.'\' selected>',$select_play);
	    	
	    	$select_server_sel = str_replace('<option value=\''.$playserver.'\' >','<option value=\''.$playserver.'\' selected>',$select_server);
	    	
			$plt->set_var('n',$playnum);
			$plt->set_var('playurl',$playurl);
			$plt->set_var('playnote',$playnote);
			$plt->set_var('select_play_sel',$select_play_sel);
			$plt->set_var('select_server_sel',$select_server_sel);
			$i++;
			$playnum++;
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($arr);
	}
	else{
		$plt->set_var('rows_'.$rn,'');
	}
	$plt->set_var('playcount',$playnum);

    // rocking - 屏蔽界面 下载信息部分-
    /*
	$downnum = 1;
	$rn='down';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	if(!empty($valarr['d_downfrom'])){
		$downfromarr = explode('$$$',$valarr['d_downfrom']);
	    $downserverarr = explode('$$$',$valarr['d_downserver']);
	    $downnotearr = explode('$$$',$valarr['d_downnote']);
	    $downurlarr = explode('$$$',$valarr['d_downurl']);
	    $i=0;
		foreach($downfromarr as $a){
			$downfrom = $downfromarr[$i];
			$downserver = $downserverarr[$i];
	    	$downnote = $downnotearr[$i];
	    	$downurl = str_replace('#', Chr(13),$downurlarr[$i]);
	    	
	    	$select_down_sel = str_replace('<option value=\''.$downfrom.'\' >','<option value=\''.$downfrom.'\' selected>',$select_down);
	    	$select_server_sel = str_replace('<option value=\''.$downserver.'\' >','<option value=\''.$downserver.'\' selected>',$select_server);
	    	
	    	
	    	
			$plt->set_var('n',$downnum);
			$plt->set_var('downurl',$downurl);
			$plt->set_var('downnote',$downnote);
			$plt->set_var('select_down_sel',$select_down_sel);
			$plt->set_var('select_server_sel',$select_server_sel);
			$i++;
			$downnum++;
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($arr);
	}
	else{
		$plt->set_var('rows_'.$rn,'');
	}
	$plt->set_var('downcount',$downnum);
	*/
	
	//分类处理
	$pidarr = $MAC_CACHE['vodcata'];
	$pidarrn = array();
	$pidarrv = array();
	foreach($pidarr as $arr1){
		$s='&nbsp;|—';
		if($arr1['t_pid']==0){
			array_push($pidarrn,$s.$arr1['t_name']);
			array_push($pidarrv,$arr1['t_id']);
			foreach($pidarr as $arr2){
				if($arr1['t_id']==$arr2['t_pid']){
					$s='&nbsp;|&nbsp;&nbsp;&nbsp;|—';
					array_push($pidarrn,$s.$arr2['t_name']);
					array_push($pidarrv,$arr2['t_id']);
				}
			}
		}
	}

    //资源类型处理
    $typearr = $MAC_CACHE['restype'];
    $typearrn = array();
    $typearrv = array();
    foreach($typearr as $arr1){
        array_push($typearrn, $arr1['t_name']);
        array_push($typearrv, $arr1['t_id']);
    }

	$grouparr = $MAC_CACHE['usergroup'];
	$grouparrn = array();
	$grouparrv = array();
	foreach($grouparr as $arr){
		array_push($grouparrn,$arr['ug_name']);
		array_push($grouparrv,$arr['ug_id']);
	}
	
	$areaarr = explode(',',$MAC['app']['area']);
	$langarr = explode(',',$MAC['app']['lang']);
	
	$arr=array(
		array('a'=>'hide','c'=>$valarr['d_hide'],'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('a'=>'level','c'=>$valarr['d_level'],'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'cata','c'=>0,'n'=>$pidarrn,'v'=>$pidarrv), //类别的选择框不需要指明那个被选中，所以将其 'c'设为0
		array('a'=>'group','c'=>$valarr['d_usergroup'],'n'=>$grouparrn,'v'=>$grouparrv),
		array('a'=>'area','c'=>$valarr['d_area'],'n'=>$areaarr,'v'=>$areaarr),
		array('a'=>'lang','c'=>$valarr['d_lang'],'n'=>$langarr,'v'=>$langarr),
        array('a'=>'type','c'=>$valarr['d_type'], 'n'=>$typearrn, 'v'=>$typearrv)
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
	unset($grouparrn);
	unset($pidarr);
	unset($areaarr);
	unset($langarr);
}

else
{
	showErr('System','未找到指定系统模块');
}
?>