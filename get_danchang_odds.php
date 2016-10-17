<?php
$debug=0;
$debugnum='511343';
$res_array = array();
$link="http://m.500.com/info/index.php?c=detail&fid=";
function model2($param, $odd, $unfinish)
{
		global $link;
		global $res_array;
		global $debug;
		global $debugnum;
		$score = explode(":",$param['score']);
		$score[0] = intval($score[0]);
		$score[1] = intval($score[1]);
		$time = strtotime($param['year']."-".$param['time']);
		$my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)");
		$all = array();
		$avg['first'] = array();
		$avg['end'] = array();
		$jingcai = array();
		$count=0;
		foreach($odd as $m)
		{
				if(in_array($m['name'], $my_array))
				{
						$all[$m['name']] = $m;
						$avg['first']['win'] += $m['first']['win'];
						$avg['end']['win'] += $m['end']['win'];
						$avg['first']['draw'] += $m['first']['draw'];
						$avg['end']['draw'] += $m['end']['draw'];
						$avg['first']['lost'] += $m['first']['lost'];
						$avg['end']['lost'] += $m['end']['lost'];
						$count++;
				}
				elseif($m['name'] == '竞彩官方')
				{
						$jingcai['first']['win'] = $m['first']['win'];
						$jingcai['end']['win'] = $m['end']['win'];
						$jingcai['first']['draw'] = $m['first']['draw'];
						$jingcai['end']['draw'] = $m['end']['draw'];
						$jingcai['first']['lost'] = $m['first']['lost'];
						$jingcai['end']['lost'] = $m['end']['lost'];
				}
		}

		@$avg['first']['win'] /= $count;
		@$avg['end']['win'] /= $count;
		@$avg['first']['draw'] /= $count;
		@$avg['end']['draw'] /= $count;
		@$avg['first']['lost'] /= $count;
		@$avg['end']['lost'] /= $count;
		//var_dump($avg);
		$type = $avg['first']['win'] < $avg['first']['lost']? 'homelow':'awaylow';

		if($debug==1 && $param['num'] == $debugnum)
		{
				var_dump($count);
				var_dump($avg);
				var_dump($type);
		}

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
								$num++;
								if(!($all[$name]['end']['win'] > $all[$name]['first']['win'] &&
									$all[$name]['end']['lost'] < $all[$name]['first']['lost'])
								)
								{
									//$result = 0;
									//break;
									/*
									if($name == '威廉希尔' || $name=='澳门' || $name=='立博')
									{
										$result = 0;
										break;
									}
									*/
								}
							}
                            /*
							else
							{
								$bad = 1;
							}
                            */
						}
						if($result == 0)
						{
							return 2;
						}
						if($unfinish)
						{
								echo $param['num']."\n";
								$res_array['model2'][] = $link.$param['num'];
						}
						elseif($score[0] > $score[1])	// home win
						{
								if($bad == 1)
									return 0;
								echo $param['num']." ";
								foreach($my_array as $name)
								{
									if(!isset($all[$name]))
								    {
                                        $all[$name]['first']['win'] = 0;
                                        $all[$name]['first']['draw'] = 0;
                                        $all[$name]['first']['lost'] = 0;
                                        $all[$name]['end']['win'] = 0;
                                        $all[$name]['end']['draw'] = 0;
                                        $all[$name]['end']['lost'] = 0;
                                    }
									$first_win = $all[$name]['first']['win'];
									$first_draw = $all[$name]['first']['draw'];
									$first_lost = $all[$name]['first']['lost'];
									$end_win = $all[$name]['end']['win'];
									$end_draw = $all[$name]['end']['draw'];
									$end_lost = $all[$name]['end']['lost'];
									echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
								}
								echo "0 $score[0] $score[1] $time\n";
								return 0;
						}
						else
						{
								if($bad == 1)
									return 1;
								echo $param['num']." ";
								foreach($my_array as $name)
								{
									if(!isset($all[$name]))
								    {
                                        $all[$name]['first']['win'] = 0;
                                        $all[$name]['first']['draw'] = 0;
                                        $all[$name]['first']['lost'] = 0;
                                        $all[$name]['end']['win'] = 0;
                                        $all[$name]['end']['draw'] = 0;
                                        $all[$name]['end']['lost'] = 0;
                                    }
									$first_win = $all[$name]['first']['win'];
									$first_draw = $all[$name]['first']['draw'];
									$first_lost = $all[$name]['first']['lost'];
									$end_win = $all[$name]['end']['win'];
									$end_draw = $all[$name]['end']['draw'];
									$end_lost = $all[$name]['end']['lost'];
									echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
								}
								echo "1 $score[0] $score[1] $time\n";
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
									$all[$name]['end']['lost'] > $all[$name]['first']['lost'])
								)
								{
									/*
									$result = 0;
									break;
									if($name == '威廉希尔' || $name=='澳门' || $name=='立博')
									{
										$result = 0;
										break;
									}
									*/
								}
							}		
                            /*
							else
							{
								$bad = 1;
							}
                            */
						}
						if($result == 0)
						{
							return 2;
						}
						if($unfinish)
						{
								echo $param['num']."\n";
								$res_array['model2'][] = $link.$param['num'];
						}
						elseif($score[1] > $score[0])// away win
						{
								if($bad == 1)
									return 0;
								echo $param['num']." ";
								foreach($my_array as $name)
								{
									if(!isset($all[$name]))
								    {
                                        $all[$name]['first']['win'] = 0;
                                        $all[$name]['first']['draw'] = 0;
                                        $all[$name]['first']['lost'] = 0;
                                        $all[$name]['end']['win'] = 0;
                                        $all[$name]['end']['draw'] = 0;
                                        $all[$name]['end']['lost'] = 0;
                                    }
									$first_win = $all[$name]['first']['win'];
									$first_draw = $all[$name]['first']['draw'];
									$first_lost = $all[$name]['first']['lost'];
									$end_win = $all[$name]['end']['win'];
									$end_draw = $all[$name]['end']['draw'];
									$end_lost = $all[$name]['end']['lost'];
									echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
								}
								echo "0 $score[0] $score[1] $time\n";
								return 0;
						}
						else
						{
								if($bad == 1)
									return 0;
								echo $param['num']." ";
								foreach($my_array as $name)
								{
									if(!isset($all[$name]))
								    {
                                        $all[$name]['first']['win'] = 0;
                                        $all[$name]['first']['draw'] = 0;
                                        $all[$name]['first']['lost'] = 0;
                                        $all[$name]['end']['win'] = 0;
                                        $all[$name]['end']['draw'] = 0;
                                        $all[$name]['end']['lost'] = 0;
                                    }
									$first_win = $all[$name]['first']['win'];
									$first_draw = $all[$name]['first']['draw'];
									$first_lost = $all[$name]['first']['lost'];
									$end_win = $all[$name]['end']['win'];
									$end_draw = $all[$name]['end']['draw'];
									$end_lost = $all[$name]['end']['lost'];
									echo "$first_win $first_draw $first_lost $end_win $end_draw $end_lost ";
								}
								echo "1 $score[0] $score[1] $time\n";
								return 1;
						}
				}
		}
		return 2;
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

