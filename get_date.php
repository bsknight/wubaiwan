<?php
$debug=0;
$debugnum='511343';
$res_array = array();

function curl_get_contents($url)
{
    global $curlError;
    $headerArr = array(
                    'Accept-Language: en'
                    );
    //$userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36';
    $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址
    //curl_setopt($ch,CURLOPT_HEADER,1);            //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 500);           //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);   //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER,$url);        //设置 referer
    //curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    //curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        //设置 referer
    //curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
    $r = curl_exec($ch);
    if(curl_errno($ch)){
        $curlError[] = curl_error($ch);
    }   
    curl_close($ch);
    return $r;
}

function req()
{
    $url = "http://trade.500.com/bjdc/";
    $contents = curl_get_contents($url);

    //$str = mb_convert_encoding($contents, 'UTF-8', 'GBK');
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    if (!$dom->loadHTML($contents))
    {
        $errors="";
        foreach (libxml_get_errors() as $error)  {
            $errors.=$error->message."<br/>"; 
        }
        libxml_clear_errors();
        print "libxml errors:<br>$errors";
        return;
    }
    $xpath = new DOMXPath($dom); 
    $result = $xpath->query("/html/body/div[@id='bd']/div[@class='b-top']/div[@class='b-top-inner']/div[@class='b-top-info']/div[@class='dc_q']/select[@id='expect_select']/option[1]");
    $num = 0;
    foreach ($result as $res) {
        $num = substr("{$res->firstChild->nodeValue}", 0, 6);
        print_r($num."\n");
        break;
    }
    
    /*
    $url = "http://app.video.baidu.com/session/?terminal=testcall1&danchang=$num";
    $contents = curl_get_contents($url);
    print_r($contents);
    */
    system("/home/webdev/php/bin/php /data/dan/monitor.php $num $num", $retval);
    var_dump($retval);
}

req();
