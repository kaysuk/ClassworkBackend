<?php
require_once __DIR__ . '/../scripts/db.php';
require_once __DIR__ . '/../../Ex3/auth.php';

function auth($request, $route) {
    // Проверка HTTP Basic Auth.
    $credentials = getAdminHttpAuth();

    if (!$credentials) {
        return array(
            'headers' => array(
                'WWW-Authenticate: Basic realm="Admin Area"',
                'HTTP/1.0 401 Unauthorized'
            ),
            'entity' => 'Требуется авторизация для доступа к административной части.'
        );
    }

    $result = loginAdmin($credentials['login'], $credentials['password']);
    if (!$result['success']) {
        return array(
            'headers' => array(
                'WWW-Authenticate: Basic realm="Admin Area"',
                'HTTP/1.0 401 Unauthorized'
            ),
            'entity' => 'Неверные учетные данные администратора.'
        );
    }

    return false;
}
