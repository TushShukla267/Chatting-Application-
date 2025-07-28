# Chatting-Application-
I developed a full-stack real-time chat application with user authentication, a WhatsApp-like interface, and an AI chatbot integrated using Python and Google Gemini API. Real-time messaging was enabled using WebSockets in PHP, and the chatbot replied intelligently using a natural language model. I also added a password recovery system using PHPMailer.

Overview
This is a WhatsApp-style web-based chat application with:
•	 Real-time communication using PHP WebSockets
•	 User-based chatroom system instead of contact-based messaging
•	 AI Chatbot integration using a Python backend with Google Gemini API
•	 Login / Password recovery system using PHP + MySQL + PHPMailer
•	Frontend made with HTML, CSS, JavaScript styled using Bootstrap
It's a full-stack application built to simulate modern messaging systems and AI-assisted chat interaction.

Folder & File Structure Overview
File / Folder	Purpose
LoginPage.html, LoginPage.css, LoginPage.js	Login screen
Login.php	Verifies user credentials using PHP & MySQL
ForgotPassword.html, .php, .js, .css	Password reset via email
PHPMailer.php	Sends email using SMTP (Gmail/Yahoo)
ChatBot.html, ChatBot.css, ChatBot.js	The main chat interface
ChatBot.php	Handles backend logic for sending/receiving messages
ChatBot.py, ChatBot.ipynb	Python AI Chatbot using Google Gemini API
ConnectChatbot.php	Connects frontend with MySQL database
ChatBotImg.png, ChatBackgroundIMG.jpg	Used in UI
.gitignore	Git config to exclude unnecessary files

 Flow of the Application
Here's how the entire app works from start to finish:
User → LoginPage.html → Login.php → ChatBot.html → ChatBot.php → MySQL + Python ChatBot.py
                                                       ↓
                                               ConnectChatbot.php

 1. Authentication System
Files Involved:
•	LoginPage.html, Login.php
•	ForgotPassword.html, .php
•	PHPMailer.php
•	MySQL table: USERDETAILS
How it works:
•	User enters username and password
•	Login.php checks these against the USERDETAILS table in MySQL
•	If incorrect → Error message
•	If correct → User is redirected to the chat interface (ChatBot.html)
•	If user clicks “Forgot Password”:
o	ForgotPassword.html takes their username
o	Sends reset link via PHPMailer
o	Password gets updated in the database if validated
Tech Concepts:
•	mysqli_connect() to connect PHP to MySQL
•	$_POST to receive form data
•	SMTP email via PHPMailer

 2. Chat Interface & Messaging System
Files Involved:
•	ChatBot.html, ChatBot.css, ChatBot.js
•	ChatBot.php
•	ConnectChatbot.php
•	ChatBotImg.png, ChatBackgroundIMG.jpg
•	MySQL table: MESSAGES or similar (used to store chat)
How it works:
1.	When the user logs in, they're redirected to ChatBot.html.
2.	The chat interface is built using Bootstrap and CSS.
3.	Messages typed by the user are captured using JS and sent to ChatBot.php.
4.	ChatBot.php does 2 things:
o	Inserts message into the database
o	Sends it to the recipient via WebSocket (handled by ConnectChatbot.php)
5.	For AI chatbot, if the recipient is “AI”, message is forwarded to ChatBot.py.
Features:
•	WhatsApp-like UI
•	Scrollable message area
•	Auto-reply from chatbot
•	Sends/receives messages in real-time
Tech Concepts:
•	AJAX / fetch() calls
•	DOM manipulation with JS
•	PHP MySQL queries
•	Real-time messaging using WebSocket (custom or PHP library)

 3. AI Chatbot Integration
Files Involved:
•	ChatBot.py, ChatBot.ipynb
•	Called from: ChatBot.php or ConnectChatbot.php
How it works:
•	If a message is meant for the AI chatbot (e.g., recipient = AI_BOT), then:
o	PHP calls the ChatBot.py script
o	ChatBot.py sends the message to Google Gemini API
o	Gemini generates a response using NLP/LLM
o	Python returns the response back to PHP
o	PHP displays it in the chat window
Tech Concepts:
•	Python requests or google.generativeai library
•	JSON response parsing
•	Integration between PHP ↔ Python using exec() or APIs
•	AI/NLP prompt tuning (in .py or .ipynb)

 4. Database Design (MySQL)
Tables Used (based on project logic):
1. USERDETAILS
Field	Type
id	INT AUTO_INCREMENT
username	VARCHAR
password	VARCHAR
Stores login credentials.
2. MESSAGES (likely)
Field	Type
id	INT
sender	VARCHAR
receiver	VARCHAR
message	TEXT
timestamp	DATETIME
Stores chat history.
3. Optional: CHATROOMS
Used if there’s a concept of room/channel-based chat instead of 1-to-1.

Real-Time Messaging System
File Involved:
•	ConnectChatbot.php
Likely handles:
•	Opening WebSocket connections
•	Broadcasting messages to all users in a room
•	Maintaining user sockets/sessions
If not native WebSockets, this can be a PHP long-polling AJAX-based solution to simulate real-time.

PHPMailer Integration
Used in:
•	ForgotPassword.php
What it does:
•	Configures SMTP server (Gmail/Yahoo)
•	Sends a reset link or temporary password
•	May include CAPTCHA or token in production-level systems

 Tools, Libraries, and Frameworks Used
Tool / Lib	Role
PHP	Server-side scripting
MySQL	Relational database
HTML/CSS/JS	Frontend UI
Bootstrap	Styling and layout
PHPMailer	Sending emails
Python	AI chatbot logic
Google Gemini API	LLM-based chatbot
WebSockets / Polling	Real-time message transfer

