<?php
$my_pin = "admin1123"; // <-- CHANGE THIS TO YOUR ADMIN PIN
$hashed_pin = password_hash($my_pin, PASSWORD_DEFAULT);
echo "Your secure hashed PIN is: <br><br>";
echo $hashed_pin;
?>