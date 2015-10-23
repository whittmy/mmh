<?php
function remote_filesize_curl($url,$user='',$pw='')
{
// start output buffering
ob_start();
// initialize curl with given uri
$ch = curl_init($url);
// make sure we get the header
curl_setopt($ch,CURLOPT_HEADER, 1);
// make it a http HEAD request
curl_setopt($ch,CURLOPT_NOBODY, 1);
// if auth is needed, do it here
if (!empty($user) && !empty($pw)){
    $headers = array('Authorization: Basic '. base64_encode($user.':'.$pw));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
}
$okay = curl_exec($ch);
curl_close($ch);
// get the output buffer
$head = ob_get_contents();
// clean the output buffer and return to previous
// buffer settings
ob_end_clean();
//echo 'head-->'.$head.'<----end';
// gets you the numeric value from the Content-Length
// field in the http header
$regex = '/Content-Length:\s([0-9].+?)\s/';
$count = preg_match($regex, $head, $matches);

// if there was a Content-Length field, its value
// will now be in $matches[1]
if (isset($matches[1])){
    $size = $matches[1];
}
else{
    $size = 'unknown';
}
//$last=round($size/(1024*1024),3);
//return $last.' MB';
return $size;
}
?>