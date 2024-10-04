<?php

session_start();

$clientId = 'd8d5624e-ebdd-4075-9fe1-1f3ff1563d09';
$clientSecret = '1065ce83-ce5f-4a3d-9d84-ca507207c8ce';
$authorizationEndpoint = "https://sso.dev.ppmbg.id/web/signin";
$tokenEndpoint = "https://sso.dev.ppmbg.id/api/token";
$userInfoEndpoint = "https://sso.dev.ppmbg.id/api/userinfo";
$callbackPath = "/auth/callback";

$requestUri = $_SERVER['REQUEST_URI'];

switch (true) {
    case $requestUri === '/login':
        include 'public/';
        break;

    case $requestUri === '/auth/login':
        login();
        break;

    case preg_match('/\/auth\/callback.*/', $requestUri):
        callback();
        break;

    case $requestUri === '/auth/status':
        status();
        break;

    default:
        response(404, "Not Found");
        break;
}

function login()
{
    global $authorizationEndpoint, $clientId;

    $clientId = 'd8d5624e-ebdd-4075-9fe1-1f3ff1563d09';
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth_state'] = $state;

    $authUrl = $authorizationEndpoint . '?' . http_build_query([
        'client_id' => $clientId,
        'state' => $state,
    ]);

    header('Location: ' . $authUrl);
    exit();
}

function callback()
{
    global $clientId, $clientSecret, $tokenEndpoint, $userInfoEndpoint;
    $clientId = 'd8d5624e-ebdd-4075-9fe1-1f3ff1563d09';
    $clientSecret = '1065ce83-ce5f-4a3d-9d84-ca507207c8ce';
    $tokenEndpoint = "https://sso.dev.ppmbg.id/api/token";

    if ($_GET['state'] !== $_SESSION['oauth_state']) {
        response(400, "Invalid state");
        return;
    }

    $code = $_GET['code'];
    $state = $_GET['state'];

    $tokenResponse = getAccessToken($tokenEndpoint, $clientId, $clientSecret, $code, $state);

    if (!isset($tokenResponse['value']['access_token'])) {
        response(500, "Error fetching access token");
        return;
    }

    $userInfo = getUserInfo($userInfoEndpoint, $tokenResponse['value']['access_token']);

    $_SESSION['user'] = $userInfo;

    header('Location: /');
    exit();
}

function getAccessToken($url, $clientId, $clientSecret, $code, $state)
{
    $postData = [
        'state' => $state,
        'grant_type' => 'authorization_code',
        'authorization_code' => $code,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function getUserInfo($url, $accessToken)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function status()
{
    if (isset($_SESSION['user'])) {
        response(200, $_SESSION['user']);
    } else {
        response(401, "User not authenticated");
    }
}

function response($statusCode, $message)
{
    http_response_code($statusCode);
    echo json_encode(['status' => $statusCode, 'message' => $message]);
    exit();
}
