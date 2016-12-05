<?php
date_default_timezone_set("Asia/Shanghai");
$file1=$argv[1];
$fdr = fopen($file1,"r");
$win = 0;
$lost = 0;
$mailcontent = array();
$filter_win = 0;
$filter_lost = 0;
while(!feof($fdr))
{
    $line = fgets($fdr);
    $line = trim($line,"\n");
    $items = explode(" ",$line);
    $num = $items[0];
    $all = array();
    $i = 1;
    $avg = array();
    $count = 0;
    $my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet(博天堂)");
    foreach($my_array as $comp)
    {
        if($items[$i] == 0)
        {
            //echo $num." ".$comp."\n";
            $i = $i + 6;
            continue;
        }
        $all['num'] = $num;
        $all[$comp]['first']['win'] = $items[$i];
        $all[$comp]['first']['draw'] = $items[$i+1];
        $all[$comp]['first']['lost'] = $items[$i+2];
        $all[$comp]['end']['win'] = $items[$i+3];
        $all[$comp]['end']['draw'] = $items[$i+4];
        $all[$comp]['end']['lost'] = $items[$i+5];
        $i = $i + 6;
        $avg['first']['win'] += $all[$comp]['first']['win'];
        $avg['end']['win'] += $all[$comp]['end']['win'];
        $avg['first']['draw'] += $all[$comp]['first']['draw'];
        $avg['end']['draw'] += $all[$comp]['end']['draw'];
        $avg['first']['lost'] += $all[$comp]['first']['lost'];
        $avg['end']['lost'] += $all[$comp]['end']['lost'];
        $count++;

    }
    $avg['first']['win'] /= $count;
    $avg['end']['win'] /= $count;
    $avg['first']['draw'] /= $count;
    $avg['end']['draw'] /= $count;
    $avg['first']['lost'] /= $count;
    $avg['end']['lost'] /= $count;
    $res = $items[$i];
	$home = $items[$i+1];
	$away = $items[$i+2];
    $time = $items[$i+3];
	if($home == $away)
	{
		//echo "draw $home $away \n";
		$draw++;
	}
    if($res == 1)
    {
        $win++;
    }
    elseif($res == 0)
    {
        $lost++;
    }
    /*
    $match_num = explode(":", $all['num']);
	$url = "http://m.500.com/info/live/?c=detail&fid=".$match_num[1];
	var_dump($url);
    $contents = curl_get_contents($url);
    $str = mb_convert_encoding($contents, 'UTF-8', 'GBK');
	$tmp = $str;
    var_dump($tmp);
    #preg_match_all('/<a.+href\="(http[^\"]+fenxi\/ouzhi[^\"]+)"/', $tmp, $out, PREG_PATTERN_ORDER);
    #preg_match_all('/jl-sfont">(.+\s..\:..)<\/span>/', $tmp, $out, PREG_PATTERN_ORDER);
    preg_match_all('/jl-sfont">(.+)<\/span>/', $tmp, $out, PREG_PATTERN_ORDER);
    var_dump($out);
    */
    $right[$num]['res'] = model2($all, $res, $avg, $mailcontent, $home, $away, $num);
    $right[$num]['time'] = $time;
}
echo "win:";
var_dump($win);
echo "draw:";
var_dump($draw);
echo "lost:";
var_dump($lost);
$win = 0;
$lost = 0;
uasort($right, 'cmp');
foreach($right as $k=>$v)
{
    if($v['res'] === 1)
    {
        echo "win ";
        echo $k." ".date('Y-m-j h:m:s', $v['time'])."\n";
        $win++;
    }
    elseif($v['res'] === 0)
    {
        echo "lost ";
        echo $k." ".date('Y-m-j h:m:s', $v['time'])."\n";
        $mailcontent['error'][] =
        "http://odds.500.com/fenxi/ouzhi-".str_replace('model2:','',$k).".shtml";
        $lost++;
    }
}
//var_dump($mailcontent);
echo "win:";
var_dump($win);
echo "lost:";
var_dump($lost);
//$ret = mail('xiesicong@baidu.com,241092598@qq.com', 'result', str_replace('\\', '',json_encode($mailcontent)));
var_dump($filter_win);
var_dump($filter_lost);
function get_yapan($num, $type)
{
        $url = "http://m.500.com/info/index.php?c=detail&a=yazhiAjax&r=1&fid=".$num;
        $tmp = curl_get_contents($url);
        $tmp = json_decode($tmp,true);
        $rang = 0;
        /*
        if($type == "homelow")
        {
                $pankou = array("半球/一球","一球","一球/球半","球半","球半/两球","两球");
                $rangqiu = array("半球/一球"=>0.75,"一球"=>1,"一球/球半"=>1.25,"球半"=>1.5,"球半/两球"=>1.75,"两球"=>2);
        }
        elseif($type == "awaylow")
        {
                $pankou = array("受半球/一球","受一球","受一球/球半","受球半","受球半/两球","受两球");
                $rangqiu = array("受半球/一球"=>0.75,"受一球"=>1,"受一球/球半"=>1.25,"受球半"=>1.5,"受球半/两球"=>1.75,"受两球"=>2);
        }
        */
        //var_dump($url);
        if($type == 'homelow')
        {
            $index = 'away';
        }
        else
        {
            $index = 'home';
        }
        //var_dump($index);
        foreach($tmp['list'] as $m)
        {
            if($m['name'] == '伟德')
            {
                //var_dump($m['last']['handi']);
                if($m['last']['handi'] == "平手")
                {
                    //$rang = $rangqiu[$m['last']['handi']];
                    return $m['last'][$index];
                }
                //return $rang;
            }
        }
        return 0;
}
function model2($all, $res, $avg, &$mailcontent, $home, $away, $game_num)
{
        global $filter_win;
        global $filter_lost;
        $my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet(博天堂)");
        $type = $avg['first']['win'] < $avg['first']['lost']? 'homelow':'awaylow';
        if($type == 'homelow')
        {
                if($avg['end']['win'] > 2.05 &&
                    $avg['first']['win'] > 2.01 &&
                    $avg['end']['win'] > $avg['first']['win'] &&
                    $avg['end']['win'] > $avg['end']['lost']
                )
                {
                        $num = 0;
                        $result = 1;
                        $bad = 0;
                        $num1 = 0;
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                if(!($all[$name]['end']['win'] > $all[$name]['first']['win'] &&
                                    //$all[$name]['end']['draw'] <= $all[$name]['first']['draw'] &&
                                    $all[$name]['end']['lost'] < $all[$name]['first']['lost']  
                                    //&& $all[$name]['end']['lost'] < $all[$name]['end']['win'] 
                                    //&& $all[$name]['first']['lost'] > $all[$name]['first']['win'] 
                                    )
                                )
                                {
                                    //$result = 0;
                                    //break;
                                    //if($name == '威廉希尔' || $name=='澳门' || $name=='立博' || $name == 'Interwetten')
                                    //if($name == '威廉希尔' || $name == 'Interwetten' || $name=='澳门')
                                    if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
                                    {
                                        $result = 0;
                                        break;
                                    }
                                }
                                /*
                                if($all['威廉希尔']['end']['draw'] > $all['威廉希尔']['first']['draw'])
                                {
                                        $result = 0;
                                        break;
                                }
                                */
                                if(!($all[$name]['end']['draw'] < $all[$name]['first']['draw']))
                                {
                                    //if($name == 'Interwetten' || $name == 'Bet365' || $name == '威廉希尔')
                                    //if($name == 'Interwetten' || )
									if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
									//if($name == 'Interwetten' || $name=='伟德')
                                    {	
                                        $num++;
                                        if($num >= 2)
                                        {
                                            $result = 0;
                                            break;
                                        }
                                        
                                        if($res == 1)
                                        {
                                            //echo "error ";
                                            //var_dump($all['num']);
                                            //$mailcontent['bad'][] =
                                            //"http://odds.500.com/fenxi/ouzhi-".str_replace('model2:','',$all['num']).".shtml";
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $num1++;
                                if($num1 >= 1)
                                {
                                    $bad =1;
                                    break;
                                }
                            }
                        }

                        if($result == 0 || $bad == 1)
                        {
                            return 2;
                        }
                        $yapei = (float)get_yapan($game_num, $type);
                        if($yapei != 0)
                        {
                            $oupei = (float)$all['伟德']['end']['draw'];
                            $profit = $yapei/( ($oupei-1)+1+($yapei) ) * ($oupei-1);
                            /*
                            var_dump($game_num);
                            var_dump($yapei);
                            var_dump($oupei);
                            var_dump($profit);
                            */
                            if($profit < 0.408)
                            {
                                if($res != 1)   // home win
                                {
                                    $filter_lost++;       
                                }
                                else
                                {
                                    $filter_win++;
                                }
                                return 2;
                            }
                        }
                        else
                        {
                            //return 2;
                        }
						if($home == $away)
						{
							//return 2;
						}
                        if($res != 1)   // home win
                        {
                                return 0;
                        }
                        else
                        {
                                return 1;
                        }
                }
        }
        elseif($type == 'awaylow')
        {
                if($avg['end']['lost'] > 2.05 &&
                    $avg['first']['lost'] > 2.01 &&
                    $avg['end']['lost'] > $avg['first']['lost'] &&
                    $avg['end']['win'] < $avg['end']['lost']
                )
                {
                        $num = 0;
                        $result = 1;
                        $bad = 0;
                        $num1 = 0;
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                if(!($all[$name]['end']['win'] < $all[$name]['first']['win'] &&
                                    //$all[$name]['end']['draw'] <= $all[$name]['first']['draw'] && 
                                    $all[$name]['end']['lost'] > $all[$name]['first']['lost'] 
                                    //&& $all[$name]['end']['lost'] > $all[$name]['end']['win'] 
                                    //&& $all[$name]['first']['lost'] < $all[$name]['first']['win'] 
                                    )
                                )
                                {
                                    //$result = 0;
                                    //break;
                                    //if($name == '威廉希尔' || $name=='澳门' || $name=='立博' || $name == 'Interwetten')
                                    //if($name == '威廉希尔' || $name == 'Interwetten' || $name=='澳门')
                                    if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
                                    {
                                        $result = 0;
                                        break;
                                    }
                                }
                                /*
                                if($all['威廉希尔']['end']['draw'] > $all['威廉希尔']['first']['draw'])
                                {
                                        $result = 0;
                                        break;
                                }
                                */
                                if(!($all[$name]['end']['draw'] < $all[$name]['first']['draw']))
                                {
                                    //if($name == 'Interwetten' || $name == 'Bet365' || $name == '威廉希尔')
                                    //if($name == 'Interwetten')
                                    if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
									//if($name == 'Interwetten' || $name=='伟德')
									{
                                        $num++;
                                        if($num >= 2)
                                        {
                                            $result = 0;
                                            break;
                                        }
                                        if($res == 1)
                                        {
                                            //echo "error ";
                                            //var_dump($all['num']);
                                            //$mailcontent['interwetten filter'][] =
                                            //"http://odds.500.com/fenxi/ouzhi-".str_replace('model2:','',$all['num']).".shtml";
                                        }
                                    }
                                }
                                
                            }
                            else
                            {
                                $num1++;
                                if($num1 >= 1)
                                {
                                    $bad =1;
                                    break;
                                }
                            }
                        }
                        if($result == 0 || $bad == 1)
                        {
                            return 2;
                        }

                        $yapei = (float)get_yapan($game_num, $type);
                        if($yapei != 0)
                        {
                            $oupei = (float)$all['伟德']['end']['draw'];
                            $profit = $yapei/( ($oupei-1)+1+($yapei) ) * ($oupei-1);
                            /*
                            var_dump($game_num);
                            var_dump($yapei);
                            var_dump($oupei);
                            var_dump($profit);
                            */
                            if($profit < 0.408)
                            {
                                if($res == 0)   // away win
                                {
                                    $filter_lost++;       
                                }
                                else
                                {
                                    $filter_win++;
                                }
                                return 2;
                            }
                        }
                        else
                        {
                            //return 2;
                        }

						if($home == $away)
						{
							//return 2;
						}
                        if($res == 0)// away win
                        {
                                return 0;
                        }
                        elseif($res == 1);
                        {
                                return 1;
                        }
                }
        }
        return 2;
}

function cmp($a, $b) {
    if ($a['time'] == $b['time']) {
        return 0;
    }
    return ($a['time'] < $b['time']) ? -1 : 1;
}

function curl_get_contents($url)
{
        $headerArr = array(
                        'Accept-Language: en'
                        );
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36';
        $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址
        //curl_setopt($ch,CURLOPT_HEADER,1);            //是否显示头部信息
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);           //设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);   //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER, $url);        //设置 referer
        //curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        //curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        //设置 referer
        //curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      //跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
}
/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
