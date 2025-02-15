<?php
// Define constants for the plugin details
define('MY_PLUGIN_TITLE', 'Online Poker Script');
define('MY_PLUGIN_SLUG', 'online-poker-script');
define('MY_PLUGIN_VERSION', '2.9.5');
define('LICENSE_SERVER_URL', 'https://www.onlinepokerscript.com');

// Function to get settings from the database
function get_setting($key, $default = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT Xvalue FROM " . DB_SETTINGS . " WHERE setting = :setting AND Xkey = :xkey");
        $stmt->execute(['setting' => 'licensekey', 'xkey' => strtoupper($key)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['Xvalue'] : $default;
    } catch (PDOException $e) {
        error_log('Error fetching setting: ' . $e->getMessage());
        echo 'Error fetching setting: ' . $e->getMessage();
        return $default;
    }
}

// Function to update settings in the database
function update_setting($key, $value) {
    global $pdo;
    try {
        // Update the setting value in the database
        $stmt = $pdo->prepare("UPDATE " . DB_SETTINGS . " SET Xvalue = :value WHERE setting = :setting AND Xkey = :xkey");
        $stmt->execute(['value' => $value, 'setting' => 'licensekey', 'xkey' => strtoupper($key)]);

        // If updating the activation status, update it with the correct value
        if ($key === 'activation') {
            $activation_stmt = $pdo->prepare("UPDATE " . DB_SETTINGS . " SET Xvalue = :activation_value WHERE setting = 'activation' AND Xkey = 'ACTIVATION'");
            $activation_stmt->execute(['activation_value' => $value]);
        }

        return $value;
    } catch (PDOException $e) {
        error_log('Error updating setting: ' . $e->getMessage());
        echo 'Error updating setting: ' . $e->getMessage();
        return false;
    }
}

// Function to handle license verification
function check_purchased_product($license_key) {
    $nonce = get_nonce_key(LICENSE_SERVER_URL . '/?nonce=1&license_key=' . $license_key);
    $url = LICENSE_SERVER_URL . '/wp-json/wlm/v1/license?license_key=' . $license_key . '&product_id=2569&nonce=' . $nonce;

    $response = file_get_contents($url);
    $http_response_header = $http_response_header ?? [];
    $body = json_decode($response);

    // Display the raw response and HTTP response code for debugging
    //echo '<pre>';
    //echo 'Request URL: ' . $url . "\n";
    //echo 'Response Headers: ' . print_r($http_response_header, true) . "\n";
    //echo 'Response Body: ' . $response . "\n";
    //echo '</pre>';

    if ($response === false) {
        error_log('Error: Could not connect to the license server.');
        echo 'Error: Could not connect to the license server.';
        return false;
    }

    if (!empty($license_key)) {
        if (isset($body->status) && $body->status === 'completed') {
            update_setting('licensekey', $license_key);
            update_setting('active_message', $body->message);
            update_setting('activation', 'active');
        
            // Refresh the page
            echo "<script type='text/javascript'>location.reload();</script>";
        
            return true;
        } else {
            handle_license_error($body->status, $body->message);
            return false;
        }
    } else {
        echo 'No license key found.';
        return false;
    }
}

// Function to get the nonce key from the server
function get_nonce_key($url) {
    $response = file_get_contents($url);
    if ($response === false) {
        echo 'Error fetching nonce key from URL: ' . $url;
    }
    return $response;
}

// Function to handle different license errors
function handle_license_error($status, $message) {
    echo 'License error occurred: Status: ' . $status . ' - Message: ' . $message . "<br>";
    switch ($status) {
        case 'not_found':
        case 'invalid_disabled':
        case 'nonce_invalid':
        case 'missing':
            update_setting('licensekey', '');
            update_setting('activation', 'inactive');
            echo 'License error: ' . $message . ': ' . $status;
            break;
        default:
            update_setting('licensekey', '');
            update_setting('activation', 'inactive');
            echo 'Unknown error occurred. Status: ' . $status . ' - Message: ' . $message;
    }
}

// Simple form to submit license key
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['license_key'])) {
    $license_key = htmlspecialchars($_POST['license_key']);
    update_setting('licensekey', $license_key);

    if (check_purchased_product($license_key)) {
        echo 'License activated successfully.';
    } else {
        echo 'License activation failed.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>License Settings</title>
</head>
<body>
    <h1>License Settings</h1>
    <form method="post">
        <label for="license_key">License Key:</label>
        <input type="text" id="license_key" name="license_key" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
<?php die; ?>