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

aend=0;
hend=0;
for data in string.gmatch(body, "onClick=\"_gaq\.push[^=]+href=\"([^\"']+odds/)\"") do
    _, hend, htitle  = string.find(body, "data%-hname=\"([^\"']+)\"", hend+1)  
    _, aend, atitle = string.find(body, "data%-aname=\"([^\"']+)\"", aend+1)
    print(htitle, atitle)
	print("http://www.okooo.com"..data)
end

