<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

error_log("Received request for: " . $path); // Логирование

switch ($path) {
    case '/':
    case '/frontend.php':
        require __DIR__.'/frontend.php';
        break;

    case '/admin.php':
        require __DIR__.'/admin.php';
        break;

    case '/api/route-service':
        $url = 'http://localhost:8002/route-service/route.service.php';
        proxyRequest($url);
        break;

    case '/api/log-service':
        $url = 'http://localhost:8001/log-service/log.service.php';
        proxyRequest($url);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
}

function proxyRequest($targetUrl) {
    $method = $_SERVER['REQUEST_METHOD'];
    $headers = getallheaders();
    $content = file_get_contents('php://input');

    error_log("Proxying to: $targetUrl | Method: $method"); // Логирование

    $ch = curl_init($targetUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $content,
        CURLOPT_HTTPHEADER => array_map(
            fn($k, $v) => "$k: $v",
            array_keys($headers),
            $headers
        ),
        CURLOPT_HEADER => true,
    ]);

    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

    foreach (explode("\r\n", substr($response, 0, $headerSize)) as $header) {
        if (strpos($header, 'HTTP/') === 0) {
            header($header);
        } elseif (!empty($header)) {
            header($header);
        }
    }

    echo substr($response, $headerSize);
    curl_close($ch);
}