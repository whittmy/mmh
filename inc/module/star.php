<?php
if(!defined('MAC_ROOT')){
	exit('Access Denied');
}

if($method=='list')
{
	$tpl->P["siteaid"] = 50;
	$tpl->P['cp'] = 'starlist';
	$tpl->P['cn'] = $tpl->P['pg'].'-'.$tpl->P['order'].'-'.$tpl->P['by'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->H = loadFile(MAC_ROOT."/template/".$MAC['site']['templatedir']."/".$MAC['site']['htmldir']."/star_list.html");
	$tpl->mark();
	$linkbytime = $tpl->getLink('star','starlist',$aa,array('pg'=>1,'by'=>'time'));
	$linkbyhits = $tpl->getLink('star','starlist',$aa,array('pg'=>1,'by'=>'hits'));
	$tpl->H = str_replace(array('{page:linkbytime}','{page:linkbyhits}'),array($linkbytime,$linkbyhits),$tpl->H);
	$tpl->pageshow();
}

elseif($method=='detail')
{
	$tpl->P["siteaid"] = 51;
	$tpl->P['cp'] = 'star';
	$tpl->P['cn'] = $tpl->P['id'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$sql = "SELECT * FROM {pre}star WHERE s_hide=0 AND s_id=" . $tpl->P['id'];
	$row = $db->getRow($sql);
	if (!$row){ showMsg ("获取数据失败，请勿非法传递参数", "../"); }
	$tpl->D = $row;
	$tpl->loadstar();
	unset($row);
}

else
{
	showErr('System','未找到指定系统模块');
}
?>