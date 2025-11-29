<?php
// check_mail.php - Upload this to your server to test mail configuration

header('Content-Type: text/html; charset=utf-8');

echo "<h1>PHP Mail Configuration Check</h1>";

// Check if mail function exists
echo "<h2>1. Mail Function Check</h2>";
if (function_exists('mail')) {
    echo "✓ mail() function is available<br>";
} else {
    echo "✗ mail() function is NOT available<br>";
}

// Check PHP mail configuration
echo "<h2>2. PHP Mail Settings</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";

$mail_settings = [
    'SMTP',
    'smtp_port',
    'sendmail_from',
    'sendmail_path'
];

foreach ($mail_settings as $setting) {
    $value = ini_get($setting);
    echo "<tr><td>$setting</td><td>" . ($value ? $value : '<em>Not set</em>') . "</td></tr>";
}
echo "</table>";

// Test sending email
echo "<h2>3. Test Email Send</h2>";
echo "<p>Enter your email address to receive a test email:</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    $test_email = filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL);
    
    if ($test_email) {
        $subject = "Test Email from PHP";
        $message = "This is a test email sent from your server at " . date('Y-m-d H:i:s');
        $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        
        echo "<p>Attempting to send test email to: <strong>$test_email</strong></p>";
        
        $result = @mail($test_email, $subject, $message, $headers);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Mail function returned TRUE - Email may have been sent</p>";
            echo "<p>Check your inbox (and spam folder) for the test email.</p>";
        } else {
            echo "<p style='color: red;'>✗ Mail function returned FALSE - Email was NOT sent</p>";
            $error = error_get_last();
            if ($error) {
                echo "<p>Error: " . htmlspecialchars(print_r($error, true)) . "</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>Invalid email address</p>";
    }
}

?>

<form method="POST">
    <input type="email" name="test_email" placeholder="your@email.com" required>
    <button type="submit">Send Test Email</button>
</form>

<h2>4. Server Information</h2>
<table border='1' cellpadding='5'>
    <tr><td>PHP Version</td><td><?php echo phpversion(); ?></td></tr>
    <tr><td>Server Software</td><td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td></tr>
    <tr><td>Server OS</td><td><?php echo PHP_OS; ?></td></tr>
</table>

<h2>5. Common Issues & Solutions</h2>
<ul>
    <li><strong>mail() returns FALSE:</strong> Your server doesn't have a mail server configured</li>
    <li><strong>Emails go to spam:</strong> Need proper SPF/DKIM records</li>
    <li><strong>No sendmail_path:</strong> Need to install and configure sendmail or postfix</li>
    <li><strong>Solution:</strong> Use PHPMailer with SMTP instead of mail()</li>
</ul>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
    h1 { color: #4CAF50; }
    h2 { color: #333; margin-top: 30px; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th { background-color: #4CAF50; color: white; padding: 8px; text-align: left; }
    td { padding: 8px; }
    form { margin: 20px 0; }
    input[type="email"] { padding: 8px; width: 300px; border: 1px solid #ddd; border-radius: 4px; }
    button { padding: 8px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background-color: #45a049; }
</style>