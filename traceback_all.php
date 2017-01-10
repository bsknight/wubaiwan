<?php
date_default_timezone_set("Asia/Shanghai");
$file1=$argv[1];
$fdr = fopen($file1,"r");
$win = 0;
$lost = 0;
$mailcontent = array();

$filter_win = 0;
$filter_lost = 0;
$noyapan_win = 0;
$noyapan_lost = 0;
$total_profit = 0;

$static_array = array();
$empty_array = array();
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
    //$my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet(博天堂)","Pinnacle平博");
    //$not_use_array = array("澳门","利记","Unibet (优胜客)","Mansion88 (明升)","金宝博","香港马会","Eurobet","10BET","Gamebookers","皇冠","易胜博");
    #$not_use_array = array("利记","Unibet (优胜客)","Mansion88 (明升)","金宝博","Eurobet","10BET","Gamebookers","皇冠", "平博");
    #$not_use_array = array("Pinnacle平博");
    #$not_use_array = array("平博");
    $not_use_array = array();
    $my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)","Pinnacle平博","皇冠","易胜博","利记","Unibet (优胜客)","Mansion88 (明升)","金宝博","香港马会","Eurobet","10BET","Gamebookers", "平博");
    $win_max_comp = '';
    $draw_max_comp = '';
    $lost_max_comp = '';
    $win_max = 0;
    $draw_max = 0;
    $lost_max = 0;
    foreach($my_array as $comp)
    {
        if(in_array($comp, $not_use_array))
        {
            $i = $i + 6;
            continue;
        }
            
        if($items[$i] == "0.00")
        {
            $empty_array[$comp] += 1;
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

        $win_max_comp = $items[$i+3] > $win_max ? $comp : $win_max_comp;
        $win_max = $items[$i+3] > $win_max ? $items[$i+3] : $win_max;
        $draw_max_comp = $items[$i+4] > $draw_max ? $comp : $draw_max_comp;
        $draw_max = $items[$i+4] > $draw_max ? $items[$i+4] : $draw_max;
        $lost_max_comp = $items[$i+5] > $lost_max ? $comp : $lost_max_comp;
        $lost_max = $items[$i+5] > $lost_max ? $items[$i+5] : $lost_max;

        $i = $i + 6;
        $avg['first']['win'] += $all[$comp]['first']['win'];
        $avg['end']['win'] += $all[$comp]['end']['win'];
        $avg['first']['draw'] += $all[$comp]['first']['draw'];
        $avg['end']['draw'] += $all[$comp]['end']['draw'];
        $avg['first']['lost'] += $all[$comp]['first']['lost'];
        $avg['end']['lost'] += $all[$comp]['end']['lost'];
        $count++;

    }
    if($count == 0)
        continue;
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
    $dc_date = $items[$i+4];
    $danchangnum = $items[$i+5];
	$yapan['okooo_num'] = $items[$i+6];
	$yapan['home'] = $items[$i+7];
	$yapan['away'] = $items[$i+8];
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
    $right[$num]['res'] = model2($all, $res, $avg, $mailcontent, $home, $away, $num, $yapan);
    $right[$num]['time'] = $time;
    if($avg['end']['win'] == $avg['end']['lost'])
    {
        $type = 'end_home_away_equal';
    }
    elseif($avg['end']['win'] < $avg['end']['lost'])
    {
        $type = 'end_home_low';
    }
    elseif($avg['end']['lost'] < $avg['end']['win'])
    {
        $type = 'end_away_low';
    }
    /*
    $static_array[$res][$type]['win_max'][$win_max_comp] ++;
    $static_array[$res][$type]['draw_max'][$draw_max_comp] ++;
    $static_array[$res][$type]['lost_max'][$lost_max_comp] ++;
    */
    $type = $avg['first']['win'] < $avg['first']['lost']? 'homelow':'awaylow';
    if($type == "homelow")
    {
        $end_low_index = 'lost';
        $end_high_index = 'win';
    }
    else
    {
        $end_low_index = 'win';
        $end_high_index = 'lost';
    }
    if( ($avg['end'][$end_high_index] > 2.05 &&
        $avg['first'][$end_high_index] > 2.01 &&
        $avg['end'][$end_high_index] > $avg['first'][$end_high_index] &&
        $avg['end'][$end_high_index] >= $avg['end'][$end_low_index]
        && $avg['first'][$end_low_index] >= $avg['first'][$end_high_index])
    )
    {
        foreach($my_array as $comp)
        {
            if(in_array($comp, $not_use_array))
            {
                continue;
            }
            if(!isset($all[$comp]))
            {
                if($comp == "Pinnacle平博")
                {
                    if(isset($all["平博"]))
                        $comp = "平博";
                    else
                        continue;
                }
                elseif($comp == "平博")
                {
                    if(isset($all["Pinnacle平博"]))
                        $comp = "Pinnacle平博";
                    else
                        continue;
                }
                else
                {
                    continue;
                }
            }
            if($all[$comp]['end'][$end_high_index] > $all[$comp]['first'][$end_high_index] &&
                $all[$comp]['end'][$end_low_index] < $all[$comp]['first'][$end_low_index] 
                && $all[$comp]['end'][$end_low_index] <= $all[$comp]['end'][$end_high_index] 
                && $all[$comp]['end']['draw'] < $all[$comp]['first']['draw'] 
                )
            //if(    $all[$comp]['end']['draw'] < $all[$comp]['first']['draw'] )
            //if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德' || $name=="Pinnacle平博")
            {
                $static_array[$res]['right_trend'][$comp] ++;
            }
            else
            {
                /*
                var_dump($comp);
                var_dump($all[$comp]);
                var_dump($avg);
                var_dump($home);
                var_dump($away);
                var_dump($res);
                exit;
                */
                $static_array[$res]['wrong_trend'][$comp] ++;
            }
        }
        $total[$res] ++;
    }
}
function array_sort($array, $type='asc'){
    $result=array();
    foreach($array as $var => $val){
        $set=false;
        foreach($result as $var2 => $val2){
            if($set==false){
                if($val>$val2 && $type=='desc' || $val<$val2 && $type=='asc'){
                    $temp=array();
                    foreach($result as $var3 => $val3){
                        if($var3==$var2) $set=true;
                        if($set){
                            $temp[$var3]=$val3;
                            unset($result[$var3]);
                        }
                    }
                    $result[$var]=$val;    
                    foreach($temp as $var3 => $val3){
                        $result[$var3]=$val3;
                    }
                }
            }
        }
        if(!$set){
            $result[$var]=$val;
        }
    }
    return $result;
}
foreach($my_array as $comp)
{
    $static_array[2]['presion'][$comp] = ($static_array[1]['right_trend'][$comp]) / ($static_array[1]['right_trend'][$comp]+$static_array[0]['right_trend'][$comp]);
    $static_array[2]['presion'] = array_sort($static_array[2]['presion'], 'desc');
    $static_array[2]['recall'][$comp] = ($static_array[1]['right_trend'][$comp]) / ($static_array[1]['right_trend'][$comp] + $static_array[1]['wrong_trend'][$comp]);
    $static_array[2]['recall'] = array_sort($static_array[2]['recall'], 'desc');
    $static_array[2]['f-score'][$comp] = 2 * $static_array[2]['presion'][$comp] * $static_array[2]['recall'][$comp] /($static_array[2]['presion'][$comp] + $static_array[2]['recall'][$comp]);
    $static_array[2]['f-score'] = array_sort($static_array[2]['f-score'], 'desc');
    
    $static_array[3]['presion'][$comp] = ($static_array[0]['wrong_trend'][$comp]) / ($static_array[0]['wrong_trend'][$comp]+$static_array[1]['wrong_trend'][$comp]);
    $static_array[3]['presion'] = array_sort($static_array[3]['presion'], 'desc');
    $static_array[3]['recall'][$comp] = ($static_array[0]['wrong_trend'][$comp]) / ($static_array[0]['right_trend'][$comp] + $static_array[0]['wrong_trend'][$comp]);
    $static_array[3]['recall'] = array_sort($static_array[3]['recall'], 'desc');
    $static_array[3]['f-score'][$comp] = 2 * $static_array[3]['presion'][$comp] * $static_array[3]['recall'][$comp] /($static_array[3]['presion'][$comp] + $static_array[3]['recall'][$comp]);
    $static_array[3]['f-score'] = array_sort($static_array[3]['f-score'], 'desc');
}
var_dump($empty_array);
print_r($static_array);
var_dump($total);
echo "base rate:";
var_dump((float)$total[1]/(float)($total[0]+$total[1]));
echo "win:";
var_dump($win);
echo "lost:";
var_dump($lost);
echo "base rate:";
var_dump((float)$win/(float)($win+$lost));
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
echo "rate:";
var_dump((float)$win/(float)($win+$lost));
echo "avg profit:";
var_dump((float)$total_profit/(float)($win+$lost));
//$ret = mail('xiesicong@baidu.com,241092598@qq.com', 'result', str_replace('\\', '',json_encode($mailcontent)));
echo "filter:\n";
var_dump($filter_win);
var_dump($filter_lost);
echo "noyapan:\n";
var_dump($noyapan_win);
var_dump($noyapan_lost);

