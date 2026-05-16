<?php declare(strict_types=1);
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile | Restaurant App</title>
  <!-- rest of your profile HTML -->
</head>
