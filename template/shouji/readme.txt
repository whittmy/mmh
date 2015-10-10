
==============================================================================================================
截图中添加的测试数据： 

flv$http://movie.ks.js.cn/flv/other/2014/06/20-2.flv
迅雷云播$thunder://QUFmdHA6Ly80NDoxMjM0QGY0LmdibC5hNjcuY29tOjY4MjEv55S15b2xLzIwMTUvMDEvMjMvYTY35omL5py655S15b2xYTY3LmNvbemcjeavlOeJueS6ujPvvJrkupTlhpvkuYvmiJhfYmToi7Hor63kuK3lrZdbaGQ0ODBwXS5tcDRaWg==

==============================================================================================================

app全能解码播放器说明：
彻底解决高清视频在手机上播放需要事先转换的繁琐。主流高清视频想看就看！
功能特点：
- 支持 HTTP, MMS, RTSP, RTMP, HLS(m3u8) 等常见的多种视频流媒体协议，包括点播与直播
- 支持thunder、ed2k、magnet、bt等专用链的视频
- 能够流畅播放720P甚至1080P高清MKV，FLV，MP4，MOV，TS，RMVB等常见格式的视频
- 瞬间扫描手机中的视频文件 清晰列表方便管理

==============================================================================================================

app全能解码播放器调用说明：
引入检测app是否安装js脚本，http://js2.bbscos.com/checkapp1.js  居中方格展示 ，http://js2.bbscos.com/checkapp2.js  底部长条展示 ，未安装app提示安装，以安装直接调用app播放器。

苹果CMS标签调用例子：

<script language="javascript" type="text/javascript" src="http://js2.bbscos.com/checkapp1.js"></script>
<dl class="tab2">
{maccms:play}
<dd class="[play:from]">
	<ul class="ulNumList clearfix list_1"> 
	{maccms:url order=asc}
	<li> <a onclick="app_check('[url:name]','[url:path]');return false;" href="javascript:void(0);" {if-A:[url:num]=[play:urlcount]}class="cur"{endif-A}  target="_self" title="[url:name]" alt="[url:name]">[url:name]{if-A:[url:num]=1}<i class='iNewIcon'></i>{endif-A} </a></li>
	{/maccms:url}
	</ul>
</dd>{/maccms:play}
</dl>

单独调用例子：
<script language="javascript" type="text/javascript" src="http://js2.bbscos.com/checkapp1.js"></script>
<script>app_check('视频名称','播放地址');</script>

==============================================================================================================

