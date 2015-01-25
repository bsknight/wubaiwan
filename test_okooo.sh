#!/bin/sh
file=$1
i=1
while read line 
do
	echo $i, $line
	lua ./okooo.lua $line
	let i+=1
done<$file