function Obser($date, &$good_array, &$bad_array, $unfinish, &$total)
{
		global $debug;
		global $debugnum;
		//$url = "http://trade.500.com/jczq/?playtype=nspf&date=".$date;
		//var_dump($url);
		$url = "http://live.500.com/zqdc.php?e=".$date;
		$contents = curl_get_contents($url);
		$str = mb_convert_encoding($contents, 'UTF-8', 'GBK');
		$tmp = $str;
		preg_match_all('/<a.+href\="(http[^\"]+fenxi\/ouzhi[^\"]+)"/', $tmp, $out, PREG_PATTERN_ORDER);
		preg_match_all('/center">(..-.....:..)<\/td>/', $tmp, $times, PREG_PATTERN_ORDER);
		if(count($times[1]) != count($out[1]))
		{
			var_dump($url);
			exit();
		}

		//var_dump($out);
		/*
		   $tmp = $str;
		   preg_match_all('/<a.class\=\"score\".href\=\"http[^\"]+fenxi\/shuju[^\"]+\".target\=\"\_blank\"\>(\d+:\d+)\<\/a\>/', $tmp, $score, PREG_PATTERN_ORDER);

		   if(count($out) != count($score))
		   {
		   echo "count not match \n";
		   exit;
		   }
		 */
		$res[1] = $out[1];
		//$res[2] = $score[1];
		//var_dump($res);
		$oupei="http://m.500.com/info/index.php?c=detail&a=ouzhiAjax&r=1&fid=";
		$scoreurl="http://m.500.com/info/live/?c=detail&fid=";
		$odd = array();
		$count = 0;
		$i = -1;
		foreach($res[1] as $k=>$url)
		{
				$i++;
				#echo ".";
				if($count++ > 1000)
				{
						echo "count > 1000 exit..\n";
						break;
				}
				preg_match("/ouzhi\-(.+)\./", $url, $num, PREG_OFFSET_CAPTURE, 0);
				//var_dump($num);
				$url = $oupei.$num[1][0];
				
				if($debug==1 && $num[1][0] != $debugnum)
				{
						continue;
				}

				//var_dump($url);
				//echo $num[1][0].",";
				//$url = "http://m.500.com/info/index.php?c=detail&a=ouzhiAjax&r=1&fid=529024";
				$contents = curl_get_contents($url);
				$json = json_decode($contents, true);

				$url = $scoreurl.$num[1][0];
				$tmp = curl_get_contents($url);
				//var_dump($tmp);
				preg_match('/d-game-time\"\>(\d+\&nbsp;:\&nbsp;\d+)</', $tmp, $out, PREG_OFFSET_CAPTURE);
				$param['score'] = str_replace("&nbsp;", "", $out[1][0]);
				$param['num'] = $num[1][0];
				$param['url'] = $url;
				$param['year'] = "20".substr($date,0,2);
				$param['time'] = $times[1][$i];
				if(!$unfinish && empty($param['score']))
				{
					#echo "empty score: ".$num[1][0]."\n";
						continue;
				}
				/*
				$ret = model1($param, $json['list'], $unfinish);
				if($ret == 1)
				{
						$param['res']='win';
						$good_array[1][] = $param;
						$total['model1'][] = $param;
				}
				elseif($ret == 0)
				{
						$param['res']='lose';
						$bad_array[1][] = $param;
						$total['model1'][] = $param;
				}
				*/
				$ret = model2($param, $json['list'], $unfinish);
				//var_dump($ret);
				if($ret == 1)
				{
						$param['res']='win';
						$good_array[2][] = $param;
						$total['model2'][] = $param;
				}
				elseif($ret == 0)
				{
						$param['res']='lose';
						$bad_array[2][] = $param;
						$total['model2'][] = $param;
				}
				/*
				$ret = model3($param, $json['list'], $unfinish);
				//var_dump($ret);
				if($ret == 1)
				{
						$param['res']='win';
						$good_array[3][] = $param;
						$total['model3'][] = $param;
				}
				elseif($ret == 0)
				{
						$param['res']='lose';
						$bad_array[3][] = $param;
						$total['model3'][] = $param;
				}
				*/
		}	
}
$start = $argv[1];
$end = $argv[2];
date_default_timezone_set("Asia/Shanghai");
//$time = strtotime($start);
//$end_time = strtotime($end);

