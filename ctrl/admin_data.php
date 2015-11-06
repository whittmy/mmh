<?php
require(dirname(__FILE__) .'/admin_conn.php');
chkLogin();

$name = be("all","name");
$ac = be("all","ac");
$flag = be("all","flag");
$show = intval(be("all","show"));
$id = be("all","id");
$tab=be('all','tab');
$colid=be('all','colid');
$col=be('all','col');
$val=trim(be('all','val'));
$ajax=be('all','ajax');


function db_error(){
    $msg = $db->error();
    @$db->query("ROLLBACK");
    $db->close();
    die('{"status":"'.$msg.'"}');
}

if($ac=='checkcache'){
	$res='no';
	if(file_exists(MAC_ROOT.'/cache/cache_data.lock')){
		$res='haved';
	}
	echo $res;
}

else if($ac=='getinfo')
{
	$tab = be("all","tab");
	$col = be("all","col");
	$val = be("all","val");
	$col2 = be("all","col2");
	$val2 = be("all","val2");
	$sql = "SELECT * from {pre}".$tab." WHERE ".$col."=".$val;
	if(!empty($col2)){
		$sql.=' and '.$col2 ."=".$val2;
	}
	$rs = $db->queryArray($sql,false);
	$nums = count($rs);
	$str = json_encode($rs);
	
	if($nums==0){
		echo '[]';
	}
	elseif($nums==1){
		echo substr($str,1,strlen($str)-2);
	}
	else{
		echo $str;
	}
	unset($rs);
}

