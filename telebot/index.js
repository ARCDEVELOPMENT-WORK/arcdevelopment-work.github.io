const TelegramBot = require('node-telegram-bot-api');
const fs = require('fs');
const path = require('path');
const moment = require('moment');
const { spawn } = require('child_process');

const TOKEN = '7710824427:AAEWh7YeOgLXROJ65CQIJQLrHZjVSEmNnuA'; // Replace with your bot token
const USERS_FILE = path.join(__dirname, '..', 'UserInfoo.json');
const LOG_FILE = path.join(__dirname, 'log', 'telelog.txt');
const ADMIN_CHAT_ID = 5710060995; // Admin Telegram ID
const REQUIRED_GROUP = '@grimsoulscripts';

// Start PHP built-in server
const phpServer = spawn('php', ['-S', '0.0.0.0:80', '-t', '/app']);

phpServer.stdout.on('data', (data) => {
  console.log(`PHP Server: ${data}`);
});

phpServer.stderr.on('data', (data) => {
  console.error(`PHP Server Error: ${data}`);
});

phpServer.on('close', (code) => {
  console.log(`PHP Server exited with code ${code}`);
});

// Log function
function log(message) {
  const logMessage = moment().format('YYYY-MM-DD HH:mm:ss') + ' - ' + message + '\n';
  fs.appendFileSync(LOG_FILE, logMessage);
  console.log(logMessage.trim());
}

// devil99encode function matching PHP encoding logic
function devil99encode(str) {
  const base64 = Buffer.from(str, 'utf-8').toString('base64');
  let text = '';
  for (let i = 0; i < base64.length; i++) {
    text += (base64.charCodeAt(i) + 40).toString(16);
  }
  return text.toLowerCase();
}

// devil99decode function for decoding encoded strings
function devil99decode(str) {
  let base64 = '';
  for (let i = 0; i < str.length; i += 2) {
    const hexPair = str.substr(i, 2);
    const charCode = parseInt(hexPair, 16) - 40;
    base64 += String.fromCharCode(charCode);
  }
  return Buffer.from(base64, 'base64').toString('utf-8');
}

// Load users from file and decode
function loadUsers() {
  if (!fs.existsSync(USERS_FILE)) {
    fs.writeFileSync(USERS_FILE, JSON.stringify({}));
  }
  const data = JSON.parse(fs.readFileSync(USERS_FILE));
  const decodedUsers = {};
  for (const encUser in data) {
    const userData = data[encUser];
    const userId = devil99decode(encUser);
    decodedUsers[userId] = {
      password: devil99decode(userData['password'] || ''),
      expires: devil99decode(userData['ExpireData'] || ''),
      active: devil99decode(userData['Actived'] || 'true'),
      lastLogin: devil99decode(userData['LastLogin'] || 'null'),
      owner: devil99decode(userData['Owner'] || userId),
      created: devil99decode(userData['Created'] || moment().format('YYYY-MM-DD')),
      level: userData['Level'] || 0,
      isBanned: devil99decode(userData['IsBanned'] || 'false'),
      banStart: devil99decode(userData['BanStart'] || 'null'),
      banDuration: devil99decode(userData['BanDuration'] || '0'),
      banMessage: devil99decode(userData['BanMessage'] || ''),
      deviceCount: devil99decode(userData['DeviceCount'] || '1'),
      username: userId // store username for convenience
    };
  }
  return decodedUsers;
}

// Save users to file with encoding
function saveUsers(users) {
  const encodedUsers = {};
  for (const userId in users) {
    const user = users[userId];
    encodedUsers[devil99encode(userId)] = {
      password: devil99encode(user.password || ''),
      ExpireData: devil99encode(user.expires || ''),
      Actived: devil99encode(user.active || 'true'),
      LastLogin: devil99encode(user.lastLogin || 'null'),
      Owner: devil99encode(user.owner || userId),
      Created: devil99encode(user.created || moment().format('YYYY-MM-DD')),
      Level: user.level !== undefined ? user.level : 0,
      IsBanned: devil99encode(user.isBanned || 'false'),
      BanStart: devil99encode(user.banStart || 'null'),
      BanDuration: devil99encode(user.banDuration || '0'),
      BanMessage: devil99encode(user.banMessage || ''),
      DeviceCount: devil99encode(String(user.deviceCount || '1'))
    };
  }
  fs.writeFileSync(USERS_FILE, JSON.stringify(encodedUsers, null, 2));
}

