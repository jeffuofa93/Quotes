<?php
require_once "./DatabaseAdaptor.php";

$controller = new Controller();
echo $controller->getReturnString();


class Controller
{

    private DatabaseAdaptor $theDBA;
    private string $returnString;

    public function __construct()
    {
        session_set_cookie_params(0);
        session_start();
        $this->theDBA = new DatabaseAdaptor();
        $this->sanitizeInput();
        $this->findCommand();
    }

    private function sanitizeInput()
    {
        foreach ($_POST as $key => $value)
            $_POST[$key] = htmlspecialchars($value);
    }

    private function findCommand()
    {
        if (!empty($_POST))
            switch ($_POST) {
                case isset($_POST["todo"]):
                    $this->firstPageLoad();
                    break;
                case isset($_POST["ID"]):
                    $this->updateRating();
                    break;
                case isset($_POST["quote"]) && isset($_POST["author"]):
                    $this->addQuotes();
                    break;
                case isset($_POST["registerUsername"]) && isset($_POST["registerPassword"]):
                    $this->register();
                    break;
                case isset($_POST["loginUsername"]) && isset($_POST["loginPassword"]):
                    $this->login();
                    break;
                case isset($_POST["logout"]):
                    $this->logout();
                    break;
            }
    }

    private function firstPageLoad()
    {
        $this->getQuotesAsHTML($this->theDBA->getAllQuotations());
        unset($_POST["todo"]);
    }

    private function getQuotesAsHTML($arr)
    {
        $result = "";
        $deleteButton = $this->deleteButtonCheck();
        foreach ($arr as $quote)
            $result .=
                <<<EOT
            <div class="container">
            {$quote['quote']}<br>
            <p class="author">
            &nbsp;&nbsp;--{$quote['author']}<br>
            </p>
            <form action="controller.php" method="post">
            <input type="hidden" name="ID" value={$quote['id']}>&nbsp;&nbsp;&nbsp;
            <button class="button4" name="update" value="increase">+</button>
            &nbsp;<span id="rating"> {$quote['rating']}</span>&nbsp;&nbsp;
            <button class="button4" name="update" value="decrease">-</button>&nbsp;&nbsp;
            $deleteButton
            </form>
            </div>
            EOT;
        // Assign completed string to class variable
        $this->returnString = $result;
    }

    private function deleteButtonCheck(): string
    {
        return isset($_SESSION["currentUser"]) ? "<button class='button4' name='update' value='delete'>Delete</button>" : "";
    }

    private function updateRating()
    {
        header("Location: view.php");
        $this->getQuotesAsHTML($this->theDBA->updateRating($_POST['ID'], $_POST['update']));
        unset($_POST['update']);
        unset($_POST['ID']);
    }

    private function addQuotes()
    {
        header("Location: view.php");
        $this->getQuotesAsHTML($this->theDBA->addQuote($_POST['quote'], $_POST['author']));
        unset($_POST['author']);
        unset($_POST['quote']);
    }

    private function register()
    {
        $user = $_POST['registerUsername'];
        $password = $_POST['registerPassword'];
        $duplicateCheck = $this->theDBA->duplicateUser($user);
        unset($_POST['registerUsername']);
        unset($_POST['registerPassword']);
        if ($duplicateCheck) {
            $_SESSION['registrationError'] = "<p id='errorMessage'>Account Name Taken</p>";
            header("Location: register.php");
        } else {
            $this->getQuotesAsHTML($this->theDBA->addUser($user, $password));
            header("Location: view.php");
        }
    }

    private function login()
    {
        $user = $_POST['loginUsername'];
        $password = $_POST['loginPassword'];
        $isValidLogin = $this->theDBA->verifyCredentials($user, $password);
        unset($_POST['loginUsername']);
        unset($_POST['loginPassword']);
        if (!$isValidLogin) {
            $_SESSION['loginError'] = "<p id='errorMessage'>Invalid Account/Password</p>";
            header("Location: login.php");
        } else {
            $_SESSION["currentUser"] = $user;
            $this->getQuotesAsHTML($this->theDBA->getAllQuotations());
            header("Location: view.php");
        }
    }

    private function logout()
    {
        unset($_SESSION["currentUser"]);
        $this->getQuotesAsHTML($this->theDBA->getAllQuotations());
        header("Location: view.php");
    }

    public function getReturnString(): string
    {
        return $this->returnString;
    }
}

?>
<!---->
