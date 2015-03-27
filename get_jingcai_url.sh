#!/bin/sh
url=$1
file=$2
lua get_jingcai_url.lua $url > $file
iconv -f gbk -t utf-8 $file > $file".utf8"
rm $file
