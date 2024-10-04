<?php

session_start();

// Set OAuth2 parameters
$clientId = getenv('OAUTH2_CLIENT_ID') ?? 'd8d5624e-ebdd-4075-9fe1-1f3ff1563d09';
$clientSecret = getenv('OAUTH2_CLIENT_SECRET') ?? '1065ce83-ce5f-4a3d-9d84-ca507207c8ce';
$authorizationEndpoint = "https://sso.dev.ppmbg.id/web/signin";
$tokenEndpoint = "https://sso.dev.ppmbg.id/api/token";
$userInfoEndpoint = "https://sso.dev.ppmbg.id/api/userinfo";
$callbackPath = "/auth/callback";

$requestUri = $_SERVER['REQUEST_URI'];

switch ($requestUri) {
    case '/auth/login':
        login();
        break;

    case '/auth/callback':
        callback();
        break;

    case '/auth/status':
        status();
        break;

    default:
        response(404, "Not Found");
        break;
}

// Function to initiate the login process
function login()
{
    global $authorizationEndpoint, $clientId;

    // Generate a state parameter for security (can be stored in session)
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth_state'] = $state;

    // Build the authorization URL
    $authUrl = $authorizationEndpoint . '?' . http_build_query([
        'client_id' => $clientId,
        'state' => $state,
    ]);

    // Redirect to OAuth2 provider
    header('Location: ' . $authUrl);
    exit();
}

// Function to handle the OAuth2 callback
function callback()
{
    global $clientId, $clientSecret, $tokenEndpoint, $userInfoEndpoint;

    // Validate the state parameter
    if ($_GET['state'] !== $_SESSION['oauth_state']) {
        response(400, "Invalid state");
        return;
    }

    // Exchange authorization code for access token
    $code = $_GET['code'];
    $state = $_GET['state'];

    $tokenResponse = getAccessToken($tokenEndpoint, $clientId, $clientSecret, $code, $state);

    if (!isset($tokenResponse['access_token'])) {
        response(500, "Error fetching access token");
        return;
    }

    // Fetch user info with the access token
    $userInfo = getUserInfo($userInfoEndpoint, $tokenResponse['access_token']);

    // Save user info in session (or handle as needed)
    $_SESSION['user'] = $userInfo;

    // Redirect to the application home page or another route
    header('Location: /');
    exit();
}

// Function to get access token from the authorization server
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

// Function to fetch user information from the user info endpoint
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

// Function to check the authentication status of the user
function status()
{
    if (isset($_SESSION['user'])) {
        response(200, $_SESSION['user']);
    } else {
        response(401, "User not authenticated");
    }
}

// Utility function to send JSON responses
function response($statusCode, $message)
{
    http_response_code($statusCode);
    echo json_encode(['status' => $statusCode, 'message' => $message]);
    exit();
}
