<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$appid = "YOUR_APP_ID";
$tennantid = "YOUR_TENNANT_ID";
$secret = "YOUR_CLIENT_SECRET";
$login_url = "https://login.microsoftonline.com/".$tennantid."/oauth2/v2.0/authorize";

session_start();
$_SESSION['state'] = session_id();

if (isset($_GET['action']) && $_GET['action'] == 'login') {
    $params = array(
        'client_id' => $appid,
        'redirect_uri' => 'http://localhost:3000/index.php',
        'response_type' => 'token',
        'scope' => 'https://graph.microsoft.com/User.Read',
        'state' => $_SESSION['state']
    );
    header('Location: '.$login_url.'?'.http_build_query($params));
    exit();
}
?>
