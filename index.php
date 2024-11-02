<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EchoChat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="chat-style.css">
</head>
<body class="container mt-5">
<?php
session_start();
$sessionUsername = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
    <div class="row justify-content-center">
        <div class="col-md-6 chat-container">
            <h3 class="text-center">EchoChat Chat Room</h3>
            <div id="login-box" class="login-box">
                <div class="mb-3">
                    <label for="username" class="form-label">Enter Username:</label>
                    <input type="text" id="username" class="form-control" required>
                </div>
                <button onclick="login()" class="btn btn-primary">Join Chat</button>
                <a href="ChatBot.html">
              <button class="chatBtns">
                <h3 class="ChatBot">Echo Bot</h3>
              </button>
            </a>
            </div>
            <div id="chat-room" class="chat-room" style="display: none;">
                <div id="chat-box" class="chat-box"></div>
                <form id="chat-form" onsubmit="sendMessage(); return false;" class="chat-form">
                    <input type="text" id="message" class="form-control message-input" placeholder="Type your message...">
                    <button type="button" onclick="sendMessage()" class="btn send-btn">Send</button>
                </form>
                <button onclick="exitChat()" class="btn exit-btn">Exit Chat</button>
            </div>
        </div>
    </div>
    <script>
        let ws;
        let username = "<?php echo $sessionUsername; ?>";
        window.onload = () => {
            if (username) {
                document.getElementById('username').value = username;
            }
        };
        
        function login() {
            username = document.getElementById('username').value;
            if (!username) {
                alert('Please enter a username');
                return;
            }
            ws = new WebSocket('ws://localhost:8080');
            ws.onopen = () => {
                document.getElementById('login-box').style.display = 'none';
                document.getElementById('chat-room').style.display = 'block';
                ws.send(JSON.stringify({ type: 'join', user: username }));
            };
            ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                const chatBox = document.getElementById('chat-box');
                if (data.type === 'message') {
                    const alignClass = data.user === username ? 'sent' : 'received';
                    chatBox.innerHTML += `<div class="message ${alignClass}"><strong>${data.user}:</strong> ${data.message}</div>`;
                } else if (data.type === 'join') {
                    chatBox.innerHTML += `<div class="message received"><em>${data.user} joined the chat</em></div>`;
                } else if (data.type === 'leave') {
                    chatBox.innerHTML += `<div class="message received"><em>${data.user} left the chat</em></div>`;
                }
                chatBox.scrollTop = chatBox.scrollHeight;
            };
            ws.onclose = () => {
                document.getElementById('chat-box').innerHTML += `<div class="message received"><strong>You have left the chat</strong></div>`;
            };
        }

        function sendMessage() {
            const message = document.getElementById('message').value;
            if (message && ws) {
                ws.send(JSON.stringify({ type: 'message', user: username, message: message }));
                document.getElementById('message').value = '';
            }
        }

        function exitChat() {
            if (ws) {
                ws.send(JSON.stringify({ type: 'leave', user: username }));
                ws.close();
                document.getElementById('chat-room').style.display = 'none';
                document.getElementById('login-box').style.display = 'block';
            }
        }
    </script>
</body>
</html>