else if($ac=='save'){
	$flag = be("all","flag");
	$upcache=false;
	$ismake=false;
	$js='';
	$backurl='';
    $msg_diy = '';

	switch($tab)	{
		case "link" :
			$id = be("all","l_id");
			$colarr = array("l_name","l_type","l_url","l_sort","l_logo");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if (!isNum($valarr['l_sort'])) { $valarr['l_sort'] = $db->getOne("SELECT MAX(l_sort) FROM {pre}link")+1; }
			$where = "l_id=".$id;
			break;
		case "vod_cata" :
			$id = be("all","t_id");
			$colarr = array("t_name","t_enname","t_sort","t_pid","t_tpl",'t_tpl_list',"t_tpl_vod","t_tpl_play","t_tpl_down","t_key","t_des","t_title");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if(isN($valarr['t_enname'])) { $valarr['t_enname'] = Hanzi2Pinyin($valarr['t_name']);}
			if(!isNum($valarr['t_sort'])) { $valarr['t_sort'] = $db->getOne("SELECT MAX(t_sort) FROM {pre}vod_cata")+1; }
			$where = "t_id=".$id;
			$upcache=true;
			break;
        case "vod_restype":
            $id = be("all", "orgid");
            $t_id = be("all", "t_id");
            $t_name = be("all", "t_name");
            //exit("$id---$t_id---$t_name");
            $colarr = array('t_id', 't_name');
            $valarr = array('t_id'=>$t_id, 't_name'=>$t_name);
            $where = "t_id=".$id;
            $upcache = true;
            $msg_diy = 'sss';
            break;
		case "vod_class" :
			$id = be("all","c_id");
			$colarr = array("c_name","c_sort","c_pid");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if (!isNum($valarr['c_sort'])) { $valarr['c_sort'] = $db->getOne("SELECT MAX(c_sort) FROM {pre}vod_class where c_pid=".$valarr['c_pid'])+1; }
			$where = "c_id=".$id;
			$upcache=true;
			break;
		case "vod_topic" :
			$uptime = be("all","uptime");
			$id = be("all","t_id");
			$colarr=array('t_name','t_enname','t_sort','t_tpl','t_pic','t_content','t_key','t_des','t_title','t_hide','t_level','t_up','t_down','t_score','t_scoreall','t_scorenum','t_hits','t_dayhits','t_weekhits','t_monthhits','t_addtime','t_time');
			
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if(isN($valarr['t_enname'])) { $valarr['t_enname'] = Hanzi2Pinyin($valarr['t_name']);}
			if(strlen($valarr['t_addtime'])!=10) { $valarr['t_addtime']=time(); $valarr['t_time']= $valarr['t_addtime']; }
    		if($uptime=='1'){ $valarr['t_time'] =time(); }
			$where = "t_id=".$id;
			$upcache=true;
			break;
		case "game_type" :
			$id = be("all","t_id");
			$colarr = array("t_name","t_enname","t_sort","t_pid","t_tpl",'t_tpl_list',"t_tpl_game","t_tpl_play","t_tpl_down","t_key","t_des","t_title");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if(isN($valarr['t_enname'])) { $valarr['t_enname'] = Hanzi2Pinyin($valarr['t_name']);}
			if(!isNum($valarr['t_sort'])) { $valarr['t_sort'] = $db->getOne("SELECT MAX(t_sort) FROM {pre}game_type")+1; }
			$where = "t_id=".$id;
			$upcache=true;
			break;
		case "game_class" :
			$id = be("all","c_id");
			$colarr = array("c_name","c_sort","c_pid");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if (!isNum($valarr['c_sort'])) { $valarr['c_sort'] = $db->getOne("SELECT MAX(c_sort) FROM {pre}game_class where c_pid=".$valarr['c_pid'])+1; }
			$where = "c_id=".$id;
			$upcache=true;
			break;
		case "game_topic" :
			$uptime = be("all","uptime");
			$id = be("all","t_id");
			$colarr=array('t_name','t_enname','t_sort','t_tpl','t_pic','t_content','t_key','t_des','t_title','t_hide','t_level','t_up','t_down','t_score','t_scoreall','t_scorenum','t_hits','t_dayhits','t_weekhits','t_monthhits','t_addtime','t_time');
				
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if(isN($valarr['t_enname'])) { $valarr['t_enname'] = Hanzi2Pinyin($valarr['t_name']);}
			if(strlen($valarr['t_addtime'])!=10) { $valarr['t_addtime']=time(); $valarr['t_time']= $valarr['t_addtime']; }
			if($uptime=='1'){ $valarr['t_time'] =time(); }
			$where = "t_id=".$id;
			$upcache=true;
			break;			
		case "art_type" :
			$id = be("all","t_id");
			$colarr = array("t_name","t_enname","t_sort","t_pid","t_tpl",'t_tpl_list',"t_tpl_art","t_key","t_des","t_title");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if(isN($valarr['t_enname'])) { $valarr['t_enname'] = Hanzi2Pinyin($valarr['t_name']);}
			if (!isNum($valarr['t_sort'])) { $valarr['t_sort'] = $db->getOne("SELECT MAX(t_sort) FROM {pre}art_type")+1; }
			$where = "t_id=".$id;
			$upcache=true;
			break;
		case "art_topic" :
			$uptime = be("all","uptime");
			$id = be("all","t_id");
			$colarr=array('t_name','t_enname','t_sort','t_tpl','t_pic','t_content','t_key','t_des','t_title','t_hide','t_level','t_up','t_down','t_score','t_scoreall','t_scorenum','t_hits','t_dayhits','t_weekhits','t_monthhits','t_addtime','t_time');
			
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if(isN($valarr['t_enname'])) { $valarr['t_enname'] = Hanzi2Pinyin($valarr['t_name']);}
			if(strlen($valarr['t_addtime'])!=10) { $valarr['t_addtime']=time(); $valarr['t_time']= $valarr['t_addtime']; }
    		if($uptime=='1'){ $valarr['t_time'] =time(); }
			$where = "t_id=".$id;
			$upcache=true;
			break;
		case "gbook":
			$id = be("all","g_id");
			$colarr = array("g_reply","g_replytime");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			$valarr['g_replytime'] = time();
			$where = "g_id=".$id;
			break;
		case "comment":
			$id = be("all","c_id");
			$colarr = array("c_content");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			$where = "c_id=".$id;
			break;
		case "manager":
			$id = be("all","m_id");
			$m_password = be("all","m_password");
			
			if( $m_password !=""){
				$colarr = array("m_name","m_password","m_levels","m_status");
			}
			else{
				$colarr = array("m_name","m_levels","m_status");
			}
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				if($n!='m_levels'){
					$valarr[$n]=be("all",$n);
				}
			}
			$valarr['m_levels'] = be("arr","m_levels");
			if( $m_password !=""){ $valarr['m_password'] = md5($m_password); }
			$where = "m_id=".$id;
			break;
		case "user_group":
			$id = be("all","ug_id");
			$colarr = array("ug_name","ug_type","ug_popedom","ug_upgrade","ug_popvalue");
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				if($n!='ug_type' && $n!='ug_popedom'){
					$valarr[$n]=be("all",$n);
				}
			}
			
			$str=be("arr","ug_type");
			$arr = explode(",",$str);
			$ug_type=",";
			for ($i=0;$i<count($arr);$i++){
				if(trim($arr[$i]) !=""){
					$ug_type = $ug_type. trim($arr[$i]) . ",";
				}
			}
			$ug_type = str_replace(",,",",",$ug_type);
			if($ug_type==","){ $ug_type="";}
			$valarr['ug_type'] = $ug_type;
			
			
			$str=be("arr","ug_popedom");
			$arr = explode(",",$str);
			$ug_popedom=",";
			for ($i=0;$i<count($arr);$i++){
				if(trim($arr[$i]) !=""){
					$ug_popedom = $ug_popedom . trim($arr[$i]) . ",";
				}
			}
			$ug_popedom = str_replace(",,",",",$ug_popedom);
			if($ug_popedom==","){ $ug_popedom="";}
			$valarr['ug_popedom'] = $ug_popedom;
			
			$where = "ug_id=".$id;
			$upcache=true;
			break;
		case "user":
			$id = be("all","u_id");
			$u_password = be("all","u_password");
			
			if($u_password!=""){
				$colarr = Array("u_name","u_group","u_password","u_email","u_qq","u_phone","u_question","u_answer","u_status","u_points","u_start","u_end","u_flag");
			}
			else{
				$colarr = Array("u_name","u_group","u_email","u_qq","u_phone","u_question","u_answer","u_status","u_points","u_start","u_end","u_flag");
			}
			
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			if($u_password!=""){
				$valarr['u_password']= md5($u_password); 
			}
			if($valarr['u_flag']==1){
				$u_start= strtotime(be("all","u_starttime"));
			    $u_end = strtotime(be("all","u_endtime"));
			}
			else{
				$u_start= ip2long(be("all","u_startip"));
				$u_end = ip2long(be("all","u_endip"));
			}
			$valarr['u_start']=$u_start;
			$valarr['u_end']=$u_end;
			$where = "u_id=".$id;
			break;
		case "user_card":
			$num=be('all','num');
			$c_money=be('all','c_money');
			$c_point=be('all','c_point');
			$num = intval($num);
			$colarr = array('c_number','c_pass','c_money','c_point','c_addtime');
			for($i=0;$i<$num;$i++){
				$c_number = getRndStr(16);
				$c_pass = getRndStr(8);
				$c_addtime= time();
				$valarr = Array($c_number,$c_pass,$c_money,$c_point,$c_addtime);
				$db->Add ('{pre}user_card',$colarr,$valarr);
			}
			$flag='ok';
			break;
		case "art":
			$id = be("all","a_id");
			$uptag = be("all","uptag");
			$uptime = be("all","uptime");
			
			$colarr=array('a_name', 'a_subname', 'a_enname', 'a_letter', 'a_color', 'a_from', 'a_author', 'a_tag', 'a_pic', 'a_type', 'a_level', 'a_hide', 'a_lock', 'a_up', 'a_down', 'a_hits', 'a_dayhits', 'a_weekhits', 'a_monthhits', 'a_addtime', 'a_time', 'a_hitstime', 'a_maketime', 'a_remarks', 'a_content');
			
			
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
			
			if(strlen($valarr['a_addtime'])!=10) { $valarr['a_addtime']=time(); $valarr['a_time']= $valarr['a_addtime']; }
    		if($uptime=='1'){ $valarr['a_time'] =time(); }
    		if(isN($valarr['a_enname'])) { $valarr['a_enname'] = Hanzi2Pinyin($valarr['a_name']);}
			if(isN($valarr['a_letter'])) { $valarr['a_letter'] = strtoupper(substring($valarr['a_enname'],1)); }
			$valarr['a_content'] = be('arr','a_content','[art:page]');
			if($uptag=='1' && $valarr['a_tag']==''){
				$valarr['a_tag'] = getTag($valarr['a_name'],$valarr['a_content']);
			}
			if(empty($valarr['a_remarks'])){
				$valarr['a_remarks'] = substring(strip_tags($valarr['a_content']),100);
			}
    		unset($pinyins);
    		$where = "a_id=".$id;
    		if($GLOBALS['MAC']['view']['artdetail']==2){
		    	$ismake=true;
		    	$js = '<img width=0 height=0 src="index.php?m=make-info-tab-art-no-{id}" />';
		    }
			break;
		case "vod":
            //表单数据处理   很多知识点
            //增加与删除都牵涉3个表， vod, vod_libs, r表
            $tb_r = be("all", "tb_r");
            $id = be("all","d_id");
			$uptag = be("all","uptag");
			$uptime = be("all","uptime");

            //将要更新的一些列，都放在该数组内
			$colarr=array('d_name', 'd_subname', 'd_episode', 'd_pids'/*, 'd_epname','d_eppic'*/, 'd_enname', 'd_letter', 'd_color', 'd_pic', 'd_picthumb', 'd_picslide', 'd_starring', 'd_directed', 'd_tag', 'd_remarks', 'd_area', 'd_lang', 'd_year', 'd_type', 'd_type_expand' , 'd_class', 'd_topic', 'd_hide', 'd_lock', 'd_state', 'd_level', 'd_usergroup', 'd_stint', 'd_stintdown', 'd_hits', 'd_dayhits', 'd_weekhits', 'd_monthhits', 'd_duration', 'd_up', 'd_down', 'd_score','d_scoreall', 'd_scorenum', 'd_addtime', 'd_time', 'd_hitstime', 'd_maketime', 'd_content', 'd_playfrom', 'd_playserver', 'd_playnote'/*, 'd_playurl'*/, 'd_downfrom', 'd_downserver', 'd_downnote', 'd_downurl');
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}

            //获取剧情(多个复选框)的相关设置数据
            $valarr['d_class'] = be('arr','d_class');
            if(!empty($valarr['d_class'])){
                $valarr['d_class'] = ','.$valarr['d_class'].',';
            }

            //rocking 获取多类别信息(多复选框), 表单名为 数组， 以数组形式上传
            $valarr['d_pids'] = be('arr', 'd_pids');
            if(!empty($valarr['d_pids'])){
                $valarr['d_pids'] = ','.$valarr['d_pids'].',';
            }

            //rocking 获取剧集标题和图片
