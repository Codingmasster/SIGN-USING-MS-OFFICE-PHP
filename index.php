<?php
echo '
<script>
    url = window.location.href;
    i = url.indexOf("#");
    if (i > 0) {
        url = url.replace("#", "?");
        window.location.href = url;
    }
</script>
';

session_start();

if (array_key_exists('access_token', $_GET)) {

    $_SESSION['t'] = $_GET['access_token'];
    $t = $_SESSION['t'];

    $ch = curl_init();

    // Request user information
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $t,
        'Content-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $userInfo = json_decode(curl_exec($ch), true);

    // Check for errors in the user information response
    if (array_key_exists('error', $userInfo)) {
        var_dump($userInfo['error']);
        die();
    }

    // Request user profile picture
    $size = '48x48';
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $t,
    ));
    curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me/photo/$value");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $profilePic = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode == 200) {
        $_SESSION['profile_pic'] = base64_encode($profilePic);
    } else {
        $_SESSION['profile_pic'] = null;
        echo "Failed to retrieve profile picture. HTTP Code: " . $httpCode;
    }

    curl_close($ch);

    // Store user information in session
    $_SESSION['msatg'] = 1;  // auth and verified
    $_SESSION['whole_response'] = $userInfo;
    header('Location: http://localhost:3000/index.php');
    exit();
}

// Retrieve the API response from the session
$apiResponse = $_SESSION["whole_response"];
$profilePic = $_SESSION["profile_pic"];
print_r("Profile pic". $profilePic);
print_r($_SESSION);
echo '<pre>';
print_r($apiResponse); // Check user info
echo '</pre>';

if ($profilePic) {
    echo '<img src="data:image/jpeg;base64,' . $profilePic . '" alt="Profile Picture" />';
} else {
    echo 'No profile picture available.';
}

// Database connection
$servername = "";
$username = ""; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (id, displayName, givenName, jobTitle, mail, mobilePhone, officeLocation, preferredLanguage, surname, userPrincipalName, profilePicture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssss", $id, $displayName, $givenName, $jobTitle, $mail, $mobilePhone, $officeLocation, $preferredLanguage, $surname, $userPrincipalName, $profilePicture);

// Set parameters and execute
$id = $apiResponse['id'];
$displayName = $apiResponse['displayName'];
$givenName = $apiResponse['givenName'];
$jobTitle = $apiResponse['jobTitle'];

$mobilePhone = $apiResponse['mobilePhone'];
$officeLocation = $apiResponse['officeLocation'];
$preferredLanguage = $apiResponse['preferredLanguage'];
$surname = $apiResponse['surname'];
$mail = $apiResponse['mail'];
$userPrincipalName = $apiResponse['userPrincipalName'];
$email = "";
// Check if mail is null or empty
if (empty($mail)) {
    // If mail is null, extract the email from userPrincipalName
    preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $userPrincipalName, $matches);
    $email = $matches[0] ?? ''; // The first match will be the email
} else {
    // If mail is not null, use the mail value
    $email = $mail;
}

$profilePicture = $profilePic;

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
