LoginURL= "https://free-gsscript-arc.onrender.com/Login.php" 

function split(s, delimiter)  
    result = {} 
    for match in (s..delimiter):gmatch("(.-)"..delimiter) do 
        table.insert(result, match) 
    end 
    return result 
end 

function WriteText(Direc,Text)
    f = io.open(Direc, "w") 
    f:write(Text) 
    f:close() 
end  

function MakeRequest(URL,parm)  
    Content = gg.makeRequest(URL,nil,parm).content 
    gg.alert("MakeRequest Content: "..tostring(Content))  -- Debug alert added
    load('Dfile  = '.."string.char(table.unpack(".. "{"..Content:gsub(string.char(tonumber("40",16))..string.char(tonumber("45",16))..string.char(tonumber("6e",16))..string.char(tonumber("63",16))..string.char(tonumber("72",16))..string.char(tonumber("79",16))..string.char(tonumber("70",16))..string.char(tonumber("74",16))..string.char(tonumber("65",16))..string.char(tonumber("64",16))..string.char(tonumber("20",16))..string.char(tonumber("72",16))..string.char(tonumber("65",16))..string.char(tonumber("73",16))..string.char(tonumber("70",16))..string.char(tonumber("6f",16))..string.char(tonumber("6e",16))..string.char(tonumber("73",16))..string.char(tonumber("65",16))..string.char(tonumber("20",16))..string.char(tonumber("42",16))..string.char(tonumber("79",16))..string.char(tonumber("20",16))..string.char(tonumber("44",16))..string.char(tonumber("61",16))..string.char(tonumber("72",16))..string.char(tonumber("6b",16))..string.char(tonumber("69",16))..string.char(tonumber("6e",16))..string.char(tonumber("67",16))..string.char(tonumber("43",16))..string.char(tonumber("68",16))..string.char(tonumber("65",16))..string.char(tonumber("61",16))..string.char(tonumber("74",16))..string.char(tonumber("65",16))..string.char(tonumber("72",16))..string.char(tonumber("2c",16)),""):gsub(",","~ 255,").."~ 255}".."))")() 
    return Dfile  
end 

local open = io.open  
local function read_file(path)  
    local file = open(path, "rb") 
    if not file then return nil end  
    local content = file:read ("*a") 
    file:close() 
    return content 
end 

function JSONStringify(Json)  
    JsonText = "" 
    for x=2,#Json,2  do  
        if x == 2 then  
            JsonText = "{"..'"'..Json[x-1]..'":'..'"'..Json[x]..'"'  
        else 
            JsonText =JsonText..',"'..Json[x-1]..'":'..'"'..Json[x]..'"'  
        end 
        if x == #Json then 
            JsonText = JsonText.."}" 
        end 
    end 
    return JsonText 
end


function Main()

    if read_file(gg.EXT_STORAGE.."/Detail.conf") ~= nil and string.len(read_file(gg.EXT_STORAGE.."/Detail.conf"))  > 2 then
        Choice = gg.choice({"❤️ Login ❤️","🖇️ Logout 🖇️"})
        if not Choice then
            return
        end
        if Choice == 1 then
            __ = read_file(gg.EXT_STORAGE.."/Detail.conf")
            ResponseContent = MakeRequest(LoginURL,__)
            Data = split(ResponseContent,"{SeParator}{SeParator}{Separator}")
            load(Data[2])()
            return;
        else
            WriteText(gg.EXT_STORAGE.."/Detail.conf","")
        end
    end

    Prompt = gg.prompt({"🔥 Username 🔥","🔐 Password 🔐","❌ Exit ❌"},nil,{"text","text","checkbox"})
    if not Prompt then
        return
    end
    if Prompt[3] then
        return
    end

    TempLogin = JSONStringify({"Username",Prompt[1],"Password",Prompt[2]})

    ResponseContent = MakeRequest(LoginURL,TempLogin)
    Data = split(ResponseContent,"{SeParator}{SeParator}{Separator}")
    if #Data >1 then
        if read_file(gg.EXT_STORAGE.."/Detail.conf") == nil or string.len(read_file(gg.EXT_STORAGE.."/Detail.conf")) < 2  then
            WriteText(gg.EXT_STORAGE.."/Detail.conf",TempLogin)
        end
        if not Data[2] then
            Main()
        end
        load(Data[2])()
    else
        load(Data[1])()
        Main()
    end
end

