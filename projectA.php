<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$user_mail = $user_phone = $user_name = $user_surname = $user_birthdate = "";
$phone_error = $mail_error = $name_error = $surname_error = $birthdate_error = "";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registratiom_form";

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
};

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validationsFlag = false;

    if (empty($_POST["name"])) {
        $name_error = "* Παρακαλώ συμπληρώστε το όνομα χρήστη";
        $validationsFlag = false;
    } else {
        $user_name = test_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Zα-ωΑ-Ω]/", $user_name)) {
            $name_error = "Το όνομα χρήστη δεν είναι έγκυρο";
        } else {
            $validationsFlag = true;
        }
    }
    if (empty($_POST["surname"])) {
        $surname_error = "* Παρακαλώ συμπληρώστε το επώνυμο χρήστη";
        $validationsFlag = false;
    } else {
        $user_surname = test_input($_POST["surname"]);
        if (!preg_match("/^[a-zA-Zα-ωΑ-Ω]/", $user_surname)) {
            $surname_error = "Το επώνυμο χρήστη δεν είναι έγκυρο";
        } else {
            $validationsFlag = true;
        }
    }
    if (empty($_POST["telephone"])) {
        $phone_error = "* Παρακαλώ συμπληρώστε το τηλέφωνο επικοινωνίας";
        $validationsFlag = false;
    } else {
        $user_phone = test_input($_POST["telephone"]);
        if (!preg_match("/^\\+?[1-9][0-9]{7,14}$/", $user_phone)) {
            $phone_error = "Το τηλέφωνο επικοινωνίας δεν είναι έγκυρο";
        } else {
            $validationsFlag = true;
        }
    }
    if (empty($_POST["mail"])) {
        $mail_error = "* Παρακαλώ συμπληρώστε το email χρήστη";
        $validationsFlag = false;
    } else {
        $user_mail = test_input($_POST["mail"]);
        if (!filter_var($user_mail, FILTER_VALIDATE_EMAIL)) {
            $mail_error = "* Το email δεν ειναι έγκυρο";
        } else {
            $validationsFlag = true;
        }
    }
    if (empty($_POST["birthdate"])) {
        $birthdate_error = "* Παρακαλώ συμπληρώστε την ημερομηνία γέννησης";
        $validationsFlag = false;
    } else {
        $user_birthdate = test_input($_POST["birthdate"]);
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $user_birthdate)) {
            $birthdate_error = "* Παρακαλώ συμπληρώστε την σωστή ημερομηνία γέννησης";
        } else {
            $validationsFlag = true;
        }
    }

    if ($validationsFlag) {

        //Edw request gia email
        $sqlSelect_mail = "SELECT email FROM users WHERE email = '" . $user_mail . "'";
        //edw request gia phone
        $sqlSelect_phone = "SELECT phone FROM users WHERE phone = '" . $user_phone . "'";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $resultEmail = $conn->query($sqlSelect_mail);
        $resultPhone = $conn->query($sqlSelect_phone);
        $emailRowCount = mysqli_num_rows($resultEmail);
        $phoneRowCount = mysqli_num_rows($resultPhone);



        $isDuplicate = false;
        if ($emailRowCount > 0 && $phoneRowCount > 0) {
            $isDuplicate = true;
            $phone_error = "* Το τηλέφωνο που καταχωρήσατε υπάρχει ήδη";
            $mail_error = "* Το email που καταχωρήσατε υπάρχει ήδη";
        } else if ($phoneRowCount > 0) {
            $isDuplicate = true;
            $phone_error = "* Το τηλέφωνο που καταχωρήσατε υπάρχει ήδη";
        } else if ($emailRowCount > 0) {
            $isDuplicate = true;
            $mail_error = "* Το email που καταχωρήσατε υπάρχει ήδη";
        }


        if (!$isDuplicate) {
            $sql = "INSERT INTO users (firstname, lastname, phone, email, birth_date)
            VALUES ('" . $user_name . "', '" . $user_surname . "', '" . $user_phone . "', '" . $user_mail . "', '" . $user_birthdate . "')";

            if ($conn->query($sql) === TRUE) {
                header("Location: succesful_submit.php");
                $conn->close();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="projectA.css">
    <title>Form Validation</title>
</head>

<body>
    <div id="validation_form">
        <form method="post" action="" id="registration_form" novalidate>
            <div id="headline">
                <h1>Φόρμα <span>Εγγραφής</span></h1>
            </div>
            <div class="form name">
                <label for="name"> Όνομα</label>
                <input type="text" placeholder="Όνομα" name="name" id="name" value="<?= $user_name; ?>">
                <div style="color: red; font-size: 10px;"> <?= $name_error; ?></div>
            </div>
            <div class="form surname">
                <label for="surname">Επίθετο</label>
                <input type="text" placeholder="Επίθετο" name="surname" id="surname" value="<?= $user_surname; ?>">
                <div style="color: red; font-size: 10px;"> <?= $surname_error; ?> </div>
            </div>
            <div class="form phone">
                <label for="telepnone">Τηλέφωνο Επικοινωνίας</label>
                <input type="tel" placeholder="Τηλέφωνο" name="telephone" id="telephone" value="<?= $user_phone; ?>">
                <div style="color: red; font-size: 10px;"> <?= $phone_error; ?> </div>
            </div>
            <div class="form mail">
                <label for="mail">Διεύθυνση Ηλεκτρονικού Ταχυδρομείου</label>
                <input type="email" placeholder="Email" name="mail" id="mail" value="<?= $user_mail; ?>">
                <div style="color: red; font-size: 10px;"> <?= $mail_error; ?> </div>
            </div>
            <div class="form date">
                <label for="birthdate">Ημερομηνία Γέννησης</label>
                <input type="date" name="birthdate" id="birthdate" value="<?= $user_birthdate; ?>">
                <div style="color: red; font-size: 10px;"> <?= $birthdate_error; ?> </div>
            </div>
            <div class="form submition">
                <input type="submit" value="Υποβολή" id="submit">
            </div>
        </form>
    </div>

</body>

</html>