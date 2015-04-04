
local ltn12 = require("ltn12")
local json = require("json")
local http = require("socket.http")
local string = require("string")

url = arg[1]..'ajax/?companytype=BaijiaBooks&type=1'
url_all = arg[1]..'ajax/?all=1&companytype=BaijiaBooks&type=1'

--print(url)
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

function get_draw()
    local i=0;
    drawArray={key='', ['key']=''}    
    drawCompanys={{code='BINGOAL', text='BINGOAL'},
                    {code='Fortuna', text='Fortuna'},
                    {code='\u9999\u6e2f\u9a6c\u4f1a', text='香港马会'},                     
                    {code='Wettpunkt', text='Wettpunkt'}, 
                }  

    local _, _, _, body = http.get(url_all)             
    local _, _, data = string.find(body, "data_str.=.'([^']+)';")
    local res = json.decode(data)

    for k,v in pairs(drawCompanys) do
        for k1,v1 in pairs(res) do
            if v1['CompanyName'] == v.code then
                    drawArray[v.text] = {
                            Name = v.text,
                            Start = {
                                home = tonumber(v1['Start']['home']),   
                                draw = tonumber(v1['Start']['draw']),           
                                away = tonumber(v1['Start']['away']),
                            },    
                            End = {
                                home = tonumber(v1['End']['home']),          
                                draw = tonumber(v1['End']['draw']),    
                                away = tonumber(v1['End']['away']),
                            }
                        }
                    i=i+1
                    break;
            end
        end
    end
    --[[
    for k,v in pairs(drawCompanys) do
        print(drawArray[v.text].Start.home,drawArray[v.text].Start.draw,drawArray[v.text].Start.away,
            drawArray[v.text].End.home,drawArray[v.text].End.draw,drawArray[v.text].End.away)
    end
    ]]--
    return drawArray;
end

array={key='', ['key']=''}
book={Name='', Start={home=0,draw=0,away=0}, End={home=0,draw=0,away=0}}
companys={{code='\u5a01\u5ec9.\u5e0c\u5c14', text='威廉希尔'}, 
                {code='Bet365', text='Bet365'}, 
                {code='bwin', text='bwin'}, 
                {code='\u4f1f\u5fb7\u56fd\u9645', text='伟德国际'}, 
                {code='\u6fb3\u95e8\u5f69\u7968', text='澳门彩票'}, 
                {code='\u7acb\u535a', text='立博'}, 
                {code='Interwetten', text='Interwetten'}, 
                {code='\u535a\u5929\u5802', text='博天堂'},
                --{code='\u6613\u80dc\u535a', text='易胜博'},
                {code='SNAI', text='SNAI'},
                {code='Coral', text='Coral'}}
local location={}

local _, _, _, body = http.get(url)  
--print(url)
local _, _, data = string.find(body, "data_str.=.'([^']+)';")
local res = json.decode(data)
local start_total_home=0;
local start_total_draw=0;
local start_total_away=0;
local end_total_home=0;
local end_total_draw=0;
local end_total_away=0;
local i=0;
--get companys book	
for k,v in pairs(res) do
	for k1,v1 in pairs(companys) do
			if v['CompanyName'] == v1.code then
                array[v1.text] = {
                        Name = v1.text,
                        Start = {
                            home = tonumber(v['Start']['home']),   
                            draw = tonumber(v['Start']['draw']),           
                            away = tonumber(v['Start']['away']),
                        },    
                        End = {
                            home = tonumber(v['End']['home']),          
                            draw = tonumber(v['End']['draw']),    
                            away = tonumber(v['End']['away']),
                        }
                    }
                start_total_home = start_total_home + v['Start']['home']
                start_total_draw = start_total_draw + v['Start']['draw']		
                start_total_away = start_total_away + v['Start']['away']			
                end_total_home = end_total_home + v['End']['home']			          
                end_total_draw = end_total_draw + v['End']['draw']			          
                end_total_away = end_total_away + v['End']['away']
                --print(book.Start.home)
                
                i=i+1
			end
	end
