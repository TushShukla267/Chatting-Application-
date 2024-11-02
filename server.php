<?php
// server.php: Simple PHP WebSocket Server with Join, Leave, and Message Broadcast

$host = '127.0.0.1';
$port = 8080;
$clients = [];

// Create a WebSocket server
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($server, $host, $port);
socket_listen($server);

echo "WebSocket server started at ws://$host:$port\n";

while (true) {
    $changed = array_merge([$server], $clients);
    $write = [];
    $except = [];
    socket_select($changed, $write, $except, 0, 10);

    // Handle new connections
    if (in_array($server, $changed)) {
        $clientSocket = socket_accept($server);
        $clients[] = $clientSocket;
        array_splice($changed, array_search($server, $changed), 1);
    }

    // Handle incoming messages from clients
    foreach ($changed as $clientSocket) {
        $buffer = socket_read($clientSocket, 1024, PHP_BINARY_READ);

        if ($buffer === false) {
            // Client disconnected unexpectedly
            unset($clients[array_search($clientSocket, $clients)]);
            socket_close($clientSocket);
            continue;
        }

        // Perform WebSocket handshake if needed
        if (strpos($buffer, "Sec-WebSocket-Key") !== false) {
            performWebSocketHandshake($buffer, $clientSocket);
            continue;
        }

        // Decode the received message and handle it based on type
        $message = json_decode(unmask($buffer), true);
        if ($message) {
            if ($message['type'] === 'join') {
                broadcast(json_encode(['type' => 'join', 'user' => $message['user']]));
            } elseif ($message['type'] === 'message') {
                broadcast(json_encode(['type' => 'message', 'user' => $message['user'], 'message' => $message['message']]));
            } elseif ($message['type'] === 'leave') {
                // Broadcast the leave message to all clients except the one who is leaving
                broadcast(json_encode(['type' => 'leave', 'user' => $message['user']]), $clientSocket);
                
                // Remove the client from the list and close the connection
                unset($clients[array_search($clientSocket, $clients)]);
                socket_close($clientSocket);
            }
        }
    }
}

// Helper function to perform WebSocket handshake
function performWebSocketHandshake($headers, $clientSocket) {
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $matches)) {
        $key = base64_encode(pack('H*', sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $headers = "HTTP/1.1 101 Switching Protocols\r\n";
        $headers .= "Upgrade: websocket\r\n";
        $headers .= "Connection: Upgrade\r\n";
        $headers .= "Sec-WebSocket-Accept: $key\r\n\r\n";
        socket_write($clientSocket, $headers, strlen($headers));
    }
}

// Helper function to broadcast a message to all connected clients except one
function broadcast($msg, $excludeClient = null) {
    global $clients;
    foreach ($clients as $client) {
        if ($client !== $excludeClient) {
            @socket_write($client, mask($msg), strlen(mask($msg)));
        }
    }
}

// Unmask the received data from the client with error handling
function unmask($payload) {
    if (strlen($payload) < 2) {
        return '';
    }
    $length = ord($payload[1]) & 127;
    if ($length == 126 && strlen($payload) >= 8) {
        $masks = substr($payload, 4, 4);
        $data = substr($payload, 8);
    } elseif ($length == 127 && strlen($payload) >= 14) {
        $masks = substr($payload, 10, 4);
        $data = substr($payload, 14);
    } elseif (strlen($payload) >= 6) {
        $masks = substr($payload, 2, 4);
        $data = substr($payload, 6);
    } else {
        return '';
    }
    $text = '';
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i % 4];
    }
    return $text;
}

// Mask data before sending it to the client
function mask($text) {
    $b1 = 0x81;
    $length = strlen($text);

    if ($length <= 125) {
        $header = pack('CC', $b1, $length);
    } elseif ($length <= 65535) {
        $header = pack('CCn', $b1, 126, $length);
    } else {
        $header = pack('CCNN', $b1, 127, $length);
    }
    return $header . $text;
}
