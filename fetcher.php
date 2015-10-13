<?php
$debug=0;
$debugnum='522025';
function model1($param, $odd, $unfinish)
{
		global $debug;
		global $debugnum;
		$score = explode(":",$param['score']);
		$score[0] = intval($score[0]);
		$score[1] = intval($score[1]);

		$my_array = array("威廉希尔","澳门","立博","Bet365","Interwetten","SNAI","伟德","Bwin","Coral","SportingBet (博天堂)");
		$all = array();
		$avg['first'] = array();
		$avg['end'] = array();
		$jingcai = array();
		$count = 0;
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

		$avg['first']['win'] /= $count;
		$avg['end']['win'] /= $count;
		$avg['first']['draw'] /= $count;
		$avg['end']['draw'] /= $count;
		$avg['first']['lost'] /= $count;
		$avg['end']['lost'] /= $count;
		//var_dump($avg);
		$type = $avg['first']['win'] < $avg['first']['lost']? 'homelow':'awaylow';

		if($debug==1 && $param['num'] == $debugnum)
		{
				var_dump($avg);
				var_dump($type);
		}

		if($type == 'homelow')
		{
				if($avg['first']['win'] < 2 && 
								$avg['end']['win'] < $avg['first']['win'] && 
								$avg['end']['draw'] > $avg['first']['draw'] && 
								$avg['end']['lost'] > $avg['first']['lost'] && 

								$jingcai['end']['win'] <= $jingcai['first']['win'] &&
								$jingcai['end']['draw'] >= $jingcai['first']['draw'] &&
								$jingcai['end']['lost'] >= $jingcai['first']['lost'] &&

								$all['立博']['end']['win'] <= $all['立博']['first']['win'] &&
								$all['立博']['end']['draw'] >= $all['立博']['first']['draw'] &&
								$all['立博']['end']['lost'] >= $all['立博']['first']['lost'] &&
								
								$all['威廉希尔']['end']['win'] <= $all['威廉希尔']['first']['win'] &&
								$all['威廉希尔']['end']['draw'] >= $all['威廉希尔']['first']['draw'] &&
								$all['威廉希尔']['end']['lost'] >= $all['威廉希尔']['first']['lost'] &&

								$all['澳门']['end']['win'] <= $all['澳门']['first']['win'] &&
								$all['澳门']['end']['draw'] >= $all['澳门']['first']['draw'] &&
								$all['澳门']['end']['lost'] >= $all['澳门']['first']['lost'] &&

								$all['Interwetten']['end']['win'] < $all['Interwetten']['first']['win'] &&
								$all['Interwetten']['end']['lost'] > $all['Interwetten']['first']['lost'] &&
								$all['Interwetten']['end']['draw'] > $all['Interwetten']['first']['draw'] 
										)
										{
												if($unfinish)
												{
														echo 'model1:'.$param['num']."\n";
												}
												elseif($score[0] > $score[1])	// home win
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
				if($avg['first']['lost'] < 2 && 
								$avg['end']['win'] >= $avg['first']['win'] && 
								$avg['end']['draw'] >= $avg['first']['draw'] && 
								$avg['end']['lost'] <= $avg['first']['lost'] && 

								$jingcai['end']['win'] >= $jingcai['first']['win'] &&
								$jingcai['end']['draw'] >= $jingcai['first']['draw'] &&
								$jingcai['end']['lost'] <= $jingcai['first']['lost'] &&

								$all['立博']['end']['win'] >= $all['立博']['first']['win'] &&
								$all['立博']['end']['draw'] >= $all['立博']['first']['draw'] &&
								$all['立博']['end']['lost'] <= $all['立博']['first']['lost'] &&
								
								$all['威廉希尔']['end']['win'] >= $all['威廉希尔']['first']['win'] &&
								$all['威廉希尔']['end']['draw'] >= $all['威廉希尔']['first']['draw'] &&
								$all['威廉希尔']['end']['lost'] <= $all['威廉希尔']['first']['lost'] &&

								$all['澳门']['end']['win'] >= $all['澳门']['first']['win'] &&
								$all['澳门']['end']['draw'] >= $all['澳门']['first']['draw'] &&
								$all['澳门']['end']['lost'] <= $all['澳门']['first']['lost'] &&

								$all['Interwetten']['end']['win'] > $all['Interwetten']['first']['win'] &&
								$all['Interwetten']['end']['lost'] < $all['Interwetten']['first']['lost'] &&
								$all['Interwetten']['end']['draw'] > $all['Interwetten']['first']['draw'] 
										)
										{
												if($unfinish)
												{
														echo 'model1:'.$param['num']."\n";
												}
												elseif($score[1] > $score[0])// away win
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
}
function model2($param, $odd, $unfinish)
{
		global $debug;
		global $debugnum;
		$score = explode(":",$param['score']);
		$score[0] = intval($score[0]);
		$score[1] = intval($score[1]);

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

		$avg['first']['win'] /= $count;
		$avg['end']['win'] /= $count;
		$avg['first']['draw'] /= $count;
		$avg['end']['draw'] /= $count;
		$avg['first']['lost'] /= $count;
		$avg['end']['lost'] /= $count;
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
				if($avg['end']['win'] > 1.9 && 
								$avg['end']['win'] > $avg['first']['win'] && 
								$jingcai['end']['win'] > 2 &&
								$jingcai['end']['win'] > $jingcai['first']['win'] &&
								$all['威廉希尔']['first']['draw'] <= $all['澳门']['first']['draw'] &&
								(
										($
										$all['澳门']['end']['draw'] <= $all['澳门']['first']['draw']  &&
										$all['威廉希尔']['end']['win'] > $all['威廉希尔']['first']['win'] &&
										$all['威廉希尔']['end']['draw'] <= $all['威廉希尔']['first']['draw'] ) ||
										(
										$all['威廉希尔']['end']['draw'] <= $all['威廉希尔']['first']['draw'] &&
										$all['澳门']['end']['win'] > $all['澳门']['first']['win'] &&
										$all['澳门']['end']['draw'] <= $all['澳门']['first']['draw'] ) 
								)&&
								$all['立博']['end']['draw'] <= $all['立博']['first']['draw']  &&
								$all['SportingBet (博天堂)']['end']['lost'] <= $all['SportingBet (博天堂)']['first']['lost'] &&
								$all['Interwetten']['end']['win'] > $all['Interwetten']['first']['win'])
				{
						if($unfinish)
						{
								echo $param['num']."\n";
						}
						elseif(!($score[0] <= $score[1]))	// home win
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
				if($avg['end']['lost'] > 1.9 && 
								$avg['end']['lost'] > $avg['first']['lost'] && 
								$jingcai['end']['lost'] > 2 &&
								$jingcai['end']['lost'] > $jingcai['first']['lost'] &&
								$all['威廉希尔']['first']['draw'] <= $all['澳门']['first']['draw'] &&
								(
										(
										$all['澳门']['end']['draw'] <= $all['澳门']['first']['draw'] &&
										$all['威廉希尔']['end']['lost'] > $all['威廉希尔']['first']['lost'] &&
										$all['威廉希尔']['end']['draw'] <= $all['威廉希尔']['first']['draw'] )||
										(
										$all['威廉希尔']['end']['draw'] <= $all['威廉希尔']['first']['draw'] &&
										$all['澳门']['end']['lost'] > $all['澳门']['first']['lost'] &&
										$all['澳门']['end']['draw'] <= $all['澳门']['first']['draw'] )
								)&&
								$all['立博']['end']['draw'] <= $all['立博']['first']['draw']  &&
								$all['SportingBet (博天堂)']['end']['win'] <= $all['SportingBet (博天堂)']['first']['win'] &&
								$all['Interwetten']['end']['lost'] > $all['Interwetten']['first']['lost'])
				{
						if($unfinish)
						{
								echo $param['num']."\n";
						}
						elseif(!($score[1] <= $score[0]))// away win
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
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);           //设置超时
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);   //用户访问代理 User-Agent
		//curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		//curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        //设置 referer
		//curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      //跟踪301
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
}

function Obser($date, &$good_array, &$bad_array, $unfinish)
{
		global $debug;
		global $debugnum;
		$url = "http://trade.500.com/jczq/?playtype=nspf&date=".$date;
		//var_dump($url);
		$contents = curl_get_contents($url);
		$str = mb_convert_encoding($contents, 'UTF-8', 'GBK');
		$tmp = $str;
		//var_dump($str);	
		preg_match_all('/<a.href\="(http[^\"]+fenxi\/ouzhi[^\"]+)"/', $tmp, $out, PREG_PATTERN_ORDER);
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
		foreach($res[1] as $k=>$url)
		{
				if($count++ > 100)
				{
						echo "count > 100 exit..\n";
						break;
				}
				preg_match("/ouzhi\-(.+)\./", $url, $num, PREG_OFFSET_CAPTURE, 0);
				//var_dump($num);
				$url = $oupei.$num[1][0];

				if($debug==1 && $num[1][0] != $debugnum)
				{
						continue;
				}

				var_dump($url);
				//$url = "http://m.500.com/info/index.php?c=detail&a=ouzhiAjax&r=1&fid=529024";
				$contents = curl_get_contents($url);
				$json = json_decode($contents, true);

				$url = $scoreurl.$num[1][0];
				$tmp = curl_get_contents($url);
				//var_dump($tmp);
				preg_match('/d-game-time\"\>(\d+\&nbsp;:\&nbsp;\d+)</', $tmp, $out, PREG_OFFSET_CAPTURE);
				$param['score'] = str_replace("&nbsp;", "", $out[1][0]);
				$param['num'] = $num[1][0];
				if(!$unfinish && empty($param['score']))
				{
						echo "empty score: ".$num[1][0]."\n";
						continue;
				}
				$ret = model1($param, $json['list'], $unfinish);
				if($ret == 1)
				{
						$good_array[1][] = $param;
				}
				elseif($ret == 0)
				{
						$bad_array[1][] = $param;
				}

				$ret = model2($param, $json['list'], $unfinish);
				//var_dump($ret);
				if($ret == 1)
				{
						$good_array[2][] = $param['num'];
				}
				elseif($ret == 0)
				{
						$bad_array[2][] = $param['num'];
				}

		}	
}
$start = $argv[1];
$end = $argv[2];
date_default_timezone_set("Asia/Shanghai");
$time = strtotime($start);
$end_time = strtotime($end);

$bad_array = array();
$good_array = array();
$unfinish = 0;
if(time()-$time<60*60*32)
{
		echo "today match\n";
		$unfinish=1;
}
while($time >= $end_time)
{
		$ret = date('Y-m-d', $time);
		var_dump($ret);
		Obser($ret, $good_array, $bad_array, $unfinish);
		$time-=60*60*24;
}
echo "#####################model1:\n";
echo "bad\n";
var_dump($bad_array[1]);
echo "good\n";
var_dump($good_array[1]);
echo "#####################model2:\n";
echo "bad\n";
var_dump($bad_array[2]);
echo "good\n";
var_dump($good_array[2]);
