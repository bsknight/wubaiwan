<?php
$debug=0;
$debugnum='511343';
$res_array = array();
$link="http://m.500.com/info/index.php?c=detail&fid=";
function model3($param, $odd, $unfinish)
{
		global $link;
		global $res_array;
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
				elseif($m['name'] == '必发')
				{
						$bifa['first']['win'] = $m['first']['win'];
						$bifa['end']['win'] = $m['end']['win'];
						$bifa['first']['draw'] = $m['first']['draw'];
						$bifa['end']['draw'] = $m['end']['draw'];
						$bifa['first']['lost'] = $m['first']['lost'];
						$bifa['end']['lost'] = $m['end']['lost'];
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
				$rang = cal_yazhi($param, $type);
				$param['rang'] = $rang;
				if($rang < 1)
				{
					return 2;
				}
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
									//if($name != "Interwetten")
									//{
									//}
									if($num >= 3)
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
						elseif($score[0] > $score[1])	// home lose
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
				$rang = cal_yazhi($param, $type);
				$param['rang'] = $rang;
				if($rang < 1)
				{
					return 2;
				}
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
									/*
									if($name != "Interwetten")
									{	
									}
									*/
									if($num >= 3)
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
						elseif($score[1] > $score[0])
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
function cal_yazhi($param, $type)
{
		$url = "http://m.500.com/info/index.php?c=detail&a=yazhiAjax&r=1&fid=".$param['num'];
		$tmp = curl_get_contents($url);
		$tmp = json_decode($tmp,true);
		$rang = 0;
		
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
		foreach($tmp['list'] as $m)
		{
			if($m['name'] == '伟德')
			{
				//var_dump($m['last']['handi']);
				if(in_array($m['last']['handi'], $pankou))
				{
					$rang = $rangqiu[$m['last']['handi']];
				}
				return $rang;
			}
		}
		return 0;
}

function model1(&$param, $odd, $unfinish)
{
		global $link;
		global $res_array;
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
				/*
				var_dump($all['立博']);
				var_dump($all['威廉希尔']);
				var_dump($all['澳门']);
				var_dump($all['Interwetten']);
				*/
		}

		if($type == 'homelow')
		{
				if($avg['first']['win'] < 2 && 
								$avg['end']['win'] < $avg['first']['win'] && 
								$avg['end']['draw'] > $avg['first']['draw'] && 
								$avg['end']['lost'] > $avg['first']['lost'] && 

								//$jingcai['end']['win'] <= $jingcai['first']['win'] &&
								//$jingcai['end']['draw'] >= $jingcai['first']['draw'] &&
								//$jingcai['end']['lost'] >= $jingcai['first']['lost'] &&

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
												$rang = 0;
												$rang = cal_yazhi($param, $type);
												$param['rang'] = $rang;
												if($rang < 1 && $rang!=0 && !$unfinish)
												{
													return 2;
												}
												if($unfinish)
												{
														echo 'model1:'.$param['num']."\n";
														$res_array['model1'][] = $link.$param['num'];
												}
												elseif($score[0] > $score[1]+$rang)	// home win
												{
														return 1;
												}
												elseif($score[0] <= $score[1]+$rang)
												{
														return 0;
												}
										}
		}
		elseif($type == 'awaylow')
		{
				if($avg['first']['lost'] < 2 && 
						
								$avg['end']['win'] >= $avg['first']['win'] && 
								$avg['end']['draw'] >= $avg['first']['draw'] && 
								$avg['end']['lost'] <= $avg['first']['lost'] && 

								//$jingcai['end']['win'] >= $jingcai['first']['win'] &&
								//$jingcai['end']['draw'] >= $jingcai['first']['draw'] &&
								//$jingcai['end']['lost'] <= $jingcai['first']['lost'] &&

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
												$rang = cal_yazhi($param, $type);
												$param['rang'] = $rang;
												if($rang < 1 && $rang!=0 && !$unfinish)
												{
													return 2;
												}
												if($unfinish)
												{
														echo 'model1:'.$param['num']."\n";
														$res_array['model1'][] = $link.$param['num'];
												}
												elseif($score[1] > $score[0]+$rang)// away win
												{
														return 1;
												}
												elseif($score[1] <= $score[0]+$rang)
												{
														return 0;
												}
										}
		}
		return 2;
}
function model2($param, $odd, $unfinish)
{
		global $link;
		global $res_array;
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
								if(!($all[$name]['end']['win'] > $all[$name]['first']['win'] &&
									$all[$name]['end']['lost'] < $all[$name]['first']['lost'])
								)
								{
									//if($name == '威廉希尔' || $name=='澳门' || $name=='立博' || $name == 'Interwetten')
									if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
									{
										$result = 0;
										break;
									}
								}
								if(!($all[$name]['end']['draw'] < $all[$name]['first']['draw']))
								{       
									//if($name == 'Interwetten' || $name == 'Bet365' || $name == '威廉希尔')
									if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
									{       
										$num++; 
										if($num >= 2)
										{       
											$result = 0;
											break;  
										}       
									}       
								} 
									
							}
                            else
                            {
                                $bad = 1;
                                break;
                            }
						}
						if($result == 0 || $bad == 1)
						{
							return 2;
						}
						if($unfinish)
						{
								echo "model2:".$param['num']."\n";
								$res_array['model2'][] = $link.$param['num'];
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
								if(!($all[$name]['end']['win'] < $all[$name]['first']['win'] &&
									$all[$name]['end']['lost'] > $all[$name]['first']['lost'])
								)
								{
									//if($name == '威廉希尔' || $name=='澳门' || $name=='立博' || $name == 'Interwetten')
									if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
									{
										$result = 0;
										break;
									}
								}
                                if(!($all[$name]['end']['draw'] < $all[$name]['first']['draw']))
                                {
                                    //if($name == 'Interwetten' || $name == 'Bet365' || $name == '威廉希尔')
                                    //if($name == 'Interwetten')
									if($name == '威廉希尔' || $name == 'Interwetten' || $name=='伟德')
                                    {
                                        $num++;
                                        if($num >= 2)
                                        {
                                            $result = 0;
                                            break;
                                        }
									}
								}
							}			
                            else
                            {
                                $bad = 1;
                                break;
                            }
						}
						if($result == 0 || $bad == 1)
						{
							return 2;
						}
						if($unfinish)
						{
								echo "model2:".$param['num']."\n";
								$res_array['model2'][] = $link.$param['num'];
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

function curl_get_contents($url)
{
		global $curlError;
		$headerArr = array(
						'Accept-Language: en'
						);
		$userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.85 Safari/537.36';
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

function Obser($date, &$good_array, &$bad_array, $unfinish, &$total)
{
		global $debug;
		global $debugnum;
		//$url = "http://trade.500.com/jczq/?playtype=nspf&date=".$date;
		//$url = "http://trade.500.com/bjdc/";
		//$unfinish = 1;
		//var_dump($url);
		$url = "http://live.500.com/zqdc.php?e=".$date;
		$contents = curl_get_contents($url);

		$str = mb_convert_encoding($contents, 'UTF-8', 'GBK');
		$tmp = $str;
		preg_match_all('/<a.+href\="(http[^\"]+fenxi\/ouzhi[^\"]+)"/', $tmp, $out, PREG_PATTERN_ORDER);
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
		$oupei="http://m.500.com/info/index.php?c=detail&a=ouzhiAjax&r=1&fid=";
		$scoreurl="http://m.500.com/info/live/?c=detail&fid=";
		$odd = array();
		$count = 0;
		foreach($res[1] as $k=>$url)
		{
				echo ".";
				if($count++ > 1000)
				{
						echo "count > 1000 exit..\n";
						break;
				}
				preg_match("/ouzhi\-(.+)\./", $url, $num, PREG_OFFSET_CAPTURE, 0);
				$url = $oupei.$num[1][0];
				//$url = $oupei."560756";
				if($debug==1 && $num[1][0] != $debugnum)
				{
						continue;
				}

				//var_dump($url);
				//echo $num[1][0].",";
				//$url = "http://m.500.com/info/index.php?c=detail&a=ouzhiAjax&r=1&fid=529024";
				$contents = curl_get_contents($url);
				$json = json_decode($contents, true);
//var_dump($json);
				$url = $scoreurl.$num[1][0];
				$tmp = curl_get_contents($url);
				//var_dump($tmp);
				preg_match('/d-game-time\"\>(\d+\&nbsp;:\&nbsp;\d+)</', $tmp, $out, PREG_OFFSET_CAPTURE);
				$param['score'] = str_replace("&nbsp;", "", $out[1][0]);
				$param['num'] = $num[1][0];
				$param['url'] = $url;
				if(!$unfinish && empty($param['score']))
				{
						echo "empty score: ".$num[1][0]."\n";
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
$curlError = array();
$unfinish = 1;
/*
if(time()-$time<60*60*33)
{
		echo "today match\n";
		$unfinish=1;
}
*/
while($start >= $end)
{
		//$ret = date('Y-m-d', $time);
		//echo "\n";
		var_dump($start);
		Obser($start, $good_array, $bad_array, $unfinish, $total);
		$start--;
}
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
$str_mail = $argv[1]."-".$argv[2]."\n";
$str_mail = $str_mail."model1 bad:\n".str_replace('\\', '', json_encode($bad_array[1]))."\n";
$str_mail = $str_mail."model1 good:\n".str_replace('\\', '', json_encode($good_array[1]))."\n";
$str_mail = $str_mail."model2 bad:\n".str_replace('\\', '', json_encode($bad_array[2]))."\n";
$str_mail = $str_mail."model2 good:\n".str_replace('\\', '', json_encode($good_array[2]))."\n";
$str_mail = $str_mail."model3 bad:\n".str_replace('\\', '', json_encode($bad_array[3]))."\n";
$str_mail = $str_mail."model3 good:\n".str_replace('\\', '', json_encode($good_array[3]))."\n";
$str_mail = $str_mail."result:\n".str_replace('\\', '', json_encode($res_array))."\n";
$str_mail = $str_mail."curl_error:\n".str_replace('\\', '', json_encode($curlError))."\n";
$ret = mail('xiesicong@baidu.com,241092598@qq.com', 'result', $str_mail);
var_dump($ret);

