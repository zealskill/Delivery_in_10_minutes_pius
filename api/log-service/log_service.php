<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Путь к лог-файлу (в той же директории, где и скрипт)
$logFile = __DIR__ . '/../log.txt';

// Создаем файл, если не существует
if (!file_exists($logFile)) {
    if (!file_put_contents($logFile, '') || !chmod($logFile, 0644)) {
        http_response_code(500);
        die(json_encode(['error' => 'Failed to initialize log file']));
    }
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'OPTIONS':
            // Для CORS preflight запросов
            http_response_code(204);
            break;

        case 'GET':
            handleGetRequest($logFile);
            break;

        case 'POST':
            handlePostRequest($logFile);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

function handleGetRequest($logFile) {
    if (!is_readable($logFile)) {
        http_response_code(500);
        echo json_encode(['error' => 'Could not read log file']);
        return;
    }

    $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($logs === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to read logs']);
        return;
    }

    echo json_encode($logs, JSON_UNESCAPED_UNICODE);
}

function handlePostRequest($logFile) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }

    if (!isset($input['message']) || empty(trim($input['message']))) {
        http_response_code(400);
        echo json_encode(['error' => 'Message is required and cannot be empty']);
        return;
    }

    $logEntry = sprintf(
        "[%s] %s\n",
        date('Y-m-d H:i:s'),
        trim($input['message'])
    );

    if (file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to write to log']);
        return;
    }

    echo json_encode(['status' => 'success', 'message' => 'Log entry added']);
}