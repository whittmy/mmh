<?php
//分页类
class Pager{
          var $url;
          var $countall;
          var $page;
          var $thestr;
          var $backstr;
          var $nextstr;
          var $pg;
          //构造函数，实例化该类的时候自动执行该函数
          function Pager($countall,$countlist){
                  @$this->pg=sprintf("%d",$_GET["pg"]);
                  //保证pg在未指定的情况下为从第1页开始
                  if ($this->pg==0){
                          $this->pg=1;
                  }
                  if (!isset($this->pg)){
                          $this->pg=1;
                  }
                  //记录数与每页显示数不能整队时，页数取余后加1
                  $this->countall = $countall;
                  if ($this->countall%$countlist!=0){
                          $this->page=sprintf("%d",$this->countall/$countlist)+1;
                  }
                  else{
                          $this->page=$this->countall/$countlist;
                  }
                
                  //得到当前的URL。具体实现请看最底部的函数实体
                  $this->url = Pager::getUrl();
                
                  //生成12345等数字形式的分页。
                  if ($this->page<=10){
                          for ($i=1;$i<$this->page+1;$i++){
                                  $this->thestr=$this->thestr.Pager::makepg($i,$this->pg);
                          }
                  }
                  else{
                          if ($this->pg<=5){
                                  for ($i=1;$i<10;$i++){
                                          $this->thestr=$this->thestr.Pager::makepg($i,$this->pg);
                                  }
                          }
                          else{
                                  if (6+$this->pg<=$this->page){
                                          for ($i=$this->pg-4;$i<$this->pg+6;$i++){
                                                  $this->thestr=$this->thestr.Pager::makepg($i,$this->pg);                        
                                          }
                                  }
                                  else{
                                          for ($i=$this->pg-4;$i<$this->page+1;$i++){
                                                  $this->thestr=$this->thestr.Pager::makepg($i,$this->pg);
                                          }
                                        
                                  }
                          }
                  }
                  //生成上页下页等文字链接
                  $this->backstr = Pager::gotoback($this->pg);
                  $this->nextstr = Pager::gotonext($this->pg,$this->page);
                  echo ($this->backstr.$this->thestr.$this->nextstr." 共".$this->countall." 条,每页".$countlist."条，分".$this->page."页");
          }
          //生成数字分页的辅助函数
          function makepg($i,$pg){
                  if ($i==$pg){
                          return " <font color=red><b>".$i."</b></font>";
                  }
                  else{
                          return " <a href=".Pager::replacepg($this->url,5,$i).">"."[".$i."]"."</a>";
                  }
          }
          //生成上一页等信息的函数
          function gotoback($pg){
                  if ($pg-1>0){
                                  return $this->gotoback=" <a href=".Pager::replacepg($this->url,3,0).">首页</a> <a href=".Pager::replacepg($this->url,2,0).">上页</a>";
                  }
                  else{
                                  return          $this->gotoback="";
                  }

          }
          //生成下一页等信息的函数
          function gotonext($pg,$page){
                  if ($pg < $page){
                                  return "&nbsp<a href=".Pager::replacepg($this->url,1,0).">下页</a>&nbsp<a href=".Pager::replacepg($this->url,4,0).">尾页</a>";
                  }
                  else{
                                  return "";
                  }
          }
        
          //处理url中$pg的方法,用于自动生成pg=x
          function replacepg($url,$flag,$i){
                  if ($flag == 1){ 
                          $temp_pg = $this->pg;
                          return str_replace("pg=".$temp_pg,"pg=".($this->pg+1),$url);
                  }
                  else if($flag == 2) {
                          $temp_pg = $this->pg;
                          return str_replace("pg=".$temp_pg,"pg=".($this->pg-1),$url);
                  }
                  else if($flag == 3) {
                          $temp_pg = $this->pg;
                          return str_replace("pg=".$temp_pg,"pg=1",$url);
                  }
                  else if($flag == 4){
                          $temp_pg = $this->pg;
                          return str_replace("pg=".$temp_pg,"pg=".$this->page,$url);
                  }
                  else if($flag == 5){
                          $temp_pg = $this->pg;
                          return str_replace("pg=".$temp_pg,"pg=".$i,$url);
                  }
                  else{
                          return $url;
                  }
          }
        
        
          //获得当前URL的方法
          function    getUrl(){  
                  $url="http://".$_SERVER["HTTP_HOST"];  
                
                  if(isset($_SERVER["REQUEST_URI"])){  
                          $url.=$_SERVER["REQUEST_URI"];  
                  }  
                  else{  
                          $url.=$_SERVER["PHP_SELF"];  
                          if(!empty($_SERVER["QUERY_STRING"])){  
                                  $url.="?".$_SERVER["QUERY_STRING"];  
                          }  
                  }  
                  //在当前的URL里加入pg=x字样
                  if (!ereg("(pg=|PG=|pG=|Pg=)", $url)){
                          if (!strpos($url,"?")){
                                  $url = $url."?pg=1";
                          }
                          else{
                                  $url = $url."&pg=1";
                          }
                  }                
                  return    $url;  
          }  
}
?>