//            $valarr['d_epname'] =  str_replace("\r\n", '#', be('all', 'epname')); //处理换行
//            $valarr['d_eppic'] = str_replace("\r\n", '#', be('all', 'eppic'));


			if(strlen($valarr['d_addtime'])!=10) { $valarr['d_addtime']=time(); }
    		if($valarr['d_time']!=''){  $valarr['d_time']= strtotime($valarr['d_time']); } else { $valarr['d_time']= $valarr['d_addtime']; }
    		if($uptime=='1'){ $valarr['d_time'] =time(); }
    		if(isN($valarr['d_enname'])) { $valarr['d_enname'] = Hanzi2Pinyin($valarr['d_name']);}
			if(isN($valarr['d_letter'])) { $valarr['d_letter'] = strtoupper(substring($valarr['d_enname'],1)); }
			unset($pinyins);
			if($uptag=='1' && $valarr['d_tag']==''){
				$valarr['d_tag'] = getTag($valarr['d_name'],$valarr['d_content']);
			}

            //播放集信息
			$playurl=be('arr', 'playurl',',,,');  //各源的播放地址框， 用 ',,,'分隔
            $playfrom=be('arr', 'playfrom');    //默认以 ','分隔
            $playserver=be('arr', 'playserver');
            $playnote=be('arr','playnote');

		    $playurlarr=explode(',,,',$playurl);
            $playfromarr=explode(',',$playfrom);
            $playserverarr=explode(',',$playserver);
		    $playnotearr=explode(',',$playnote);

            //计算 源(组)数
		    $playurlarrlen=count($playurlarr);
            $playfromarrlen=count($playfromarr);
            $playserverarrlen=count($playserverarr);
		    if(isN($playurl)) { $playurlarrlen=-1; }

            //不管内容是否为空都要显示,取最大值
            $playurlarrlen = max($playurlarrlen, $playfromarrlen);
            $playfromarrlen = $playurlarrlen;


            //download相关的，忽略
		    $downurl=be('arr', 'downurl',',,,'); $downfrom=be('arr', 'downfrom'); $downserver=be('arr', 'downserver');$downnote=be('arr', 'downnote');
		    $downurlarr=explode(',,,',$downurl); $downfromarr=explode(',',$downfrom); $downserverarr=explode(',',$downserver);
		    $downnotearr=explode(',',$downnote);
		    $downurlarrlen=count($downurlarr); $downfromarrlen=count($downfromarr); $downserverarrlen=count($downserverarr);
		    if(isN($downurl)) { $downurlarrlen=-1; }
		    
		    $rc = false;
            $sql_insert_libs = '';
            $epCnt = 0; //集数，不按 序号来，取url时更安全些
		    for ($i=0;$i<$playfromarrlen;$i++){
		        if ($playurlarrlen >= $i){
		        	//if(trim($playfromarr[$i])!='no'){  //即便没有选择 播放源，也要保存数据， 没有选择播放源时，其值为 no
				        if ($rc){ /*$d_playurl .= '$$$';*/ $d_playfrom .= '$$$'; $d_playserver .= '$$$'; $d_playnote.='$$$'; }
                        $src = trim($playfromarr[$i]);
				        $d_playserver .= trim($playserverarr[$i]);
				        $d_playnote .=  trim($playnotearr[$i]);
				        //$d_playurl .= str_replace(chr(13),'#',str_replace(chr(10),'',trim($playurlarr[$i])));  //\r\n

                        $linearr = explode(chr(13), str_replace(chr(10),'',trim($playurlarr[$i]," \r\n")));
                        if(empty($linearr[0]))
                            continue;
                        $d_playfrom .= $src;

                        if($epCnt< count($linearr)){
                            $epCnt = count($linearr);
                        }
                        foreach($linearr as $line){
                            $line = trim($line);
                            if(empty($line)){
                                continue;
                            }
                            $itemarr = explode('|', $line);
                            if(count($itemarr)!=4){
                                exit('error-format:'.$itemarr[0].','.$itemarr[1].'....!!!');
                            }

                            $idx = trim($itemarr[0]);
                            $name = trim($itemarr[1]);
                            $pic = trim($itemarr[2]);
                            $url = trim($itemarr[3]);
                            if(empty($sql_insert_libs)){
                                $sql_insert_libs = 'insert into {pre}vod_libs (l_pid,l_idx,l_pic,l_downurl,l_name,l_src) values ';
                            }
                            //此处不能的$id不能被使用，因为 add操作时，该$id为空；edit操作没有问题，但是不能兼容。
                            //所以此处替换成表示符，具体使用时再替换。 暂定 ## 替代 $id
                            $sql_insert_libs .= "(##,$idx,'$pic','$url','".addslashes($name)."','$src'),";
                        }
				        $rc =true;
			        //}
		        }
		    }
            $sql_insert_libs = trim($sql_insert_libs, ',');

		    $rc = false;
		    for ($i=0;$i<$downfromarrlen;$i++){
		        if ($downfromarrlen >= $i){
		        	if(trim($downfromarr[$i])!='no'){
				        if ($rc){ $d_downurl .= '$$$'; $d_downfrom .= '$$$'; $d_downserver .= '$$$'; $d_downnote.='$$$';  }
				        $d_downfrom .= trim($downfromarr[$i]);
				        $d_downserver .= trim($downserverarr[$i]);
				        $d_downnote .=  trim($downnotearr[$i]);
				        $d_downurl .= str_replace(chr(13),'#',str_replace(chr(10),'',trim($downurlarr[$i])));
				        $rc =true;
				    }
		        }
		    }

            $valarr['d_episode'] = $epCnt;
		    $valarr['d_playfrom']=trim($d_playfrom, '$ ');
		    $valarr['d_playserver']=trim($d_playserver, '$ ');
		    $valarr['d_playnote']=trim($d_playnote, '$ ');
		    //$valarr['d_playurl']=$d_playurl;
		    $valarr['d_downfrom']=trim($d_downfrom, '$ ');
		    $valarr['d_downserver']=trim($d_downserver, '$ ');
		    $valarr['d_downnote']=trim($d_downnote, '$ ');
		    $valarr['d_downurl']=trim($d_downurl, '$ ');
		    $where = "d_id=".$id;
		    if($GLOBALS['MAC']['view']['voddetail']==2 || $GLOBALS['MAC']['view']['vodplay']==2 || $GLOBALS['MAC']['view']['voddown']==2){
		    	$ismake=true;
		    	$js = '<img width=0 height=0 src="index.php?m=make-info-tab-vod-no-{id}" />';
		    }
			break;
		
		case "game":
			$id = be("all","d_id");
			$uptag = be("all","uptag");
			$uptime = be("all","uptime");
				
			$colarr=array('d_name', 'd_packname', 'd_version','d_enname', 'd_ext','d_letter', 'd_color', 'd_pic','d_tag', 'd_remarks', 'd_year', 'd_type', 'd_type_expand' , 'd_class', 'd_topic', 'd_hide', 'd_lock', 'd_level', 'd_usergroup', 'd_stint', 'd_stintdown', 'd_hits', 'd_dayhits', 'd_weekhits', 'd_monthhits', 'd_size', 'd_up', 'd_down', 'd_score','d_scoreall', 'd_scorenum', 'd_addtime', 'd_time', 'd_hitstime', 'd_maketime', 'd_content', 'd_downnote', 'd_downurl', 'd_img1','d_img2','d_img3','d_img4');
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=be("all",$n);
			}
				
			if(strlen($valarr['d_addtime'])!=10) { $valarr['d_addtime']=time(); }
			if($valarr['d_time']!=''){  $valarr['d_time']= strtotime($valarr['d_time']); } else { $valarr['d_time']= $valarr['d_addtime']; }
			if($uptime=='1'){ $valarr['d_time'] =time(); }
			if(isN($valarr['d_enname'])) { $valarr['d_enname'] = Hanzi2Pinyin($valarr['d_name']);}
			if(isN($valarr['d_letter'])) { $valarr['d_letter'] = strtoupper(substring($valarr['d_enname'],1)); }
			unset($pinyins);
			if($uptag=='1' && $valarr['d_tag']==''){
				$valarr['d_tag'] = getTag($valarr['d_name'],$valarr['d_content']);
			}
				
