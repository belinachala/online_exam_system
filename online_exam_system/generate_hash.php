<?php
$password = 'lamo1234'; // Plain password
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
echo "Hashed Password: " . $hashed_password; // Print the hashed password
?>