end
--print(i)
local size = table.getn(companys)
--print(size)
book.Name = 'avg';
book.Start.home = start_total_home/size;
book.Start.draw = start_total_draw/size;
book.Start.away = start_total_away/size;
book.End.home = end_total_home/size;
book.End.draw = end_total_draw/size;
book.End.away = end_total_away/size;

drawArray = get_draw()
drawBook={Name='', Start={home=0,draw=0,away=0}, End={home=0,draw=0,away=0}}
drawBook.Name = 'brawAvg'
local start_total_home=0;
local start_total_draw=0;
local start_total_away=0;
local end_total_home=0;
local end_total_draw=0;
local end_total_away=0;
drawBook.Start.home = (array['Bet365'].Start.home+array['bwin'].Start.home+array['威廉希尔'].Start.home
                    +array['立博'].Start.home+array['伟德国际'].Start.home+array['澳门彩票'].Start.home
                    +array['Coral'].Start.home+drawArray['BINGOAL'].Start.home+drawArray['Fortuna'].Start.home
                    +drawArray['香港马会'].Start.home+drawArray['Wettpunkt'].Start.home)/11
--print(drawBook.Start.home)
drawBook.Start.draw = (array['Bet365'].Start.draw+array['bwin'].Start.draw+array['威廉希尔'].Start.draw
                    +array['立博'].Start.draw+array['伟德国际'].Start.draw+array['澳门彩票'].Start.draw
                    +array['Coral'].Start.draw+drawArray['BINGOAL'].Start.draw+drawArray['Fortuna'].Start.draw
                    +drawArray['香港马会'].Start.draw+drawArray['Wettpunkt'].Start.draw)/11
--print(drawBook.Start.draw)
drawBook.Start.away = (array['Bet365'].Start.away+array['bwin'].Start.away+array['威廉希尔'].Start.away
                    +array['立博'].Start.away+array['伟德国际'].Start.away+array['澳门彩票'].Start.away
                    +array['Coral'].Start.away+drawArray['BINGOAL'].Start.away+drawArray['Fortuna'].Start.away
                    +drawArray['香港马会'].Start.away+drawArray['Wettpunkt'].Start.away)/11
--print(drawBook.Start.away)
drawBook.End.home = (array['Bet365'].End.home+array['bwin'].End.home+array['威廉希尔'].End.home
                    +array['立博'].End.home+array['伟德国际'].End.home+array['澳门彩票'].End.home
                    +array['Coral'].End.home+drawArray['BINGOAL'].End.home+drawArray['Fortuna'].End.home
                    +drawArray['香港马会'].End.home+drawArray['Wettpunkt'].End.home)/11
--print(drawBook.End.home)
drawBook.End.draw = (array['Bet365'].End.draw+array['bwin'].End.draw+array['威廉希尔'].End.draw
                    +array['立博'].End.draw+array['伟德国际'].End.draw+array['澳门彩票'].End.draw
                    +array['Coral'].End.draw+drawArray['BINGOAL'].End.draw+drawArray['Fortuna'].End.draw
                    +drawArray['香港马会'].End.draw+drawArray['Wettpunkt'].End.draw)/11
--print(drawBook.End.draw)
drawBook.End.away = (array['Bet365'].End.away+array['bwin'].End.away+array['威廉希尔'].End.away
                    +array['立博'].End.away+array['伟德国际'].End.away+array['澳门彩票'].End.away
                    +array['Coral'].End.away+drawArray['BINGOAL'].End.away+drawArray['Fortuna'].End.away
                    +drawArray['香港马会'].End.away+drawArray['Wettpunkt'].End.away)/11
--print(drawBook.End.away)