// 			$valarr['d_img1']=;
// 			$valarr['d_img2']=;
// 			$valarr['d_img3']=;
// 			$valarr['d_img4']=;
			
			$downurl=be('arr', 'downurl');$downnote=be('arr', 'downnote');
	
			$valarr['d_class'] = be('arr','d_class');
			if(!empty($valarr['d_class'])){
				$valarr['d_class'] = ','.$valarr['d_class'].',';
			}

			$valarr['d_downnote']=$downnote;
			$valarr['d_downurl']=$downurl;
			$where = "d_id=".$id;
			if($GLOBALS['MAC']['view']['gamedetail']==2 || $GLOBALS['MAC']['view']['gameplay']==2 || $GLOBALS['MAC']['view']['gamedown']==2){
				$ismake=true;
				$js = '<img width=0 height=0 src="index.php?m=make-info-tab-game-no-{id}" />';
			}
			break;			
	}

    //由于涉及到同时对两表进行修改，为保证数据同步，需要采用事务处理
    //而且两个表的引擎必须支持事务才行，InnoDB
    //更新用于索引的分类信息，
    $dbOpOk = true;

    if($flag=="add" || $flag=="edit"){
        //>>>>>>>>>> 事务开始
        $db->query("BEGIN");

        if($flag=="add"){
            if(!$db->Add('{pre}'.$tab,$colarr,$valarr)){
                db_error();
            }
            $id=$db->insert_id(); //rocking
            if($ismake){
                //rocking $id=$db->insert_id();
                $js = str_replace('{id}',$id,$js);
            }
        }
        elseif($flag=="edit"){
            $backurl=be("all","backurl");
            if(!$db->Update('{pre}'.$tab,$colarr,$valarr,$where,1)){
                db_error();
            }
            if($ismake){
                $js = str_replace('{id}',$id,$js);
            }
        }

        //不管是 add还是 edit,此时的 $id 都不再为空(假定 $res3执行无误)
        //这是仅针对 vod表操作时才会涉及到的 分类表更改
        if($tab == "vod") {
            //先删除所属分类,r表
            //echo("delete from {pre}$tb_r where r_did=$id <br>");
            if(!$db->query("delete from {pre}$tb_r where r_did=$id")){
                db_error();
            }

            //再根据d_pids的值进行插入  !!!!!!!!! 若是 新增，则此时r_did还不存在
            $pidarr = explode(',', $valarr['d_pids']);
            foreach ($pidarr as $pid) {
                $pid = trim($pid);
                if (strlen($pid) > 0) {
                    if ($sql == null) {
                        $sql = "insert into {pre}$tb_r (r_cid, r_did) values ";
                    }
                    $sql .= "($pid, $id),";
                }
            }
            if (!empty($sql)) {
                //echo rtrim($sql, ', ').'<br>';
                if(!$db->query(rtrim($sql, ', '))){
                    db_error();
                }
            }

            //更新libs表内的集信息,先清 再加
            if(!$db->query('delete from {pre}vod_libs where l_pid='.$id)){
                db_error();
            }

            if(!empty($sql_insert_libs)) {
                $sql_insert_libs = strtr($sql_insert_libs, array('##'=>$id));
                //exit($sql_insert_libs);
                if(!$db->query($sql_insert_libs)){
                    db_error();
                }
            }
            //echo($sql_insert_libs.'<br>');
        }

        $db->query("COMMIT");
        $db->query("END");
        //<<<<<<<<<<<<<<<<<<<<<<
    }

    //exit("$res1----$res2---$res3");
    if($upcache){ updateCacheFile(); }

    if(!empty($msg_diy)){
       exit('{"status":"ok"}');
    }

    showMsg('数据已保存'.$js,$backurl);
}

