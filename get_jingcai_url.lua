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

for data in string.gmatch(body, "onClick=\"_gaq\.push[^=]+href=\"([^\"']+odds/)\"") do
	print("http://www.okooo.com"..data)
end
--j = 0
-- while true do
	
-- 	i, j, data = string.find(body, "onClick=\"_gaq\.push[^=]+href=\"([^\"']+odds)/\"", j+1)
-- 	print(i, j, data)	
-- 	if j == nil then
-- 		break
-- 	end
-- end