function get_yapan($num, $type)
{
        $url = "http://odds.500.com/fenxi1/inc/ajax.php?t=yazhi&p=1&r=1&fixtureid=$num&companyid=6&updatetime=2017-12-06";
        $tmp = curl_get_contents($url);
        $tmp = json_decode($tmp,true);
        if($type == 'homelow')
        {
            $index = 2;
        }
        else
        {
            $index = 0;
        }
        foreach($tmp as $data)
        {
            if($data[1] == "平手")
            {
                return $data[$index];
            }
        }
        return 0;
}
function get_yapan_bak($num, $type)
{
        $url = "http://m.500.com/info/index.php?c=detail&a=yazhiAjax&r=1&fid=".$num;
        #$url = "http://odds.500.com/fenxi1/inc/ajax.php?_=1480994860231&t=yazhi&p=1&r=1&fixtureid=$num&companyid=6&updatetime=2017-12-06";
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
function model2($all, $res, $avg, &$mailcontent, $home, $away, $game_num, $yapan)
{
        global $filter_win;
        global $filter_lost;
        global $noyapan_win;
        global $noyapan_lost;
        global $total_profit;
        //$key_comp = array("Coral","Pinnacle平博","SportingBet (博天堂)","易胜博","利记","Unibet (优胜客)","Mansion88 (明升)","香港马会");
        //$key_comp = array("澳门","立博","Bet365","Coral","SportingBet (博天堂)","Pinnacle平博","易胜博","利记","Unibet (优胜客)","Mansion88 (明升)","香港马会");
        $key_comp = array("威廉希尔","Bet365","Interwetten","Bwin","Coral","Pinnacle平博","易胜博","香港马会","Eurobet","Gamebookers");
        $key_comp = array("威廉希尔","Bet365","Bwin","Coral","易胜博","Eurobet","Gamebookers");
        $key_comp = array("威廉希尔","Bet365","Interwetten","伟德","Bwin","Coral","易胜博");
        $key_comp = array("易胜博","Coral","香港马会","Pinnacle平博");
        $key_comp = array("易胜博","Coral","香港马会");
        $key_comp = array("威廉希尔","香港马会","Bet365","Coral","易胜博","Eurobet");
        $key_comp = array("威廉希尔","香港马会","Coral","易胜博","Eurobet");
        $key_comp = array("香港马会","易胜博","威廉希尔");
        $key_comp = array("香港马会","易胜博","Eurobet");
        $key_comp = array("香港马会","易胜博","平博");
        $key_comp = array("香港马会","易胜博", "Interwetten");
        $key_comp = array("香港马会","Coral", "Interwetten", "易胜博", "威廉希尔");
        $key_comp = array("香港马会","易胜博","Coral","Interwetten");
        $key_comp = array("易胜博","Coral","香港马会");
        $key_comp = array("易胜博","Coral","香港马会");
        #$key_comp_sec = array("威廉希尔", "Interwetten", "Coral", "Eurobet");
        #$key_comp = array("SportingBet (博天堂)","Coral","Mansion88 (明升)");
        #$key_comp = array("香港马会","Eurobet","Gamebookers");
        #$key_comp = array("易胜博","香港马会","Coral");
        //$home_key_comp = array("易胜博","Coral","香港马会","Gamebookers","Eurobet","立博","伟德","威廉希尔","Bet365","皇冠");
        #$key_comp = array("易胜博","Bet365","Coral");
        #$key_comp = array("SportingBet (博天堂)","Mansion88 (明升)","Coral","香港马会");
        $my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)","易胜博","香港马会");
        #$my_array = array("Bwin","Coral","易胜博","香港马会","威廉希尔","Eurobet","Gamebookers");
        #$my_array = array("威廉希尔","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)","易胜博","香港马会","Eurobet","Gamebookers");
        #$my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)","易胜博","香港马会");
        #$my_array = array("威廉希尔","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)");
        #$my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)","Pinnacle平博","皇冠","易胜博","利记","Unibet (优胜客)","Mansion88 (明升)","金宝博","香港马会","Eurobet","10BET","Gamebookers");
        #$my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)","Pinnacle平博","皇冠","易胜博","香港马会","Eurobet","Gamebookers");
        $type = $avg['first']['win'] < $avg['first']['lost']? 'homelow':'awaylow';
        /*
        if($type == "homelow")
        {
            $end_low_index = 'lost';
            $end_high_index = 'win';
        }
        else
        {
            $end_low_index = 'win';
            $end_high_index = 'lost';
        }
        if(!(
                ($avg['end'][$end_high_index] > 2.05 &&
                $avg['first'][$end_high_index] > 2.01 &&
                $avg['end'][$end_high_index] > $avg['first'][$end_high_index] &&
                $avg['end'][$end_high_index] > $avg['end'][$end_low_index])
            )
           )
        {
            return 2;
        }
        $result = 0;
        foreach($my_array as $comp)
        {    
            if(!isset($all[$comp]))
            {
                var_dump($comp);
                return 2;
            }
            if($all[$comp]['end'][$end_high_index] > $all[$comp]['first'][$end_high_index] &&
                $all[$comp]['end'][$end_low_index] < $all[$comp]['first'][$end_low_index] 
                //&& $all[$comp]['end'][$end_low_index] < $all[$comp]['end'][$end_high_index] 
                && $all[$comp]['end']['draw'] < $all[$comp]['first']['draw'] 
                )
            {
                #if($comp == '威廉希尔' || $comp == 'Interwetten' || $comp=='伟德' || $comp=="Coral" || $comp=="Bwin" || $comp=="Bet365" || $comp=="Pinnacle平博")
                //if($comp == '威廉希尔' || $comp=="Bwin" || $comp=="Bet365")
                if($comp == '威廉希尔' || $comp == 'Interwetten' || $comp=='伟德' || $comp=="Pinnacle平博")
                {
                    $result = 1;
                    break;
                }
            }
        }
        if($result == 0)
        {
            return 2;
        }
        if($res != 1)   // home win
        {
            return 0;
        }
        else
        {
            return 1;
        }
        */
        if($type == 'homelow')
        {
                if($avg['end']['win'] > 2.05 &&
                    $avg['first']['win'] > 2.01 &&
                    $avg['end']['win'] > $avg['first']['win'] &&
                    $avg['end']['win'] >= $avg['end']['lost']
                    && $avg['first']['win'] <= $avg['first']['lost']
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
                                    && $all[$name]['end']['lost'] <= $all[$name]['end']['win'] 
                                    //&& $all[$name]['first']['lost'] >= $all[$name]['first']['win'] 
                                    )
                                )
                                {
                                    if(in_array($name, $key_comp))
                                    {
                                        $num++;
                                        if($num >= 1)
                                        {
                                            $result = 0;
                                            break;
                                        }
                                    }
                                }
                                if(!($all[$name]['end']['draw'] < $all[$name]['first']['draw']))
                                {
                                    //if($name == '威廉希尔' || $name=="易胜博" || $name == 'Gamebookers' || $name=="Eurobet" || $name=="Bet365")
                                    //if($name == '威廉希尔' || $name=="易胜博" || $name == 'Gamebookers' || $name=="Eurobet" || $name=="Bet365" || $name == 'Interwetten')
                                    if(in_array($name, $key_comp))
                                    {
                                        $result = 0;
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                if($name == "香港马会")
                                //if(in_array($name, $key_comp))
                                {
                                    $num1++;
                                    if($num1 >= 1)
                                    {
                                        $bad =1;
                                        break;
                                    }
                                }
                                /*
                                $num1++;
                                if($num1 >= 3)
                                {
                                    $bad =1;
                                    break;
                                }
                                */
                            }
                        }
                        if($result == 0 || $bad == 1)
                        {
                            return 2;
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
                    $avg['end']['win'] <= $avg['end']['lost']
                    && $avg['first']['win'] >= $avg['first']['lost']
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
                                    && $all[$name]['end']['lost'] >= $all[$name]['end']['win'] 
                                    //&& $all[$name]['first']['lost'] <= $all[$name]['first']['win'] 
                                    )
                                )
                                {
                                    if(in_array($name, $key_comp))
                                    {
                                        $num++;
                                        if($num >= 1)
                                        {
                                            $result = 0;
                                            break;
                                        }
                                    }
                                }
                                if(!($all[$name]['end']['draw'] < $all[$name]['first']['draw']))
                                {
                                    //if($name == '威廉希尔' || $name=="易胜博" || $name == 'Gamebookers' || $name=="Eurobet" || $name=="Bet365")
                                    //if($name == '威廉希尔' || $name=="易胜博" || $name == 'Gamebookers' || $name=="Eurobet" || $name=="Bet365" || $name == 'Interwetten')
                                    if(in_array($name, $key_comp))
                                    {
                                        $result = 0;
                                        break;
                                    }
                                }
                                /*
                                if(!($all[$name]['end']['draw'] < $all[$name]['first']['draw']))
                                {
									if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德' || $name=="Pinnacle平博")
									{
                                        $num++;
                                        if($num >= 1)
                                        {
                                            $result = 0;
                                            break;
                                        }
                                    }
                                }
                                */
                            }
                            else
                            {
                                if($name == "香港马会")
                                //if(in_array($name, $key_comp))
                                {
                                    $num1++;
                                    if($num1 >= 1)
                                    {
                                        $bad =1;
                                        break;
                                    }
                                }
                                /*
                                $num1++;
                                if($num1 >= 3)
                                {
                                    $bad =1;
                                    break;
                                }
                                */
                            }
                        }
                        if($result == 0 || $bad == 1)
                        {
                            return 2;
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
                        "Content-Type: text/xml; charset=utf-8",
                        "Accept:text/html",
                        "Host:odds.500.com"
                        );
        #$userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4';
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址
        //curl_setopt($ch,CURLOPT_HEADER,1);            //是否显示头部信息
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);           //设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);   //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER, $url);        //设置 referer
        //curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        //curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        //设置 referer
        curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      //跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $r = curl_exec($ch);
        if (curl_errno($ch)) { 
            print "Error: " . curl_error($ch); 
        } else { 
            curl_close($ch); 
        } 
        return $r;
}
/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
