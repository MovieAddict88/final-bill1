<?php
$hash = '$2y$10$51/xhfZqmMt6pr9HhXfZB.Punql5srC5vXtOEradf0Cs5Dg/FzHYy';
if (password_verify('password', $hash)) {
    echo 'Password is valid!';
} else {
    echo 'Invalid password.';
}
?>