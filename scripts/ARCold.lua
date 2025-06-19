version = "FREE"
tos = "TOS 01/07/2024"

--------------PROCESS VALIDATION AND RUN-------------
Process = gg.getTargetPackage()
if Process == "fantasy.survival.game.rpg" then
    gg.alert("❤️‍🔥 GRIM SOUL FREE SCRIPT ❤️‍🔥\n\nTo Upgrade and Renew your Script.\nCONTACT:- https://t.me/grimsoulscripts ")
else
    gg.alert("❌Failed to Verify The Active Process. ❕PLEASE CHOOSE THE CORRECT PROCESS.❕")
    os.exit()
end

gg.alert("🎉Grim Soul V"..version.." Updated Script🎉\n\n✅️[ADDED] Friendly UI\n✅️[FIXED] VM Freezing\n✅️[ADDED] V"..version.. " Items\n✅️[ADDED] Low-End Device Support\n✅️[BETA] 64bit Support (beta)\n✅️[ADDED] " ..tos.."\n\nJOIN TELEGRAM FOR MORE INFO: t.me/grimsoulscripts")

print("🛠 MADE and Encrypted By Grim Soul Scripts🛠")


ARCSCRIPT = gg.makeRequest('https://free-gsscript-arc.onrender.com/scripts/FREE.lua').content
if not ARCSCRIPT then
    gg.alert('You Are Offline⚠️\nOR\n❗You Did not Give Internet access')
    noselect()
else
    pcall(load(ARCSCRIPT))
end
------------------------------------------ END -----------------------------------------
