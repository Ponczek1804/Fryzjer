<?php
require_once('@php/controler/miscellaneous.php');
require_once('@php/controler/validate.php');

$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];

$errors = [];
$succes_message = "";

if(isset($_POST["submit_form"])){
    try {
        $email = $_POST['email'] ?? '';
        $validate_error = validate_email($email);
        if($validate_error != null)
            throw new Exception($validate_error);
            
        $user = get_user_by_email($DB, $email);


        $new_password = sha1(strval(rand()));
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $DB->prepare("UPDATE `users` SET `password` = ? WHERE `users`.`id` = ?");
        $stmt->bind_param("si",$hashed_password, $user['id']);
        if(!$stmt->execute())
            throw new Exception("Error Processing Request");
        
            
        $message=<<<END
        Witaj twoje hasło zostało zresetowane
        <br> Nowe hasło to: <strong>$new_password</strong>
        END;
        
        sendEmail($email, "{$user["name"]} {$user["surname"]}", "Password Reset", $message, "");

        
        $succes_message = "Twoje hasło zostało zresetowane i przesłane na adres email, przedź na strone {$ROUTER->GetHtmlLink("login", "logowania")}";

    } catch (Exception $e) {
        $errors []= $e->getMessage();
    }
}

$errors_html = get_errors_as_html($errors);

return <<<END
$errors_html
$succes_message
<form class="container pt-5 pb-4 col-4"method="POST">
<div class="row">
    <div class="mb-3">
        <label class="form-label" for="email" >Wprowadź adres email: </label>
        <input class="form-control" type="text" name="email" id="email">
    </div>
    <div class="mb-3">
        <input class="btn btn-primary d-grid gap col-8 mx-auto" type="submit" name="submit_form" value="Wyślij wiadomość">
    </div>
</div>
</form>
END;
?>