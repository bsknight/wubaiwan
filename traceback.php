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
    $right[$num] = model2($all, $res, $avg);
}

$win = 0;
$lost = 0;
foreach($right as $k=>$v)
{
    if($v === 1)
    {
        $win++;
    }
    elseif($v === 0)
    {
        $lost++;
    }
}
var_dump($win);
var_dump($lost);
function model2($all, $res, $avg)
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
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                $num++;
                                if(!($all[$name]['end']['win'] > $all[$name]['first']['win'] &&
                                    $all[$name]['end']['lost'] < $all[$name]['first']['lost'] &&
                                    $all[$name]['end']['win'] < 2.05 &&
                                    $all[$name]['first']['win'] < 2.01 
                                    )
                                )
                                {
                                    $result = 0;
                                    break;
                                    if($name == '威廉希尔' || $name=='澳门' || $name=='立博')
                                    {
                                        $result = 0;
                                        break;
                                    }
                                }

                            }
                        }
                        if($result == 0)
                        {
                            return 2;
                        }
                        if($res != 1)   // home win
                        {
                        /*
                        echo $all['num']." ";
                                foreach($my_array as $name)
                                {
                                    if(!isset($all[$name]))
                                        break;
                                    $first_win = $all[$name]['first']['win'];
                                    $first_draw = $all[$name]['first']['draw'];
                                    $first_lost = $all[$name]['first']['lost'];
                                    $end_win = $all[$name]['end']['win'];
                                    $end_draw = $all[$name]['end']['draw'];
                                    $end_lost = $all[$name]['end']['lost'];
                                    echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
                                }
                                echo "0\n";
                        */
                                return 0;
                        }
                        else
                        {
                        /*
                        echo $all['num']." ";
                                foreach($my_array as $name)
                                {
                                    if(!isset($all[$name]))
                                        break;
                                    $first_win = $all[$name]['first']['win'];
                                    $first_draw = $all[$name]['first']['draw'];
                                    $first_lost = $all[$name]['first']['lost'];
                                    $end_win = $all[$name]['end']['win'];
                                    $end_draw = $all[$name]['end']['draw'];
                                    $end_lost = $all[$name]['end']['lost'];
                                    echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
                                }
                                echo "1\n";
                        */
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
                        foreach($my_array as $name)
                        {
                            if(isset($all[$name]))
                            {
                                $num++;
                                if(!($all[$name]['end']['win'] < $all[$name]['first']['win'] &&
                                    $all[$name]['end']['lost'] > $all[$name]['first']['lost'] &&
                                    $all[$name]['end']['lost'] > 2.05 &&
                                    $all[$name]['first']['lost'] > 2.01 
                                    )
                                )
                                {
                                    $result = 0;
                                    break;
                                    if($name == '威廉希尔' || $name=='澳门' || $name=='立博')
                                    {
                                        $result = 0;
                                        break;
                                    }
                                }
                            }
                        }
                        if($result == 0)
                        {
                            return 2;
                        }
                        if($res == 0)// away win
                        {
                        /*
                        echo $all['num']." ";
                                foreach($my_array as $name)
                                {
                                    if(!isset($all[$name]))
                                        break;
                                    $first_win = $all[$name]['first']['win'];
                                    $first_draw = $all[$name]['first']['draw'];
                                    $first_lost = $all[$name]['first']['lost'];
                                    $end_win = $all[$name]['end']['win'];
                                    $end_draw = $all[$name]['end']['draw'];
                                    $end_lost = $all[$name]['end']['lost'];
                                    echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
                                }
                                echo "0\n";
                        */
                                return 0;
                        }
                        elseif($res == 1);
                        {
                        /*
                        echo $all['num']." ";
                                foreach($my_array as $name)
                                {
                                    if(!isset($all[$name]))
                                        break;
                                    $first_win = $all[$name]['first']['win'];
                                    $first_draw = $all[$name]['first']['draw'];
                                    $first_lost = $all[$name]['first']['lost'];
                                    $end_win = $all[$name]['end']['win'];
                                    $end_draw = $all[$name]['end']['draw'];
                                    $end_lost = $all[$name]['end']['lost'];
                                    echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
                                }
                                echo "1\n";
                        */
                                return 1;
                        }
                }
        }
        return 2;
}


/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
