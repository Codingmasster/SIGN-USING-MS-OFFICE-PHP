<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$appid = "75efda2b-99f0-4413-ae63-8f82d54ff54e";
$tennantid = "d141f270-6d61-46e7-8103-2fd44000a90a";
$secret = "4f4c4852-7516-4129-9b98-89f7d6024170";
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
