<?php
$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];

// Tablica przechowujaca błedy
$register_errors = [
    "name"=>["message"=>"", "class"=>""],
    "surname"=>["message"=>"", "class"=>""],
    "login"=>["message"=>"", "class"=>""],
    "email"=>["message"=>"", "class"=>""],
    "password"=>["message"=>"", "class"=>""],
    "phone"=>["message"=>"", "class"=>""],
    "unexpected"=>["message"=>"", "class"=>""],
];
$isValid = [];

$name = $_POST['name'] ?? '';
$surname = $_POST['surname'] ?? '';
$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '+48';



// czy kliknieto przycisk zarejestruj się
if(isset($_POST['submit_form'])){
    require_once('@php/controler/validate.php');
    require_once('@php/controler/miscellaneous.php');

    $register_errors["name"]["message"] = validate_name($name);
    $register_errors["surname"]["message"] = validate_surname($surname);
    $register_errors["login"]["message"] = validate_login($login) ? validate_login($login) : validate_if_login_exist($DB,$login);
    $register_errors["email"]["message"] = validate_email($email) ? validate_email($email) : validate_if_email_exist($DB, $email);
    $register_errors["password"]["message"] = validate_passwords($password,$password2);
    $register_errors["phone"]["message"] = validate_phone($phone);

    foreach ($register_errors as $key => $error) {
        $register_errors[$key]["class"]  = $error["message"] != "" ?  "is-invalid" : "is-valid";
    }
    
    
    
    // jezeli nie było błedow podczas rejestracji
    $check_errors = array_filter($register_errors, function ($error) {
        return $error["message"] != "";
    });
    if(empty($check_errors)){

        // patrz dokumentacaj https://www.php.net/manual/en/function.password-hash.php
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $DB->prepare("INSERT INTO `users` (`name`, `surname`, `email`, `number`, `login`, `password`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss",$name, $surname, $email, $phone, $login, $hashed_password);
        
        if($stmt->execute()){
            try {
                $message = <<<END
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Document</title>
                </head>
                <body>
                <h1>Drogi Kliencie,</h1>
                <p>Dziękujemy za zarejestrowanie się na naszej stronie internetowej. Potwierdzamy, że Twoje konto zostało utworzone pomyślnie.</p>
                <p>Jeśli masz jakiekolwiek pytania lub wątpliwości dotyczące rejestracji lub usług fryzjerskich, prosimy o kontakt z nami.</p>
                <p> Z poważaniem, Zespół fryzjerski</p>
                    
                </body>
                </html>
                END;
                sendEmail($email, "{$name} {$surname}", "Dzękujemy że dołączyłeś", $message, "");
            } catch (Exception $e) {

            }

            $ROUTER->RedirectToPage("register_finish");
        }
        else{
            $register_errors[] = "Wystąpił nieoczekiwany bład, {$stmt->error}";
        }
    }
}


// zwrocenie widoku
return<<<END
<div class="errors">{$register_errors["unexpected"]["message"]}</div>
<form class="container pt-5 pb-4 col-9"method="POST">
<h1 class="pb-4">Rejestracja</h1>
<div class="row" >
    <div class="mb-3 col-6 form-group-has-danger">
        <label class="form-label" for="name" >Imie:</label>
        <input class="form-control {$register_errors["name"]["class"]}" type="text" name="name" id="name" value="$name">
        <div class="invalid-feedback">{$register_errors["name"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="surname">Nazwisko:</label>
        <input class="form-control   {$register_errors["surname"]["class"]}" type="text" name="surname" id="surname" value="$surname">
        <div class="invalid-feedback">{$register_errors["surname"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="login">Login:</label>
        <input class="form-control {$register_errors["login"]["class"]}" type="text" name="login" id="login" value="$login">
        <div class="invalid-feedback">{$register_errors["login"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="email">Email:</label>
        <input class="form-control {$register_errors["email"]["class"]}" type="email" name="email" id="email" value="$email">
        <div class="invalid-feedback">{$register_errors["email"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="password">Haslo:</label>
        <input class="form-control {$register_errors["password"]["class"]}" type="password" name="password" id="password" value="$password">
        <div class="invalid-feedback">{$register_errors["password"]["message"]}</div>
    </div>
    <div class="mb-3 col-6" >
        <label class="form-label" for="password2">powtórz hasło:</label>
        <input class="form-control {$register_errors["password"]["class"]}" type="password" name="password2" id="password2" value="$password2">
        <div class="invalid-feedback">{$register_errors["password"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="phone">Nr telefonu:</label>
        <input class="form-control {$register_errors["phone"]["class"]}" type="text" name="phone" id="phone" value="$phone">
        <div class="invalid-feedback">{$register_errors["phone"]["message"]}</div>
    </div>
    <div class="mb-3">
        <input class="btn btn-primary" type="submit" name="submit_form" value="Utwórz konto">
    </div>
</div>
</form>
END;
?>