else if($ac=='del')
{   // 删除操作
	$upcache=false;
	switch($tab)
	{
		case "art":
			$col="a_id";
			$ids = be("get","a_id");
			if(isN($ids)){
				$ids= be("arr","a_id");
			}
			break;
		case "vod":
			$col="d_id";
			$ids = be("get","d_id");
			if(isN($ids)){
				$ids= be("arr","d_id");
			}
			break;
			
		case "game":
			$col="d_id";
			$ids = be("get","d_id");
			if(isN($ids)){
				$ids= be("arr","d_id");
			}
			break;			
		case "link" :
			$col="l_id";
			$ids = be("get","l_id");
			if(isN($ids)){
				$ids= be("arr","l_id");
			}
			break;
        case "vod_restype":
            $col="t_id";
            $ids = be("get","t_id");



            break;
        case "vod_cata":
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$arr=explode(',',$ids);
			foreach($arr as $a){
				$cc = $db->getOne('select count(*) from {pre}vod_cata where t_pid='.$a);
				if($cc>0){
					showMsg('请先删除本类下面的子栏目','');
					return;
				}
				$cc = $db->getOne('select count(*) from {pre}vod where d_type='.$a);  //?????????
				if($cc>0){
					showMsg('请先删除本类下面的视频','');
					return;
				}
			}
			$upcache=true;
			break;
		case "vod_class":
			$col="c_id";
			$ids = be("get","c_id");
			if(isN($ids)){
				$ids= be("arr","c_id");
			}
			$upcache=true;
			break;
		case "vod_topic" :
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$arr=explode(',',$ids);
			foreach($arr as $a){
				$cc = $db->getOne('select count(*) from {pre}vod_relation where r_type=2 and r_a='.$a);
				if($cc>0){
					showMsg('请先删除本专题下面的视频','');
					return;
				}
			}
			$upcache=true;
			break;
		case "game_type":
				$col="t_id";
				$ids = be("get","t_id");
				if(isN($ids)){
					$ids= be("arr","t_id");
				}
				$arr=explode(',',$ids);
				foreach($arr as $a){
					$cc = $db->getOne('select count(*) from {pre}game_type where t_pid='.$a);
					if($cc>0){
						showMsg('请先删除本类下面的子栏目','');
						return;
					}
					$cc = $db->getOne('select count(*) from {pre}game where d_type='.$a);
					if($cc>0){
						showMsg('请先删除本类下面的视频','');
						return;
					}
				}
				$upcache=true;
				break;
		case "game_class":
				$col="c_id";
				$ids = be("get","c_id");
				if(isN($ids)){
					$ids= be("arr","c_id");
				}
				$upcache=true;
				break;
		case "game_topic" :
				$col="t_id";
				$ids = be("get","t_id");
				if(isN($ids)){
					$ids= be("arr","t_id");
				}
				$arr=explode(',',$ids);
				foreach($arr as $a){
					$cc = $db->getOne('select count(*) from {pre}game_relation where r_type=2 and r_a='.$a);
					if($cc>0){
						showMsg('请先删除本专题下面的视频','');
						return;
					}
				}
				$upcache=true;
				break;
		case "art_type" :
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$arr=explode(',',$ids);
			foreach($arr as $a){
				$cc = $db->getOne('select count(*) from {pre}art_type where t_pid='.$a);
				if($cc>0){
					showMsg('请先删除本类下面的子栏目','');
					return;
				}
				$cc = $db->getOne('select count(*) from {pre}art where a_type='.$a);
				if($cc>0){
					showMsg('请先删除本类下面的视频','');
					return;
				}
			}
			$upcache=true;
			break;
		case "art_topic" :
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$arr=explode(',',$ids);
			foreach($arr as $a){
				$cc = $db->getOne('select count(*) from {pre}art_relation where r_type=2 and r_a='.$a);
				if($cc>0){
					showMsg('请先删除本专题下面的视频','');
					return;
				}
			}
			$upcache=true;
			break;
		case "gbook":
			$col="g_id";
			$ids = be("get","g_id");
			if(isN($ids)){
				$ids= be("arr","g_id");
			}
			break;
		case "manager":
			$col="m_id";
			$ids = be("get","m_id");
			if(isN($ids)){
				$ids= be("arr","m_id");
			}
			break;
		case "user_group":
			$col="ug_id";
			$ids = be("get","ug_id");
			if(isN($ids)){
				$ids= be("arr","ug_id");
			}
			$upcache=true;
			break;
		case "user":
			$col="u_id";
			$ids = be("get","u_id");
			if(isN($ids)){
				$ids= be("arr","u_id");
			}
			break;
		case "user_card":
			$col="c_id";
			$ids = be("get","c_id");
			if(isN($ids)){
				$ids= be("arr","c_id");
			}
			break;
		case "comment":
			$col="c_id";
			$ids = be("get","c_id");
			if(isN($ids)){
				$ids= be("arr","c_id");
			}
			break;
	}

    //>>>>>>>>>> 事务开始
    $db->query("BEGIN");

    if (!isN($ids)) {
        if(!$db->Delete('{pre}'.$tab, $col." in (".$ids.")")){
            db_error();
        }
        if($tab == 'vod') {
            //删除某部影片时，需要同时删除其 r表关系、以及libs表中的分集信息
            if(!$db->Delete('{pre}vod_r_type_dir', "r_did in (".$ids.")")){
                db_error();
            }
            if(!$db->Delete('{pre}vod_libs', "l_pid in (".$ids.")")){
                db_error();
            }
        }
    }

    $db->query("COMMIT");
    $db->query("END");

	if ($upcache){ updateCacheFile(); }

	redirect ( getReferer() );
}

