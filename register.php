
<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php 
  session_set_cookie_params(0);
  session_start();
?>

<h2>Register</h2>
<form autocomplete="off"  action="controller.php" method="post">
<div class="container">
<input type="text" name="registerUsername" placeholder='Username' required>
<br>
<input type="text" name="registerPassword" placeholder='Password' required>
<br><br>
    <button class="button4" type="submit" value="Register">Register</button> <br>
<?php 

if( isset($_SESSION ['registrationError']))
  echo $_SESSION ['registrationError']; 
unset($_SESSION ['registrationError']);
?>

</div>

</form>