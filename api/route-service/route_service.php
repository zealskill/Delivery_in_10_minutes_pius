<?php
// Включение логгирования ошибок
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/../../api/route_service_errors.log');

// Установка заголовков
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Определение путей к файлам
$baseDir = realpath(__DIR__.'/../../api');
$addressesFile = $baseDir.'/addresses.txt';
$logFile = $baseDir.'/log.txt';

// Проверка и создание файлов если не существуют
if (!file_exists($addressesFile)) {
    if (!file_put_contents($addressesFile, '') || !chmod($addressesFile, 0644)) {
        error_log("Failed to create addresses file");
        http_response_code(500);
        die(json_encode(['error' => 'Failed to initialize addresses file']));
    }
}

if (!file_exists($logFile)) {
    if (!file_put_contents($logFile, '') || !chmod($logFile, 0644)) {
        error_log("Failed to create log file");
    }
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'OPTIONS':
            // Preflight запрос для CORS
            http_response_code(204);
            break;

        case 'GET':
            handleGetRequest($addressesFile, $logFile);
            break;

        case 'POST':
            handlePostRequest($addressesFile, $logFile);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    error_log("Exception: ".$e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

function handleGetRequest($file, $logFile) {
    if (!is_readable($file)) {
        error_log("Addresses file not readable");
        http_response_code(500);
        echo json_encode(['error' => 'Could not read addresses file']);
        return;
    }

    $content = @file_get_contents($file);
    if ($content === false) {
        error_log("Failed to read addresses file");
        http_response_code(500);
        echo json_encode(['error' => 'Failed to read addresses']);
        return;
    }

    $coordinates = array_values(array_filter(
        array_map('trim', explode("\n", $content)),
        function($line) {
            return preg_match('/^-?\d+\.\d+\s*,\s*-?\d+\.\d+$/', $line);
        }
    ));

    // Логируем запрос координат
    $logMessage = sprintf(
        "[%s] GET request for coordinates. Returned %d coordinates\n",
        date('Y-m-d H:i:s'),
        count($coordinates)
    );
    file_put_contents($logFile, $logMessage, FILE_APPEND);

    echo json_encode($coordinates, JSON_UNESCAPED_UNICODE);
}

function handlePostRequest($addressesFile, $logFile) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Invalid JSON input");
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }

    if (isset($input['route_start']) && isset($input['route_end'])) {
        logRoute($input, $logFile);
    } elseif (isset($input['action']) && isset($input['coords'])) {
        handleAdminAction($input, $addressesFile, $logFile);
    } else {
        error_log("Invalid request parameters");
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request parameters']);
    }
}

function logRoute($data, $logFile) {
    $logMessage = sprintf(
        "[%s] Route from %s to %s\n",
        date('Y-m-d H:i:s'),
        $data['route_start'],
        $data['route_end']
    );

    if (!@file_put_contents($logFile, $logMessage, FILE_APPEND)) {
        error_log("Failed to write to log file");
    }

    echo json_encode(['status' => 'success']);
}

function handleAdminAction($input, $file, $logFile) {
    $coords = preg_replace('/\s+/', '', $input['coords']);

    if (!preg_match('/^-?\d+\.\d+,-?\d+\.\d+$/', $coords)) {
        error_log("Invalid coordinates format: ".$coords);
        http_response_code(400);
        echo json_encode(['error' => 'Invalid coordinates format. Use: lat,lng']);
        return;
    }

    $currentAddresses = is_readable($file)
        ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];

    switch ($input['action']) {
        case 'add':
            if (!in_array($coords, $currentAddresses)) {
                if (!file_put_contents($file, $coords.PHP_EOL, FILE_APPEND)) {
                    error_log("Failed to write to addresses file");
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to save coordinates']);
                    return;
                }

                // Логируем добавление координат
                $logMessage = sprintf(
                    "[%s] Added new coordinates: %s\n",
                    date('Y-m-d H:i:s'),
                    $coords
                );
                file_put_contents($logFile, $logMessage, FILE_APPEND);

                echo json_encode(['status' => 'added', 'coords' => $coords]);
            } else {
                echo json_encode(['status' => 'exists', 'coords' => $coords]);
            }
            break;

        case 'remove':
            $newAddresses = array_filter($currentAddresses, function($a) use ($coords) {
                return preg_replace('/\s+/', '', $a) !== $coords;
            });

            if (!file_put_contents($file, implode(PHP_EOL, $newAddresses))) {
                error_log("Failed to update addresses file");
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update coordinates']);
                return;
            }

            // Логируем удаление координат
            $logMessage = sprintf(
                "[%s] Removed coordinates: %s. Remaining: %d\n",
                date('Y-m-d H:i:s'),
                $coords,
                count($newAddresses)
            );
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            echo json_encode([
                'status' => 'removed',
                'remaining' => count($newAddresses)
            ]);
            break;

        default:
            error_log("Invalid action: ".$input['action']);
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action. Use: add or remove']);
            break;
    }
}