elseif($ac=='clear')
{
    $sql='truncate TABLE {pre}'.$tab;
	$db->query($sql);
	redirect( getReferer() );
}
else if($ac=='device_res_op'){
    //$flag / $tab
    switch($flag) {
        case 'add_top':
            $t_id = be("all", "t_id");
            $t_name = be("all", "t_name");

            if(strlen($t_id) == 0){
                $colarr = array('t_name');
                $valarr = array('t_name'=>$t_name);
            }
            else{
                $colarr = array('t_id', 't_name');
                $valarr = array('t_id'=>$t_id, 't_name'=>$t_name);
            }

            $res = $db->Add('{pre}'.$tab,$colarr,$valarr);
            if($res)
                exit('{"status":"ok"}');
            else
                exit('{"status":"error"}');

            break;
        case 'add_sub':
            // 对顶层类别进行扩展 子类/全部
            $t_belong = be("all", "t_belong");
            $alias = trim(be('all', 'alias'));
            $t_id = be("all", 't_id');
            $idstr = trim(be("all", 'ids'));
            if(strlen($idstr) == 0
                || ($t_belong=='sub'&&strlen($alias)==0)
                || strlen($t_id) == 0){
                exit('{"status":"失败:有空参数"}');
            }

            $sql = "select * from {pre}$tab where t_id=$t_id";
            $res = $db->query($sql);
            $sql = '';
            if($row=$db->fetch_array($res)){
                if($t_belong == 'sub'){
                    $t_subdes_arr = explode(',', trim($row['t_sub_desc'], ', '));
                    if(in_array($alias, $t_subdes_arr)){
                        exit('{"status":" 别名 \''.$alias.'\' 已存在"}');
                    }
                    array_push($t_subdes_arr, $alias);
                    $t_subdes_str = trim(implode(',', $t_subdes_arr), ', ');

                    $t_subcls_str = trim($row['t_subclses'], ', ');
                    $t_subcls_arr = explode(',', $t_subcls_str);
                    if(in_array($idstr, $t_subcls_arr)){
                        exit('{"status":" 你选择的类别已存在"}');
                    }
                    $t_subcls_arr = array_unique($t_subcls_arr);
                    array_push($t_subcls_arr, $idstr);
                    $t_subcls_str = trim(implode(',', $t_subcls_arr), ', ');

                    $sql = "update {pre}$tab set t_subclses='$t_subcls_str', t_sub_desc='$t_subdes_str' where t_id=$t_id";
                }
                else if($t_belong == 'all'){
                    $t_allcls_str = trim($row['t_allclses'].','.$idstr, ', ');
                    $t_allcls_str = str_replace('_', ',', $t_allcls_str);
                    $t_allcls_arr = explode(',', $t_allcls_str);
                    $t_allcls_arr = array_unique($t_allcls_arr);
                    $t_allcls_str = trim(implode(',', $t_allcls_arr), ', ');

                    $sql = "update {pre}$tab set t_allclses='$t_allcls_str' where t_id=$t_id";
                }
            }
            //exit($sql);
            if(!empty($sql)){
                $res = $db->query($sql);
                if($res)
                    exit('{"status":"ok"}');
            }
            exit('{"status":"error"}');
            break;
        case 'clear_sub':
            //情况vod_device_res_mgr 表下面某条目 子类/全部 下面的所有内容。
            $t_id= be("all", 't_id');
            $t_belong = be('all', 't_belong');
            if($t_belong == 'all')
                $sql = "update {pre}$tab set t_allclses='', t_all_desc='' where t_id=$t_id";
            else if($t_belong == 'sub')
                $sql = "update {pre}$tab set t_subclses='', t_sub_desc='' where t_id=$t_id";

            if(!empty($sql))
                $db->query($sql);
            redirect(getReferer());
            break;
        case 'del_top':
            $t_id = be('all', 't_id');
            if(is_numeric($t_id)){
                $sql = "delete from {pre}$tab where t_id=$t_id";
                $db->query($sql);

                redirect(getReferer());
            }
            break;
        case 'del_item':
            //admin_data.php?ac=set&tab=vod_device_res_mgr&t_pid=3&t_id=25340&t_belong=sub
            $t_pid = be('all', 't_pid');
            $t_id = be('all', 't_id'); //可能是 xx_yy 的形式
            $t_belong = be('all', 't_belong');
            if($t_belong == 'all'){
                $sql = "select t_allclses from {pre}$tab where t_id=$t_pid";
                $rs = $db->query($sql);
                $sql = '';
                while ($row = $db->fetch_array($rs)){
                    $t_allclses = $row['t_allclses'];
                    $t_allclses = str_replace($t_id, '', $t_allclses);
                    $t_allclses = str_replace('_,', ',', $t_allclses);
                    $t_allclses = str_replace(',_', ',', $t_allclses);
                    $t_allclses = str_replace(',,', ',', $t_allclses);
                    $t_allclses = trim($t_allclses, '_,');

                    $sql = "update {pre}$tab set t_allclses='$t_allclses' where t_id=$t_pid";
                    break;
                }
            }
            elseif($t_belong == 'sub'){
                $sql = "select t_subclses, t_sub_desc from {pre}$tab where t_id=$t_pid";
                $rs = $db->query($sql);
                $sql = '';
                while ($row = $db->fetch_array($rs)){
                    $t_sub_desc = $row['t_sub_desc'];
                    $t_sub_descArr = explode(',', $t_sub_desc);

                    $t_subclses = $row['t_subclses'];
                    $t_subclsesArr = explode(',', $t_subclses);

                    $rt = array_keys($t_subclsesArr, $t_id);
                    if(count($rt) > 0){
                        $idx = $rt[0];
                        array_splice($t_subclsesArr,$idx,1);
                        array_splice($t_sub_descArr,$idx,1);
                    }

                    $t_subclses = implode(',', $t_subclsesArr);
                    $t_sub_desc = implode(',', $t_sub_descArr);

                    $sql = "update {pre}$tab set t_subclses='$t_subclses', t_sub_desc='$t_sub_desc' where t_id=$t_pid";
                    break;
                }
            }

            if(!empty($sql))
               $db->query($sql);

            redirect( getReferer() );
            break;
    }

}

elseif($ac=='set')
{
    $sql='UPDATE {pre}'.$tab.' set '.$col.'='.$val.' WHERE '.$colid .' IN('.$id.')';
	$db->query($sql);
	redirect( getReferer() );
}

elseif($ac=='getfields')
{
	if(empty($tab)) { echo '[]'; }
	$rs = $db->query('SHOW COLUMNS FROM '.$tab);
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row['Field'];
	}
	unset($rs);
	echo json_encode($dbarr);;
}

elseif($ac=='getexpandtype')
{
	echo '';
}

