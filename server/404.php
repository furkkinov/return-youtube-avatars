<?php
http_response_code(200);
function sendResponse($msg, $code, $success = false) {
    header("Content-type: application/json");
    $data = array(
        "info" => $msg,
        "code" => $code,
        "success" => $success
    );
    exit(json_encode($data));
}
$_SERVER['REQUEST_URI'] = str_replace("/https://yt3.ggpht.com/", "https://yt3.ggpht.com/", $_SERVER['REQUEST_URI']);
if(filter_var($_SERVER['REQUEST_URI'], FILTER_VALIDATE_URL)) {
  $_SERVER["REQUEST_URI"] = parse_url($_SERVER['REQUEST_URI'])["path"];
  header("Location: ".$_SERVER["REQUEST_URI"]);
  exit;
}
$url = "https://yt3.ggpht.com".$_SERVER['REQUEST_URI'];
/*
Использовать https://yt4.ghpht.com/ как прокси сервер
*/
$yt4 = false;
if($yt4) {
    $yt4url = "https://yt4.ggpht.com".$_SERVER['REQUEST_URI'];
    header("Location: $yt4url");
    exit;
} else {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $headers = [];
    function startsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        return substr( $haystack, 0, $length ) === $needle;
    }
    // тут я спиздил код на обработку полученных хедеров
    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
      function($curl, $header) use (&$headers)
      {
        $len = strlen($header);
        $header = explode(':', $header, 2);
        if (count($header) < 2) 
          return $len;
    
        $headers[strtolower(trim($header[0]))][] = trim($header[1]);
        
        return $len;
      }
    );
    
    $data = curl_exec($ch);
    if(!isset($headers["content-type"])) sendResponse("Invalid response from yt3.ggpht.com", 500, false);
    if(!startsWith($headers["content-type"][0], "image/")) sendResponse("Invalid response from yt3.ggpht.com", 500, false);
    header("Content-type: ".$headers["content-type"][0]);
    echo $data;
    curl_close($ch);
    exit;
}
?>