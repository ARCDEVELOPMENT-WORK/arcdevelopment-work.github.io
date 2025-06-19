--[[ 
╔═══════════════════════════════════════════════════╗
║             💀 Grim Soul Free Script           ║
║                 📦 Version: 7.3.0                 ║
║             📅 Terms: TOS 01/07/2024              ║
║        📲 Contact: https://t.me/grimsoulscripts   ║
╚═══════════════════════════════════════════════════╝
--]]

-- 🔧 CONFIGURATION
local DEBUG_MODE       = false
local MAINTENANCE_MODE = false -- 🔴 Set to true to enable maintenance mode
local MAINTENANCE_MSG  = "🚧 Script is under maintenance.\n\n🛠️ We are currently updating our features.\nPlease try again later or join our Telegram for updates.\n\n📢 t.me/grimsoulscripts"
local REQUIRED_PACKAGE = "fantasy.survival.game.rpg"
local SCRIPT_URL       = "https://paid-gsscript-arc.onrender.com/scripts/V7.2.1.lua"
local VERSION          = "FREE"
local TOS_DATE         = "TOS 01/07/2024"

-- 🔍 DEBUG LOGGER
local function logDebug(msg)
    if DEBUG_MODE then print("🛠️ DEBUG: " .. msg) end
end

-- ✅ TOAST + ALERT HELPERS
local function showToast(msg)
    gg.toast("🔔 " .. msg)
end

local function showAlert(title, msg)
    gg.alert("📘 " .. title .. "\n\n" .. msg)
end

-- 🚧 MAINTENANCE CHECK
local function checkMaintenance()
    if MAINTENANCE_MODE then
        gg.alert("🚧 MAINTENANCE MODE ACTIVE 🚧", MAINTENANCE_MSG)
        os.exit()
    end
end

-- 🔐 VALIDATE GAME PROCESS
local function validateGameProcess()
    logDebug("Checking target package...")
    local currentPackage = gg.getTargetPackage()

    if currentPackage ~= REQUIRED_PACKAGE then
        showAlert("Process Mismatch", 
            "❌ Current App: " .. tostring(currentPackage) ..
            "\n✅ Required: " .. REQUIRED_PACKAGE ..
            "\n\nPlease select the correct game process.")
        os.exit()
    end
end

-- 👋 WELCOME MESSAGE
local function showWelcome()
    gg.alert(
        "🎮 Welcome to Grim Soul Script v" .. VERSION,
        "👑 Thank you for your trust and support!\n\n" ..
        "🛡️ Your script has been verified successfully.\n" ..
        "📌 Please avoid sharing or redistributing.\n\n" ..
        "📢 For support, updates, and new releases:\n🔗 t.me/grimsoulscripts\n\n" ..
        "💖 Stay strong, survivor!"
    )
end

-- 📄 CHANGELOG DISPLAY
local function showChangelog()
    showAlert("🚀 What's New in v" .. VERSION,
        "🔹 [NEW] Sleek User Interface\n" ..
        "🔹 [FIX] VMOS Crashing Resolved\n" ..
        "🔹 [ADD] v" .. VERSION .. " Exclusive Items\n" ..
        "🔹 [OPTIMIZED] Low-End Device Compatibility\n" ..
        "🔹 [BETA] 64-bit Process Support\n" ..
        "🔹 [CLEANUP] Removed Deprecated Items\n" ..
        "🔹 [INFO] Terms Updated: " .. TOS_DATE .. "\n\n" ..
        "📎 Join our growing community:\n🔗 t.me/grimsoulscripts"
    )
end

-- 🌐 LOAD REMOTE SCRIPT
local function loadRemoteScript()
    logDebug("Requesting remote script...")
    local response = gg.makeRequest(SCRIPT_URL)

    if not response or not response.content then
        showAlert("⚠️ Connection Error",
            "📴 Failed to fetch script from the server.\n" ..
            "Please ensure you're connected to the internet.")
        os.exit()
    else
        local success, result = pcall(load(response.content))
        if not success then
            showAlert("❌ Script Execution Error", tostring(result))
            os.exit()
        end
    end
end

-- 🚀 MAIN EXECUTION SEQUENCE

checkMaintenance()

showToast("🔍 Verifying game environment...")
validateGameProcess()

showToast("✅ Game process confirmed!")
showWelcome()
showChangelog()

showToast("⏳ Loading script. Please wait...")
loadRemoteScript()

print("✅ [Grim Soul Script v" .. VERSION .. "] Loaded Successfully.")
