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
    $res[0] = $items[$i];
    $res[1] = $items[$i+1];
    $rang = $items[$i+2];
    if($rang == 0)
        continue;
    $right[$num] = model3($all, $res, $avg, $rang, $mailcontent);
}
$win = 0;
$lost = 0;
foreach($right as $k=>$v)
{
    if($v === 1)
    {
        //echo "win ";
        //var_dump($k);
        $win++;
    }
    elseif($v === 0)
    {
        //echo "lost ";
        //var_dump($k);
        $mailcontent['error'][] =
        "http://odds.500.com/fenxi/ouzhi-".str_replace('model3:','',$k).".shtml";
        $lost++;
    }
}
var_dump($win);
var_dump($lost);
$ret = mail('xiesicong@baidu.com,241092598@qq.com', 'result', str_replace('\\', '',json_encode($mailcontent)));
function model3($all, $res, $avg, $rang, &$mailcontent)
{
        $my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet(博天堂)");
        $type = $avg['first']['win'] < $avg['first']['lost']? 'homelow':'awaylow';
        if($type == 'homelow')
        {
                if(
                    $avg['first']['win'] < 1.9 &&
                    $avg['end']['win'] < 1.98 &&
                    $avg['end']['win'] > $avg['first']['win']
                )
                {
                        $num = 0;
                        $result = 1;
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                if(!($all[$name]['end']['win'] > $all[$name]['first']['win'] &&
                                    $all[$name]['end']['draw'] <= $all[$name]['first']['draw'] &&
                                    $all[$name]['end']['lost'] < $all[$name]['first']['lost']
                                    )
                                )
                                {
                                    $num++;
                                    //if($name == '威廉希尔' || $name=='澳门' || $name=='立博')
                                    if($name == "Interwetten")
                                    {
                                        $result = 0;
                                        break;
                                    }
                                    if($num >= 1)
                                    {
                                        $result = 0;
                                        break;
                                    }
                                    /*
                                    if(!($all[$name]['end']['win'] >= $all[$name]['first']['win'] &&
                                        $all[$name]['end']['draw'] <= $all[$name]['first']['draw'] &&
                                        $all[$name]['end']['lost'] <= $all[$name]['first']['lost']))
                                        {
                                            if($name == '威廉希尔' || $name=='澳门' || $name=='立博')
                                            {
                                                $result = 0;
                                                break;
                                            }
                                        }
                                    }
                                    */
                                }
                                if($all['威廉希尔']['first']['draw'] > $avg['first']['draw'])
                                {
                                    $result = 0;
                                    break;
                                }

                            }
                        }
                        if($result == 0)
                        {
                            return 2;
                        }
                        if($unfinish)
                        {
                                echo "model3:".$param['num']."\n";
                                $res_array['model3'][] = $link.$param['num'];
                        }
                        elseif($res[0] > $res[1]+$rang)   // home lose
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
                if(
                    $avg['first']['lost'] < 1.9 &&
                    $avg['end']['lost'] < 1.98 &&
                    $avg['end']['lost'] > $avg['first']['lost']
                )
                {
                        $num = 0;
                        $result = 1;
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                $num++;
                                if(!($all[$name]['end']['win'] < $all[$name]['first']['win'] &&
                                    $all[$name]['end']['draw'] <= $all[$name]['first']['draw'] &&
                                    $all[$name]['end']['lost'] > $all[$name]['first']['lost']
                                    )
                                )
                                {
                                    $num++;
                                    if($name == "Interwetten")
                                    {
                                        $result = 0;
                                        break;
                                    }
                                    if($num >= 1)
                                    {
                                        $result = 0;
                                        break;
                                    }
                                    /*
                                    if(!($all[$name]['end']['win'] <= $all[$name]['first']['win'] &&
                                        $all[$name]['end']['draw'] <= $all[$name]['first']['draw'] &&
                                        $all[$name]['end']['lost'] >= $all[$name]['first']['lost']))
                                        {
                                            if($name == '威廉希尔' || $name=='澳门' || $name=='立博')
                                            {
                                                $result = 0;
                                                break;
                                            }
                                        }
                                    }
                                    */
                                }
                                if($all['威廉希尔']['first']['draw'] > $avg['first']['draw'])
                                {
                                    $result = 0;
                                    break;
                                }
                            }
                        }
                        if($result == 0)
                        {
                            return 2;
                        }
                        if($res[1] > $res[0]+$rang)
                        {
                                return 0;
                        }
                        else
                        {
                                return 1;
                        }
                }
        }
        return 2;        
        /*
        if($type == 'homelow')
        {
            if($res[0] > $res[1]+$rang)
            {
                return 0;
            }
            else
            {
                $num = $all['num'];
                echo $num." ";
                return 1;
            }
        }
        elseif($type == 'awaylow')
        {
            if($res[1] > $res[0]+$rang)
            {
                return 0;
            }
            else
            {
                $num = $all['num'];
                echo $num." ";
                return 1;
            }
        }
        */
}


/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