$bad_array = array();
$good_array = array();
$total = array();
$unfinish = 0;
/*
if(time()-$time<60*60*33)
{
		echo "today match\n";
		$unfinish=1;
}
*/
if(substr($start, 2, 2) > 12 || substr($start, 4, 2)>20)
{
	$start--;
}
while($start >= $end)
{
		Obser($start, $good_array, $bad_array, $unfinish, $total);
		$start--;
}	
/*
echo "\n#####################model1:\n";
echo "bad\n";
var_dump($bad_array[1]);
echo "good\n";
var_dump($good_array[1]);
echo "#####################model2:\n";
echo "bad\n";
var_dump($bad_array[2]);
echo "good\n";
var_dump($good_array[2]);
echo "#####################model3:\n";
echo "bad\n";
var_dump($bad_array[3]);
echo "good\n";
var_dump($good_array[3]);
echo "#####################total:\n";
echo "total\n";
echo "bad\n";
var_dump(count($bad_array[1]));
echo "good\n";
var_dump(count($good_array[1]));
echo "bad\n";
var_dump(count($bad_array[2]));
echo "good\n";
var_dump(count($good_array[2]));
echo "bad\n";
var_dump(count($bad_array[3]));
echo "good\n";
var_dump(count($good_array[3]));
//var_dump($total);
$str_mail = $start."-".$end."\n";
$str_mail = $str_mail."model1 bad:\n".str_replace('\\', '', json_encode($bad_array[1]))."\n";
$str_mail = $str_mail."model1 good:\n".str_replace('\\', '', json_encode($good_array[1]))."\n";
$str_mail = $str_mail."model2 bad:\n".str_replace('\\', '', json_encode($bad_array[2]))."\n";
$str_mail = $str_mail."model2 good:\n".str_replace('\\', '', json_encode($good_array[2]))."\n";
$str_mail = $str_mail."model3 bad:\n".str_replace('\\', '', json_encode($bad_array[3]))."\n";
$str_mail = $str_mail."model3 good:\n".str_replace('\\', '', json_encode($good_array[3]))."\n";
$str_mail = $str_mail."result:\n".str_replace('\\', '', json_encode($res_array))."\n";
$ret = mail('xiesicong@baidu.com,241092598@qq.com', 'result', $str_mail);
var_dump($ret);
*/
