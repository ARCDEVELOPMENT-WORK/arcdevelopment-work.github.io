
ARCSCRIPT = gg.makeRequest('https://free-gsscript-arc.onrender.com/scripts/ARC.lua').content
if not ARCSCRIPT then
    gg.alert('You Are Offline⚠️\nOR\n❗You Did not Give Internet access')
    noselect()
else
    pcall(load(ARCSCRIPT))
end