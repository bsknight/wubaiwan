<?php
/***************************************************************************
 * 
 * Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
 * 
 **************************************************************************/
 
 
 
/**
 * @file php/gen_general_conf.php
 * @author zhaowuyuan(com@baidu.com)
 * @date 2014/07/11 13:34:32
 * @brief 
 *  
 **/
$file1=$argv[1];
$fdr = fopen($file1,"r");
$win = 0;
$lost = 0;
$mailcontent = array();
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
    $right[$num] = model2($all, $res, $avg, $mailcontent, $home, $away);
}
echo "win:";
var_dump($win);
echo " draw:";
var_dump($draw);
echo " lost:";
var_dump($lost);
$win = 0;
$lost = 0;
foreach($right as $k=>$v)
{
    if($v === 1)
    {
        echo "win ";
        var_dump($k);
        $win++;
    }
    elseif($v === 0)
    {
        echo "lost ";
        var_dump($k);
        $mailcontent['error'][] =
        "http://odds.500.com/fenxi/ouzhi-".str_replace('model2:','',$k).".shtml";
        $lost++;
    }
}
var_dump($mailcontent);
echo "win:";
var_dump($win);
echo " lost:";
var_dump($lost);
$ret = mail('xiesicong@baidu.com,241092598@qq.com', 'result', str_replace('\\', '',json_encode($mailcontent)));
function model2($all, $res, $avg, &$mailcontent, $home, $away)
{
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
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                if(!($all[$name]['end']['win'] > $all[$name]['first']['win'] &&
                                    //$all[$name]['end']['draw'] <= $all[$name]['first']['draw'] &&
                                    $all[$name]['end']['lost'] < $all[$name]['first']['lost'] 
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
                                            echo "error ";
                                            var_dump($all['num']);
                                            $mailcontent['interwetten filter'][] =
                                            "http://odds.500.com/fenxi/ouzhi-".str_replace('model2:','',$all['num']).".shtml";
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $num++;
                                if($num < 5)
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
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                if(!($all[$name]['end']['win'] < $all[$name]['first']['win'] &&
                                    //$all[$name]['end']['draw'] <= $all[$name]['first']['draw'] && 
                                    $all[$name]['end']['lost'] > $all[$name]['first']['lost'] 
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
                                            echo "error ";
                                            var_dump($all['num']);
                                            $mailcontent['interwetten filter'][] =
                                            "http://odds.500.com/fenxi/ouzhi-".str_replace('model2:','',$all['num']).".shtml";
                                        }
                                    }
                                }
                                
                            }
                            else
                            {
                                $num++;
                                if($num < 5)
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


/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