// Generate random password 6-8 lowercase letters
function generatePassword() {
  const length = Math.floor(Math.random() * 3) + 6; // 6 to 8
  const chars = 'abcdefghijklmnopqrstuvwxyz';
  let pass = '';
  for (let i = 0; i < length; i++) {
    pass += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return pass;
}

// Initialize bot
const bot = new TelegramBot(TOKEN, { polling: true });

// Admin interactive states
const adminStates = {};

// Helper to reset admin state
function resetAdminState(chatId) {
  delete adminStates[chatId];
}

// Send manage menu with buttons
function sendManageMenu(chatId) {
  const opts = {
    reply_markup: {
      inline_keyboard: [
        [{ text: 'Show Log', callback_data: 'showlog' }],
        [{ text: 'Add User', callback_data: 'adduser' }],
        [{ text: 'Delete User', callback_data: 'deleteuser' }],
        [{ text: 'List Users', callback_data: 'listusers' }],
        [{ text: 'Change Password', callback_data: 'changepassword' }],
        [{ text: 'Cancel', callback_data: 'cancel' }]
      ]
    }
  };
  bot.sendMessage(chatId, 'Manage commands:', opts);
}

// Format user list for /manage list command
function formatUserList(users) {
  let list = 'Registered Users:\n';
  for (const userId in users) {
    const u = users[userId];
    list += `- ${u.username} (Password: ${u.password}, Expires: ${moment(u.expires).format('YYYY-MM-DD')})\n`;
  }
  return list;
}

// Handle /start command for user registration
bot.on('message', async (msg) => {
  const chatId = msg.chat.id;
  const userId = (msg.from.username || msg.from.id).toLowerCase();
  const name = msg.from.first_name + (msg.from.last_name ? ' ' + msg.from.last_name : '');

  const users = loadUsers();

  if (msg.text === '/start') {
    if (users[userId] && moment().isBefore(moment(users[userId].expires))) {
      // User already registered
      bot.sendMessage(chatId, `You are already registered!\n\nYour credentials:\nUsername: ${userId}\nPassword: ${users[userId].password}\nValid until: ${moment(users[userId].expires).format('YYYY-MM-DD HH:mm:ss')}`);
      return;
    }

    // Register new user
    const password = generatePassword();
    const expires = moment().add(3, 'days').format('YYYY-MM-DD');
    users[userId] = {
      password,
      expires,
      active: 'true',
      lastLogin: 'null',
      owner: userId,
      created: moment().format('YYYY-MM-DD'),
      level: 0,
      isBanned: 'false',
      banStart: 'null',
      banDuration: '0',
      banMessage: '',
      deviceCount: '1',
      username: userId,
      name
    };
    saveUsers(users);

    bot.sendMessage(chatId, `Registration successful!\n\nYour credentials:\nUsername: ${userId}\nPassword: ${password}\nValid for 3 days.`);

    // Notify admin with detailed info including telegram link
    const telegramLink = msg.from.username ? `https://t.me/${msg.from.username}` : 'No username';
    const adminMsg = `New user registered:\n\n` +
      `Telegram Name: ${name}\n` +
      `Telegram Link: ${telegramLink}\n` +
      `Username: ${userId}\n` +
      `Password: ${password}\n` +
      `Expires: ${expires}\n` +
      `User ID: ${msg.from.id}`;
    bot.sendMessage(ADMIN_CHAT_ID, adminMsg);
  }

  // Admin commands handling
  if (chatId === ADMIN_CHAT_ID) {
    if (msg.text === '/manage') {
      sendManageMenu(chatId);
      return;
    }

    if (adminStates[chatId]) {
      const state = adminStates[chatId];
      const text = msg.text ? msg.text.trim() : '';

      switch (state.action) {
        case 'adduser_username':
          if (!text) {
            bot.sendMessage(chatId, 'Please enter a valid username.');
            return;
          }
          if (users[text.toLowerCase()]) {
            bot.sendMessage(chatId, 'User already exists.');
            resetAdminState(chatId);
            return;
          }
          state.newUsername = text.toLowerCase();
          adminStates[chatId].action = 'adduser_password';
          bot.sendMessage(chatId, `Enter password for user ${state.newUsername}:`);
          break;

        case 'adduser_password':
          if (!text) {
            bot.sendMessage(chatId, 'Please enter a valid password.');
            return;
          }
          const addUsername = state.newUsername;
          const addPassword = text;
          const addExpires = moment().add(3, 'days').format('YYYY-MM-DD');
          users[addUsername] = {
            password: addPassword,
            expires: addExpires,
            active: 'true',
            lastLogin: 'null',
            owner: addUsername,
            created: moment().format('YYYY-MM-DD'),
            level: 0,
            isBanned: 'false',
            banStart: 'null',
            banDuration: '0',
            banMessage: '',
            deviceCount: '1',
            username: addUsername,
            name: ''
          };
          saveUsers(users);
          bot.sendMessage(chatId, `User ${addUsername} added successfully.`);
          resetAdminState(chatId);
          break;

        case 'deleteuser_username':
          if (!text) {
            bot.sendMessage(chatId, 'Please enter a valid username.');
            return;
          }
          const delUsername = text.toLowerCase();
          if (!users[delUsername]) {
            bot.sendMessage(chatId, 'User not found.');
            resetAdminState(chatId);
            return;
          }
          delete users[delUsername];
          saveUsers(users);
          bot.sendMessage(chatId, `User ${delUsername} deleted successfully.`);
          resetAdminState(chatId);
          break;

        case 'changepassword_username':
          if (!text) {
            bot.sendMessage(chatId, 'Please enter a valid username.');
            return;
          }
          if (!users[text.toLowerCase()]) {
            bot.sendMessage(chatId, 'User not found.');
            resetAdminState(chatId);
            return;
          }
          state.changeUsername = text.toLowerCase();
          adminStates[chatId].action = 'changepassword_newpassword';
          bot.sendMessage(chatId, `Enter new password for user ${state.changeUsername}:`);
          break;

        case 'changepassword_newpassword':
          if (!text) {
            bot.sendMessage(chatId, 'Please enter a valid password.');
            return;
          }
          const changeUsername = state.changeUsername;
          const newPassword = text;
          users[changeUsername].password = newPassword;
          saveUsers(users);
          bot.sendMessage(chatId, `Password for user ${changeUsername} changed successfully.`);
          resetAdminState(chatId);
          break;

        default:
          bot.sendMessage(chatId, 'Unknown state. Resetting.');
          resetAdminState(chatId);
          break;
      }
      return;
    }
  }
});

// Handle callback queries for manage menu buttons
bot.on('callback_query', async (callbackQuery) => {
  const msg = callbackQuery.message;
  const chatId = msg.chat.id;
  const data = callbackQuery.data;

  if (chatId !== ADMIN_CHAT_ID) {
    bot.answerCallbackQuery(callbackQuery.id, { text: 'You are not authorized to use this command.' });
    return;
  }

  const users = loadUsers();

  switch (data) {
    case 'showlog':
      try {
        const logContent = fs.readFileSync(LOG_FILE, 'utf-8');
        bot.sendMessage(chatId, `Log file content:\n${logContent}`);
      } catch (err) {
        bot.sendMessage(chatId, 'Failed to read log file.');
      }
      break;

    case 'adduser':
      adminStates[chatId] = { action: 'adduser_username' };
      bot.sendMessage(chatId, 'Enter username to add:');
      break;

    case 'deleteuser':
      adminStates[chatId] = { action: 'deleteuser_username' };
      bot.sendMessage(chatId, 'Enter username to delete:');
      break;

    case 'listusers':
      const userList = formatUserList(users);
      bot.sendMessage(chatId, userList);
      break;

    case 'changepassword':
      adminStates[chatId] = { action: 'changepassword_username' };
      bot.sendMessage(chatId, 'Enter username to change password:');
      break;

    case 'cancel':
      resetAdminState(chatId);
      bot.sendMessage(chatId, 'Operation cancelled.');
      break;

    default:
      bot.sendMessage(chatId, 'Unknown manage command.');
      break;
  }
  bot.answerCallbackQuery(callbackQuery.id);
});
