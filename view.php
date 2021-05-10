
<?php
session_set_cookie_params(0);
session_start();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <title>Quotation Service</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<h1 id="mainHeader">Quotation Service</h1>
<div id="buttonDiv">
    <?php
    if (!isset($_SESSION["currentUser"]))
        echo <<<EOT
            <form action="register.php" method="post">
                <button class="button4" id="register"> Register</button>
            </form><br>
            <form action="login.php" method="post">
                <button class="button4" id="login"> Login</button>
            </form>
            EOT;
    if (isset($_SESSION["currentUser"]))
        echo <<<EOT
            <form action="addQuotes.php" method="post">
                <button class="button4" id="addQuote">Add Quotes</button>
            </form><br>
             <form action="controller.php" method="post">
                <button class="button4" id="logout" name="logout">Logout</button>
            </form>
           EOT;
    ?>
</div>
<div id="quotes"></div>

<script>
    //Replacement for show quotes
    const quoteDiv = document.getElementById("quotes");
    window.addEventListener("load", () => {
        const form = new FormData();
        form.set("todo","getQuotes")
        fetch('controller.php', {method: 'POST', body: form})//
            .then(res => res.text())
            .then(res => quoteDiv.innerHTML=res)
            .catch(e => console.error('Error on page load' + e))
    });
</script>
</body>
</html>