<?php
require_once 'user.php';

User::checkLoggedIn();
User::logoutUser();
header("Location: login.php");

?>
