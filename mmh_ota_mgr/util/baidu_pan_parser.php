<?php
/*    function baidu_pan_parser($share_url){
        $url_curl = curl_init($share_url);
        curl_setopt($url_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url_curl, CURLOPT_BINARYTRANSFER, true);
        $result_get = curl_exec($url_curl);
        curl_close($url_curl);
        //preg_match_all('/\\\"dlink\\\":\\\"([^"]*)\\\"}\"/',$result_get,$match_tmp);
        preg_match_all('/\;\;_dlink=\"([^"]*)\"/',$result_get,$match_tmp);
        $url_tmp = stripslashes($match_tmp[1][0]);
        $url_tmp = stripslashes($url_tmp);
        return $url_tmp;        
    } */ 
    function baidu_pan_parser($share_url,$share_p){
        $header[] = "Connection: keep-alive"; 
        $header[] = "X-Requested-With: XMLHttpRequest";
        $header[] = "Accept: application/json, text/javascript, */*; q=0.01";
        $header[] = "Referer: ".$share_url;
        $header[] = "Accept-Encoding: gzip,deflate,sdch";
        $header[] = "Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3";
        $header[] = "Accept-Language: zh-CN,zh;q=0.8"; 
         
        global $t1,$t2,$t3,$t4,$t5;    
        $header[] = "Cookie: BAIDUID=4253B0A496DEF0F862D3C36AA416007D:FG=1; locale=zh; PANWEB=1; bdshare_firstime=1379062972902; Hm_lvt_b181fb73f90936ebd334d457c848c8b5=1379061925,1379473396,1380436640,1380436660; BDUSS=FJheG14dXV4cVZHd3NxSDFWN29OTXlhT2pySndGMTBjS1JnN1pQUjNDaXZXRzlTQVFBQUFBJCQAAAAAAAAAAAEAAABbV4w2AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAK~LR1Kvy0dSb; H_PS_PSSID=1464_3744_3778_3735_3595; Hm_lvt_773fea2ac036979ebb5fcc768d8beb67=".$t4.",".$t3.",".$t2.",".$t1."; Hm_lpvt_773fea2ac036979ebb5fcc768d8beb67=".$t5."; Hm_lvt_adf736c22cd6bcc36a1d27e5af30949e=".$t4.",".$t3.",".$t2.",".$t1."; Hm_lpvt_adf736c22cd6bcc36a1d27e5af30949e=".$t5."; recommendTime=iphone2013-11-14%2017%3A05; cflag=65535%3A1"; 	              
        $curl = curl_init();  
		curl_setopt($curl, CURLOPT_URL, $share_p);  
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION , true);	
        curl_setopt($curl, CURLOPT_AUTOREFERER , true);	
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   		
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$rtdata = curl_exec($curl);  
                  
        preg_match_all('/\"dlink\":\"([^"]*)\"/',$rtdata,$match_tmp);
        $url_tmp = stripslashes($match_tmp[1][0]);
        $url_tmp = stripslashes($url_tmp); 
        curl_close($curl); 
        return  $url_tmp;    
    }
    function baidu_pan_dl($share_url,$url_dl){
        $header[] = "Connection: keep-alive"; 
        $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Referer: ".$share_url;
        //$header[] = "Accept-Encoding: gzip,deflate,sdch";
        //$header[] = "Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3";
        //$header[] = "Accept-Language: zh-CN,zh;q=0.8"; 
        $header[] = "BAIDUID=4253B0A496DEF0F862D3C36AA416007D:FG=1; locale=zh; BDUSS=FJheG14dXV4cVZHd3NxSDFWN29OTXlhT2pySndGMTBjS1JnN1pQUjNDaXZXRzlTQVFBQUFBJCQAAAAAAAAAAAEAAABbV4w2AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAK~LR1Kvy0dSb; cflag=65535%3A1"; 	                       
        $curl = curl_init();  
		curl_setopt($curl, CURLOPT_URL, $url_dl);  
		curl_setopt($curl, CURLOPT_HEADER, 0);       
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);      
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION , true);	
        curl_setopt($curl, CURLOPT_AUTOREFERER , true);	
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   		
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_exec($curl);
        curl_close($curl);                      
    }    

    function baidu_pan_get_paser_url($url){
        $header[] = "Connection: keep-alive"; 
        $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        //$header[] = "Accept-Encoding: gzip,deflate,sdch";
        //$header[] = "Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3";
        //$header[] = "Accept-Language: zh-CN,zh;q=0.8"; 
        global $t1,$t2,$t3,$t4,$t5;    
        $header[] = "Cookie: BAIDUID=4253B0A496DEF0F862D3C36AA416007D:FG=1; locale=zh; PANWEB=1; bdshare_firstime=1379062972902; Hm_lvt_b181fb73f90936ebd334d457c848c8b5=1379061925,1379473396,1380436640,1380436660; BDUSS=FJheG14dXV4cVZHd3NxSDFWN29OTXlhT2pySndGMTBjS1JnN1pQUjNDaXZXRzlTQVFBQUFBJCQAAAAAAAAAAAEAAABbV4w2AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAK~LR1Kvy0dSb; H_PS_PSSID=1464_3744_3778_3735_3595; Hm_lvt_773fea2ac036979ebb5fcc768d8beb67=".$t4.",".$t3.",".$t2.",".$t1."; Hm_lpvt_773fea2ac036979ebb5fcc768d8beb67=".$t5."; Hm_lvt_adf736c22cd6bcc36a1d27e5af30949e=".$t4.",".$t3.",".$t2.",".$t1."; Hm_lpvt_adf736c22cd6bcc36a1d27e5af30949e=".$t5."; recommendTime=iphone2013-11-14%2017%3A05; cflag=65535%3A1";
        $curl = curl_init();  
		curl_setopt($curl, CURLOPT_URL, $url);  
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION , true);	
        curl_setopt($curl, CURLOPT_AUTOREFERER , true);	
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1");   		
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$con = curl_exec($curl);
                   
        preg_match_all('/sysUK=\"([^"]*)";/',$con,$uktmp);     
        preg_match_all('/share_id=\"([^"]*)";/',$con,$shareid_tmp);  
        preg_match_all('/fsId=\"([^"]*)";/',$con,$fsid_tmp);  

        $share_parse_url = 'http://pan.baidu.com/share/download?bdstoken=767e1edd06d2014e04316f22cba6204c&uk='.$uktmp[1][0].'&shareid='.$shareid_tmp[1][0].'&fid_list=%5B'.$fsid_tmp[1][0].'%5D';                              
        curl_close($curl); 
        return $share_parse_url;
    }    
?>