local ltn12 = require("ltn12")
local json = require("json")
local http = require("socket.http")
local string = require("string")

url = arg[1]
local header_ua = {
    ["User-Agent"] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36",
    ["Referer"]    = "http://www.okooo.com/soccer/match/"
}
function http.get(u)
    local t = {}
    local r, c, h = http.request{
            url = u,
            headers = header_ua,
            sink = ltn12.sink.table(t)}
    return r, c, h, table.concat(t)
end  

local _, _, _, body = http.get(url)
local liansai_array = {'英超', '意甲', '德甲', '西甲', '法甲', '荷甲', '葡超'}
aend=0;
hend=0;
_, title_end, title = string.find(body, "div class=\"liansai\"");
for data in string.gmatch(body, "onClick=\"_gaq\.push[^=]+href=\"([^\"']+odds/)\"") do
    _, hend, htitle  = string.find(body, "data%-hname=\"([^\"']+)\"", hend+1)  
    _, aend, atitle = string.find(body, "data%-aname=\"([^\"']+)\"", aend+1)
 	_, title_end, title = string.find(body, "href=[^=]+title=\"([^\"']+)\"", title_end+1);
   	--for key,value in pairs(liansai_array) do
	--end
	print(htitle, atitle, title)
	print("http://www.okooo.com"..data)
end