elseif($ac=='getclass')
{
	$class = be("all",'class');
	
	$typearr = $MAC_CACHE['vodcata'][$id];
	if($typearr['t_pid']>0){
		$id=$typearr['t_pid'];
	}
	$ids = $class;
	
	$valarr=array();
	$typearr = $MAC_CACHE['vodclass'];
	foreach($typearr as $a){
		if($a['c_pid']==$id){
			$arr=array();
			$arr['id'] = $a['c_id'];
			$arr['name'] = $a['c_name'];
			$arr['chk'] = strpos(',,'.$ids.',,',','.$a['c_id'].',') ? 'true' : 'false';
			array_push($valarr,$arr);
		}
	}
	echo json_encode($valarr);
}
elseif($ac=='getpids'){
    $pidsstr = be("all", "pids");
    $pidarr = explode(",", $pidsstr);

    $valarr=array();
    foreach( $MAC_CACHE['vodcata'] as $type){
        if(in_array($type['t_id'], $pidarr)){
            $arr = array();
            $arr['id'] = $type['t_id'];
            $arr['name'] = $type['t_name'];
            $arr['chk'] =  'true';
            array_push($valarr,$arr);
        }
    }

    //test
//    $arr = array();
//    $arr['id'] = '12';
//    $arr['name']='test';
//    $arr['chk']= 'true';
//    array_push($valarr,$arr);

    echo json_encode($valarr);;
}
elseif($ac=='getgameclass')
{
	$class = be("all",'class');

	$typearr = $MAC_CACHE['gametype'][$id];
	if($typearr['t_pid']>0){
		$id=$typearr['t_pid'];
	}
	$ids = $class;

	$valarr=array();
	$typearr = $MAC_CACHE['gameclass'];
	foreach($typearr as $a){
		if($a['c_pid']==$id){
			$arr=array();
			$arr['id'] = $a['c_id'];
			$arr['name'] = $a['c_name'];
			$arr['chk'] = strpos(',,'.$ids.',,',','.$a['c_id'].',') ? 'true' : 'false';
			array_push($valarr,$arr);
		}
	}
	echo json_encode($valarr);;
}
elseif($ac=='topicdata')
{
	$tid=be('all','tid');
	
	if($tab=='art'){
		$pre='a_';
		if($flag=='add'){
			$nums = $db->getOne('select count(*) from {pre}art_relation where r_type=2 and r_a='.$tid.' and r_b='.$id);
			if($nums==0){
				$db->Add('{pre}art_relation',array('r_type','r_a','r_b'),array(2,$tid,$id));
				$rc=false;
				$a_topic='';
				$rs=$db->query('select r_a from {pre}art_relation where r_type=2 and r_b='.$id);
				while ($row = $db->fetch_array($rs))
				{
					if($rc){ $a_topic.=','; }
					$a_topic .= $row['r_a'];
					$rc=true;
				}
				unset($rs);
				if(!empty($a_topic)){ $a_topic = ','.$a_topic.','; }
				$db->Update('{pre}art',array('a_topic'),array($a_topic),'d_id='.$id);
			}
		}
		elseif($flag=='del'){
			$sql='delete from {pre}art_relation where r_type=2 and r_a='.$tid.' and r_b='.$id;
			$db->query($sql);
			$sql='update {pre}art set a_topic=replace(a_topic,\''.$tid.'\',\'\') where a_id='.$id;
			$db->query($sql);
		}
		$sql='select a_id,a_name,a_enname,a_type,a_author from {pre}art_relation t inner join {pre}'.$tab.' d on d.a_id=t.r_b where t.r_type=2 and t.r_a='.$tid;
	}
	else if($tab=='vod'){
		$pre="d_";
		if($flag=='add'){
			$nums = $db->getOne('select count(*) from {pre}vod_relation where r_type=2 and r_a='.$tid.' and r_b='.$id);
			if($nums==0){
				$db->Add('{pre}vod_relation',array('r_type','r_a','r_b'),array(2,$tid,$id));
				$rc=false;
				$d_topic='';
				$rs=$db->query('select r_a from {pre}vod_relation where r_type=2 and r_b='.$id);
				while ($row = $db->fetch_array($rs))
				{
					if($rc){ $d_topic.=','; }
					$d_topic .= $row['r_a'];
					$rc=true;
				}
				unset($rs);
				if(!empty($d_topic)){ $d_topic = ','.$d_topic.','; }
				$db->Update('{pre}vod',array('d_topic'),array($d_topic),'d_id='.$id);
			}
		}
		elseif($flag=='del'){
			$sql='delete from {pre}vod_relation where r_type=2 and r_a='.$tid.' and r_b='.$id;
			$db->query($sql);
			$sql='update {pre}vod set d_topic=replace(d_topic,\''.$tid.'\',\'\') where d_id='.$id;
			$db->query($sql);
		}
		$sql='select d_id,d_name,d_enname,d_type,d_starring from {pre}vod_relation t inner join {pre}vod d on d.d_id=t.r_b where t.r_type=2 and t.r_a='.$tid;
	}
	else if($tab=='game'){
		$pre="d_";
		if($flag=='add'){
			$nums = $db->getOne('select count(*) from {pre}game_relation where r_type=2 and r_a='.$tid.' and r_b='.$id);
			if($nums==0){
				$db->Add('{pre}game_relation',array('r_type','r_a','r_b'),array(2,$tid,$id));
				$rc=false;
				$d_topic='';
				$rs=$db->query('select r_a from {pre}game_relation where r_type=2 and r_b='.$id);
				while ($row = $db->fetch_array($rs))
				{
					if($rc){ $d_topic.=','; }
					$d_topic .= $row['r_a'];
					$rc=true;
				}
				unset($rs);
				if(!empty($d_topic)){ $d_topic = ','.$d_topic.','; }
				$db->Update('{pre}game',array('d_topic'),array($d_topic),'d_id='.$id);
			}
		}
		elseif($flag=='del'){
			$sql='delete from {pre}game_relation where r_type=2 and r_a='.$tid.' and r_b='.$id;
			$db->query($sql);
			$sql='update {pre}game set d_topic=replace(d_topic,\''.$tid.'\',\'\') where d_id='.$id;
			$db->query($sql);
		}
		$sql='select d_id,d_name,d_enname,d_type from {pre}game_relation t inner join {pre}game d on d.d_id=t.r_b where t.r_type=2 and t.r_a='.$tid;
	}	
	$rs = $db->queryArray($sql,false);
	for($i=0;$i<count($rs);$i++){
		$typearr = $GLOBALS['MAC_CACHE'][$tab.'type'][$rs[$i][$pre.'type']];
	 	$alink = "../".$tpl->getLink($tab,'detail',$typearr,$rs[$i],true);
		$alink = str_replace("../".$MAC['site']['installdir'],"../",$alink);
		if (substring($alink,1,strlen($alink)-1)=="/") { $alink .= "index.". $MAC['app']['suffix'];}
		$rs[$i][$pre.'link'] = $alink;
	}
	$str = json_encode($rs);
	if($str!='[]'){
		echo $str;
		return;
	}
	echo '[]';
}

elseif($ac=='typenow')
{
	if($tab=='art'){
		$pre='a';
	}
	else{
		$pre='d';
	}
	
	$typearr=array();
	$sql='select distinct '.$pre.'_type from {pre}'.$tab.' where '.$pre.'_time >='.strtotime("today");
	$rs = $db->queryarray($sql,false);
	foreach($rs as $a){
		array_push($typearr,$a[$pre.'_type']);
	}
	unset($rs);
	echo join(',',$typearr);
}
elseif($ac=='hide')
{
	if($show==1){
		echo '<select id="val" name="val"><option value="">请选择...</option><option value="0">显示</option><option value="1">隐藏</option></select><input type="button" value="确定" onclick="ajaxsubmit(\''.$ac.'\',\''.$tab.'\',\''.$colid.'\',\''.$col.'\',\''.$id.'\');" class=input> <input type="button" value="取消" onclick="closew();" class=input>';
	}
	else{
		$sql='UPDATE {pre}'.$tab.' set '.$col.'='.$val.' WHERE '.$colid .' IN('.$id.')';
		$db->query($sql);
		if($show==2){
			echo 'reload';
		}
		else{
			redirect( getReferer() );
		}
	}
}

