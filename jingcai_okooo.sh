#!/bin/sh
file=$1
i=1
j=1
#`iconv -f gbk -t utf-8 ${file}>run.txt`
while read line 
do
	if [ $(($i%2)) != 0 ]; then
		echo $j, $line
		let j+=1
	else
		echo $line
		lua ./okooo.lua $line
		echo ''
	fi
	let i+=1
#done<'run.txt'
done<$file
