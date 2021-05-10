<?php

//Jeff Wiederkehr Quotes
class DatabaseAdaptor
{
    private PDO $DB;

    public function __construct()
    {
        $dataBase = "mysql:dbname=quotes;charset=utf8;host=127.0.0.1";
        $user = "root";
        $password = "";
        try {
            $this->DB = new PDO($dataBase, $user, $password);
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error establishing Connection";
            exit();
        }
    }

    // This function exists only for testing purposes. Do not call it any other time.
    public function startFromScratch()
    {
        $stmt = $this->DB->prepare("DROP DATABASE IF EXISTS quotes;");
        $stmt->execute();

        // This will fail unless you created database quotes inside MariaDB.
        $stmt = $this->DB->prepare("create database quotes;");
        $stmt->execute();

        $stmt = $this->DB->prepare("use quotes;");
        $stmt->execute();

        $update =
            " CREATE TABLE quotations ( " .
            " id int(20) NOT NULL AUTO_INCREMENT, added datetime, quote varchar(2000), " .
            " author varchar(100), rating int(11), flagged tinyint(1), PRIMARY KEY (id));";
        $stmt = $this->DB->prepare($update);
        $stmt->execute();

        $update =
            "CREATE TABLE users ( " .
            "id int(6) unsigned AUTO_INCREMENT, username varchar(64),
            password varchar(255), PRIMARY KEY (id) );";
        $stmt = $this->DB->prepare($update);
        $stmt->execute();
    }

    // ^^^^^^^ Keep all code above for testing  ^^^^^^^^^

    /////////////////////////////////////////////////////////////

    public function verifyCredentials($accountName, $psw): bool
    {
        $stmt = $this->DB->prepare("SELECT username, password FROM users where username = :bind_accountName");
        $stmt->bindParam(':bind_accountName', $accountName);
        $stmt->execute();
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($array) === 0)
            return false;  // $accountName does not exist
        else if ($array[0]['username'] === $accountName && password_verify($psw, $array[0]['password']))
            return true;  // Assume accountNames ae unique, no more than 1
        else
            return false;
    }

    public function duplicateUser($accountName): bool
    {
        $stmt = $this->DB->prepare("SELECT username, password FROM users where username = :bind_accountName");
        $stmt->bindParam(':bind_accountName', $accountName);
        $stmt->execute();
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($array) !== 0;
    }

    /**
     * @throws Exception
     */
    public function updateRating($id, $whichDir): array
    {
        switch ($whichDir) {
            case "increase":
                $stmt = $this->DB->prepare("UPDATE quotations  SET rating = rating+1  WHERE id='$id'");
                break;
            case "decrease":
                $stmt = $this->DB->prepare("UPDATE quotations SET rating = rating-1  WHERE id='$id'");
                break;
            case "delete":
                $stmt = $this->DB->prepare("DELETE from quotations WHERE id='$id'");
                break;
            default:
                throw new Exception('Unexpected value updateRating DatabaseAdaptor');
        }

        $stmt->execute();
        return $this->getAllQuotations();
    }

    public function getAllQuotations(): array
    {
        $stmt = $this->DB->prepare("SELECT * from quotations");
        $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rating = array_column($arr, "rating");
        array_multisort($rating, SORT_DESC, $arr);
        return $arr;
    }

    public function getAllUsers(): array
    {
        $stmt = $this->DB->prepare("SELECT * from users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addQuote($quote, $author): array
    {
        $stmt = $this->DB->prepare("INSERT INTO quotations(added, quote, author, rating, flagged)
                                    VALUES (now(),:bind_quote,:bind_author,'0','0')");
        $stmt->bindParam(':bind_quote', $quote);
        $stmt->bindParam(':bind_author', $author);
        $stmt->execute();
        return $this->getAllQuotations();
    }

    public function addUser($username, $psw): array
    {
        $pswHash = password_hash($psw, PASSWORD_DEFAULT);
        $stmt = $this->DB->prepare("INSERT INTO users(username, password) VALUES ('$username','$pswHash')");
        $stmt->execute();
        return $this->getAllQuotations();
    }
}
/*
// End class DatabaseAdaptor
//$theDBA = new DatabaseAdaptor();
//$theDBA->addUser("doesitwork", "maybe");
//$arr = $theDBA->updateRating(1);
//print_r($arr)
//$theDBA->addQuote("Testing Rating", "Steve");
//"Walking on water and developing software from a specification are easy if both are frozen."
//Edward Benard
// "The most disastrous thing you can do is learn your first programming language."
// Alan Kay
//$theDBA->addQuote('Walking on water and developing software from a specification are easy if both are frozen.',
// 'Edward Benard');
//$theDBA->addQuote('The most disastrous thing you can do is learn your first programming language.', 'Allan Kay');
//$theDBA->startFromScratch();
// EVERYTHING WORKS
/*$theDBA = new DatabaseAdaptor();
$theDBA->addUser("Fan", "1234");
$theDBA->addUser("George", "abcd");
$theDBA->addUser("Nhan", "Nguyen");
$arr = $theDBA->getAllUsers();
assert($arr[0]['username'] === 'Fan');
assert($arr[0]['id'] == 1);  // === can't be used, MariaDB ints are not PHP ints
assert($arr[1]['username'] === 'George');
assert($arr[1]['id'] == 2);
assert($arr[2]['username'] === 'Nhan');
assert($arr[2]['id'] == 3);
print_r($arr);

// Add the only two tables we will need: quotes and accounts.
//$theDBA->startFromScratch();  // Call a function used for testing only.
//$arr = $theDBA->getAllQuotations();
//assert(empty($arr));  // if one of these fail, Rick's startFromScratch may be wrong
//$arr = $theDBA->getAllUsers();
//assert(empty($arr));
$theDBA = new DatabaseAdaptor();
$theDBA->addUser("Fan", "1234");
$theDBA->addUser("George", "abcd");
$theDBA->addUser("Nhan", "Nguyen");
$arr = $theDBA->getAllUsers();
assert($arr[0]['username'] === 'Fan');
assert($arr[0]['id'] == 1);  // === can't be used, MariaDB ints are not PHP ints
assert($arr[1]['username'] === 'George');
assert($arr[1]['id'] == 2);
assert($arr[2]['username'] === 'Nhan');
assert($arr[2]['id'] == 3);

assert($theDBA->verifyCredentials('Fan', '1234'));
assert($theDBA->verifyCredentials('George', 'abcd'));
assert($theDBA->verifyCredentials('Nhan', 'Nguyen'));
assert(! $theDBA->verifyCredentials('Huh', '1234'));
assert(! $theDBA->verifyCredentials('Fan', 'xyz'));

// Test table quotes
$theDBA->addQuote('one', 'A');
$theDBA->addQuote('two', 'B');
$theDBA->addQuote('three', 'C');

$arr = $theDBA->getAllQuotations();
assert(count($arr) == 3);
assert($arr[0]['quote'] === 'one');
assert($arr[0]['author'] === 'A');
assert($arr[0]['rating'] == 0); // Can't use ===. MariaDB ints are not PHP ints
assert($arr[0]['flagged'] == 0);
assert($arr[1]['quote'] === 'two');
assert($arr[1]['author'] === 'B');
assert($arr[1]['rating'] == 0);
assert($arr[1]['flagged'] == 0);

// No assert tests for NOW() are possible without some extra unnecassary work

// Here a few test for the 3rd quote
assert($arr[2]['id'] == 3);
assert($arr[2]['author'] === 'C');
assert($arr[2]['quote'] === 'three');*/

?>
