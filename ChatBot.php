<?php
session_start();
header('Content-Type: application/json');

try {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
    require 'ConnectChatbot.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the posted data
        $username = isset($_POST['username']) ? $_POST['username'] : $username;
        $question = $_POST['question'] ?? null;
        $answer = $_POST['answer'] ?? null;

        if ($question && $answer) {
            // SQL to insert question and answer into UserHistory table
            $insertSql = "INSERT INTO UserHistory (username, UserQuestions, UserAnswers) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("sss", $username, $question, $answer);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Data saved successfully.', 'username' => $username]);
            } else {
                throw new Exception('Error saving data: ' . $stmt->error);
            }
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Question or answer missing.', 'username' => $username]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request.', 'username' => $username]);
    }
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    echo json_encode(['error' => 'Server error occurred.', 'username' => isset($username) ? $username : 'Guest']);
}

?>