elseif($ac=='shift')
{
    //该功能 先屏蔽，不用
    //对应 资源分类下面的‘转移’按钮，可以将选中的类别下的所有资源移动到指定类别下
	if ($show ==1){
        //首次获取 目标分类的相关资源(html)
		if(strpos($tab,'_cata')){
			$selstr=makeSelectAll("{pre}".$tab,"t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","");
		}
		else{
			$selstr=makeSelect("{pre}".$tab,"t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;","");
		}
		echo '<select id="val" name="val"><option value="0">请选择目标</option>' . $selstr .'</select><input type="button" value="确定" onclick="ajaxsubmit(\''.$ac.'\',\''.$tab.'\',\''.$colid.'\',\''.$col.'\',\''.$id.'\');" class=input> <input type="button" value="取消" onclick="closew();" class=input>';
	}
	else{
        //http://localhost/mmh/ctrl/admin_data.php?ac=shift&tab=vod_cata&colid=t_id&col=&id=25345,25346&show=2&val=25307&val2=undefined
        //用户选择目标类别之后的数据更新请求
        //！！！！！！！！！！
        //但是从下面的代码看，好像是将 某选择分类下的所有资源转到指定类别下面
        //在我现有的架构中比较麻烦该功能先屏蔽

		if(strpos(','.$tab,'vod')) { $tab2='vod'; } 
		elseif(strpos(','.$tab,'game')){ $tab2='game'; }
		else{ $tab2='art'; }
		switch($tab)
		{
			case 'vod_cata':
				$tab2 = 'vod';
				$colid2 = 'd_type';
				break;
			case 'art_type':
				$tab2 = 'art';
				$colid2 = 'a_type';
				break;
			case 'vod_topic':
				$tab2 = 'vod_relation';
				$colid2 = 'r_a';
				$where = ' and r_type=2 ';
				break;
			case 'art_topic':
				$tab2 = 'art_relation';
				$colid2 = 'r_a';
				$where = ' and r_type=2 ';
				break;
			
			case 'game_type':
				$tab2 = 'game';
				$colid2 = 'd_type';
				break;
			case 'game_topic':
				$tab2 = 'game_relation';
				$colid2 = 'r_a';
				$where = ' and r_type=2 ';
				break;
		}
		$db->query ("UPDATE {pre}".$tab2 ." set ". $colid2 ."=".$val. " WHERE " . $colid2 ." IN(".$id.") ".$where );
		echo "reload";
	}
}


elseif($ac=='level')
{
	if ($show==1){
		echo '<select id="val" name="val"><option value="">请选择推荐</option><option value="1">推荐1</option><option value="2">推荐2</option><option value="3">推荐3</option><option value="4">推荐4</option><option value="5">推荐5</option><option value="0">取消推荐</option></select><input type="button" value="确定" onclick="ajaxsubmit(\''.$ac.'\',\''.$tab.'\',\''.$colid.'\',\''.$col.'\',\''.$id.'\');" class=input><input type="button" value="取消" onclick="closew();" class=input>';
	}
	else{
		if($val!=''){
			$sql='UPDATE {pre}'.$tab.' set '.$col.'='.$val.' WHERE '.$colid .' IN('.$id.')';
			$db->query($sql);
			$idarr = explode(",",$id);
			for ($i=0;$i<count($idarr);$i++){
				echo 'level_'.$idarr[$i].'$&nbsp;<img src="../images/icons/ico'.$val.'.gif" border="0" style="cursor: pointer;" onclick="ajaxshow(\'level_'.$idarr[$i].'\',\''.$ac.'\',\''.$tab.'\',\''.$colid.'\',\''.$col.'\',\''.$idarr[$i].'\');" />|||';
			}
		}
	}
}

elseif($ac=='hits')
{
	if ($show ==1){
		echo '<input id="val" name="val" type="text"  size="5">到<input id="val2" name="val2" type="text"  size="5">之间<input type="button" value="确定" onclick="ajaxsubmit(\''.$ac.'\',\''.$tab.'\',\''.$colid.'\',\''.$col.'\',\''.$id.'\');" class=input> <input type="button" value="取消" onclick="closew();" class=input>';
	}
	else{
		$val = be("all","val");
		$val2 = be("all","val2");
		if (!isNum($val)){ $val=1;}
		if (!isNum($val2)){ $val2=1000;}
		
		$rs = $db->query("select ".$colid." from {pre}".$tab." where ".$colid." in (" .$id . ")");
		while($row = $db->fetch_array($rs))
		{
			$num3 = rndNum($val,$val2);
			$db->Update ("{pre}".$tab ,array($col),array($num3) ,$colid."=".$row[$colid]);
		}
		unset($rs);
		echo "reload";
	}
}

elseif($ac=='type')
{
	if ($show ==1){
		echo '<select id="val" name="val"><option value="0">请选择栏目</option>' . makeSelectAll("{pre}".$tab."_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","") .'</select><input type="button" value="确定" onclick="ajaxsubmit(\''.$ac.'\',\''.$tab.'\',\''.$colid.'\',\''.$col.'\',\''.$id.'\');" class=input> <input type="button" value="取消" onclick="closew();" class=input>';
	}
	else{
		$db->query ("UPDATE {pre}".$tab . " set ". $col ."=".$val. " WHERE " . $colid ." IN(".$id.")" );
		echo "reload";
	}
}

elseif($ac=='type_bind')
{
	$val = intval($val);
	$bind = be("all","bind");
	
	if ($show ==1){
		echo '<select id="val" name="val"><option value="">取消绑定分类</option>' . makeSelectAll('{pre}'.$tab.'_type','t_id','t_name','t_pid','t_sort',0,'','&nbsp;|&nbsp;&nbsp;','').'</select><input class="input" type="button" value="绑定" onclick="bindsave(\''.$tab.'\',\''.$bind.'\');"><input class="input" type="button" value="取消" onclick="closew();">';
	}
	else{
		$bindcache = @include(MAC_ROOT."/inc/config/config.collect.bind.php");
		if (!is_array($bindcache)) {
			$bindcache = array();
			$bindcache['1_1'] = 0;
		}
		
		$bindinsert[$bind] = $val;
		$bindarray = array_merge($bindcache,$bindinsert);
		$cv = "<?php\nreturn ".var_export($bindarray, true).";\n?>";
		fwrite(fopen(MAC_ROOT."/inc/config/config.collect.bind.php",'wb'),$cv);
	    echo 'ok';
	}
}

elseif($ac=='memcached')
{
	$host = be("all","host");
	$port = be("all","port");
	try{
		$mem=new Memcache;
		if(!$mem->connect($host,$port)){
			echo '连接失败!';
		}
		else{
			echo 'ok';
		}
	}
	catch(Exception $e){ 
		echo 'err';
		exit;
	}
}

else{
	redirect( getReferer() );
}

?>