function draw5companys(array, drawArray, drawBook)
    local i = 0;
    if drawBook.Start.draw > drawBook.End.draw then
        return 0
    end
    for k,v in pairs(drawCompanys) do
        if drawArray[v.text].Start.draw > drawBook.Start.draw and drawArray[v.text].End.draw > drawBook.End.draw   then
            i = i+1
        end
    end

    if array['Coral'].Start.draw > drawBook.Start.draw and array['Coral'].End.draw > drawBook.End.draw then
        i = i+1
    end
    return i
end

function weilianxier(array, book)
    draw = 1
	lowest = 1
    for k,v in pairs(companys) do 
        if book.End.draw > book.Start.draw then     
            if array[v.text].End.draw < array[v.text].Start.draw then
                --print('平局提升模式匹配失败，保留平局')
                draw = 1
            else
				draw = 0
			end
		end
        if array['威廉希尔'].Start.draw > array[v.text].Start.draw then
    		lowest = 0
		end
	end
	if lowest == 1 then
		print('威廉希尔初赔平赔最低，警惕冷门')
	end
    return draw
end

function interwetten(array, book, homelow)
    local res = 1
    point = array['Interwetten']
    if point.Start.draw > book.Start.draw then
        print('Interwetten平赔高于平均，排除平局')
    end   
end


function libo(array, book, homelow)
    local res = 1
    point = array['立博']
    if homelow == 1 then
        if book.Start.home < book.End.home then
            --print('胜赔提升，警惕负赔冷出')
        end
        if point.Start.home > book.Start.home then
            res = 0
            return res
        end
    else
        if book.Start.away < book.End.away then
            --print('负赔提升，警惕胜赔冷出')
        end        
        if point.Start.away > book.Start.away then
            res = 0
            return res
        end       
    end

    return res
end

function coral(array, book, homelow)
    local lowest = 1
    for k,v in pairs(companys) do
        if array['Coral'].Start.away > array[v.text].Start.away then
            lowest = 0
            break
        end
    end    
    if lowest == 1 then
        print('Coral初赔负赔最低，关注赔率')
    end 

    local lowest = 1
    for k,v in pairs(companys) do
        if array['Coral'].Start.draw > array[v.text].Start.draw then
            lowest = 0
            break
        end
    end    
    if lowest == 1 then
        print('Coral初赔平赔最低，关注赔率')
    end   

    local lowest = 1
    for k,v in pairs(companys) do
        if array['Coral'].Start.home > array[v.text].Start.draw then
            lowest = 0
            break
        end
    end    
    if lowest == 1 then
        print('Coral初赔胜赔最低，关注赔率')
    end   
end

function botiantang(array, book, homelow)
    local lose = 0

    if homelow == 1 then
        local lowest = 1
        for k,v in pairs(companys) do
            if array['博天堂'].Start.away > array[v.text].Start.away then
                lowest = 0
                break
            end
        end    
        if lowest == 1 then
            print('博天堂初赔最低，冷门不可弃！')
            return 1
        end
        --看各家波动方向是否一致
        if book.End.away > book.Start.away then 
            for k,v in pairs(companys) do 
                if array[v.text].End.away<array[v.text].Start.away then
                    --不一致，去博天堂初赔
                    if array['博天堂'].Start.away > book.Start.away then
                        lose = 0
                    else 
                        lose = 1
                    end
                    return lose
                end
                
            end
            lose = 0
            return lose
        elseif book.End.away < book.Start.away then 
                for k,v in pairs(companys) do
                    if array[v.text].End.away > array[v.text].Start.away then
                        if array['博天堂'].Start.away > book.Start.away then
                            lose = 0;
                        else 
                            lose = 1
                        end
                        return lose
                    end
                end
                lose = 1
                return lose
        end
        lose = 1
        return lose
 
    else --胜赔高
        local lowest = 1
        for k,v in pairs(companys) do
            if array['博天堂'].Start.home > array[v.text].Start.home then
                lowest = 0
                break;
            end
        end    
        if lowest == 1 then
            print('博天堂初赔最低，冷门不可弃！')
            return 1
        end

        --看各家波动方向是否一致
        if book.End.home > book.Start.home then 
            for k,v in pairs(companys) do                
                if array[v.text].End.home < array[v.text].Start.home then
                    --不一致，去博天堂初赔
                    if array['博天堂'].Start.home > book.Start.home then
                        lose = 0;
                    else 
                        lose = 1
                    end
                    return lose
                end
            end
            lose = 0
            return lose            
        elseif book.End.home < book.Start.home then
                for k,v in pairs(companys) do
                    if array[v.text].End.home > array[v.text].Start.home then
                        if array['博天堂'].Start.home > book.Start.home then
                            lose = 0;
                        else 
                            lose = 1
                        end
                        return lose
                    end
                end
                lose = 1
                return lose
        end
        lose = 1
        return lose
    end   
