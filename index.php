<?php

// Set OAuth2 parameters
$clientId = getenv('OAUTH2_CLIENT_ID') ?? 'd8d5624e-ebdd-4075-9fe1-1f3ff1563d09';
$clientSecret = getenv('OAUTH2_CLIENT_SECRET') ?? '1065ce83-ce5f-4a3d-9d84-ca507207c8ce';
$authorizationEndpoint = "https://sso.dev.ppmbg.id/web/";
$tokenEndpoint = "https://sso.dev.ppmbg.id/oauth/token";
$userInfoEndpoint = "https://sso.dev.ppmbg.id/api/userinfo";
$callbackPath = "/auth/callback";
$scope = "";

$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Simple routing based on the request path
switch ($requestUri) {
    case '/auth/login':
        if ($method === 'GET') {
            login();
        } else {
            response(405, "Method Not Allowed");
        }
        break;

    case '/auth/callback':
        if ($method === 'GET') {
            callback();
        } else {
            response(405, "Method Not Allowed");
        }
        break;

    case '/auth/status':
        if ($method === 'GET') {
            status();
        } else {
            response(405, "Method Not Allowed");
        }
        break;

    default:
        response(404, "Not Found");
        break;
}

// Function to initiate the login process
function login()
{
    global $authorizationEndpoint, $clientId, $scope;

    // Generate a state parameter for security (can be stored in cookie or used as is)
    $state = bin2hex(random_bytes(16));
    setcookie("oauth_state", $state, time() + 3600, "", "", true, true); // Secure HTTP-only cookie

    // Build the authorization URL
    $authUrl = $authorizationEndpoint . '?' . http_build_query([
        'client_id' => $clientId,
        'param_state' => $state,
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
    if (!isset($_GET['state']) || $_GET['state'] !== $_COOKIE['oauth_state']) {
        response(400, "Invalid state");
        return;
    }

    // Exchange authorization code for access token
    $code = $_GET['code'];
    $redirectUri = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/auth/callback";

    $tokenResponse = getAccessToken($tokenEndpoint, $clientId, $clientSecret, $code, $redirectUri);

    if (!isset($tokenResponse['access_token'])) {
        response(500, "Error fetching access token");
        return;
    }

    // Store the access token in a secure, HTTP-only cookie
    setcookie("access_token", $tokenResponse['access_token'], time() + 3600, "", "", true, true); // Secure HTTP-only cookie

    // Fetch user info with the access token (optional)
    $userInfo = getUserInfo($userInfoEndpoint, $tokenResponse['access_token']);

    // Redirect to the application home page or another route
    header('Location: /');
    exit();
}

// Function to get access token from the authorization server
function getAccessToken($url, $clientId, $clientSecret, $code, $redirectUri)
{
    $postData = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirectUri,
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
    if (isset($_COOKIE['access_token'])) {
        response(200, "User is authenticated");
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
