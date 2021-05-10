<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php
session_set_cookie_params(0);
session_start();
?>

<?
if (isset($_SESSION["currentUser"])) {
    unset($_SESSION["currentUser"]);
}

?>
<h2>Login</h2>
<form autocomplete="off"  action="controller.php" method="post">
    <div class="container">
        <input type="text" name="loginUsername" placeholder='Username' required>
        <br>
        <input type="text" name="loginPassword" placeholder='Password' required>
        <br><br>
        <button class="button4" type="submit" value="Login">Login<br>
        <?php
        if( isset($_SESSION ['loginError']))
            echo $_SESSION ['loginError'];
        unset($_SESSION ['loginError']);
        ?>

    </div>
</form>