end

function home(array, book, homelow)
    for k,v in pairs(companys) do    
        if array[v.text].Start.home > book.Start.home then
            print(v.text)
        end           
    end
end
win=1
draw=1
lose=1
homelow=1
print(book.Start.home, book.Start.draw, book.Start.away)
interwetten(array, book, homelow)
--coral(array, drawArray, drawBook)
if (book.Start.home > book.Start.away and book.End.home < book.End.away) or 
    (book.Start.home < book.Start.away and book.End.home > book.End.away)then
    print("摇摆盘谨慎双选")
    draw = 0
    for k,v in pairs(companys) do
        if book.End.draw > book.Start.draw then
            if array[v.text].End.draw < array[v.text].Start.draw then
                --print('平局提升模式匹配失败，保留平局')
                draw = 1
                break
            end
        else if book.End.draw < book.Start.draw then
                if array[v.text].End.draw > array[v.text].Start.draw then
                    --print('平局提升模式匹配失败，保留平局')
                    draw = 1
                    break
                end
            end
        end
    end 

    i = draw5companys(array, drawArray, drawBook)
    if i >= 3 then
        print('五家平赔模式匹配通过，平局概率降低')
        if drawBook.Start.home > 2 and drawBook.Start.away > 2 then
            print('平半盘五家平赔可能失效')
        end
    end

    print(win,draw,lose)  
    return
else if  book.Start.home < book.Start.away then
        homelow = 1
    else
        homelow = 0
    end
end

if array['威廉希尔'].Start.draw == array['立博'].Start.draw then
    --print('威廉希尔立博同平赔，关注下盘')
end

if array['威廉希尔'].Start.draw < array['澳门彩票'].Start.draw then
    print("威廉希尔平赔低于澳门，关注冷门，五家平赔模式失效，博天堂冷赔模式失效！")
    draw = weilianxier(array, book)

    i = draw5companys(array, drawArray, drawBook)
    if i >= 3 then
        print('五家平赔模式匹配通过，平局概率降低')
        if drawBook.Start.home > 2 and drawBook.Start.away > 2 then
            print('平半盘五家平赔可能失效')
        end
    end

    if homelow == 1 then
        lose = botiantang(array, book, homelow)
        --win = libo(array, book, homelow)
    else
        win = botiantang(array, book, homelow)
        --lose = libo(array, book, homelow)
    end     
else
    draw = weilianxier(array, book)

    i = draw5companys(array, drawArray, drawBook)
    if i >= 3 then
        print('五家平赔模式匹配通过，平局概率降低')
        if drawBook.Start.home > 2 and drawBook.Start.away > 2 then
            print('平半盘五家平赔可能失效')
        end
    end

    if homelow == 1 then
        lose = botiantang(array, book, homelow)
        --win = libo(array, book, homelow)
    else
        win = botiantang(array, book, homelow)
        --lose = libo(array, book, homelow)
    end 
end   



print(win,draw,lose) 
