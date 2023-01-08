<?php

$DB = $GLOBALS['DB'];
$user = $_SESSION['user'];

$edit_errors = [
    "name"=>["message"=>"","class"=>""],
    "surname"=>["message"=>"","class"=>""],
    "phone"=>["message"=>"","class"=>""],
    "email"=>["message"=>"","class"=>""],
    "current_password"=>["message"=>"","class"=>""],
    "login"=>["message"=>"","class"=>""],
    "new_password"=>["message"=>"","class"=>""],
    "unexpected"=>["message"=>"","class"=>""],
];
$edit_succes_message = "";

$name = $_POST['name'] ?? $user['name'];
$surname = $_POST['surname'] ?? $user['surname'];
$login = $_POST['login'] ?? $user['login'];
$email = $_POST['email'] ?? $user['email'];
$phone = $_POST['phone'] ?? $user['number'];
$current_password = $_POST['current_password'] ?? '';
$new_password1 = $_POST['new_password1'] ?? '';
$new_password2 = $_POST['new_password2'] ?? '';

if (isset($_POST['submit'])) {
    require('@php/controler/validate.php');

    $edit_errors["name"]["message"] = validate_name($name);
    $edit_errors["surname"]["message"]= validate_surname($surname);
    $edit_errors["phone"]["message"] = validate_phone($phone);
    
    // jezeli zmieniono login
    if($login != $user['login']){
        $edit_errors["login"]["message"] = validate_login($login) ? validate_login($login) : validate_if_login_exist($DB, $login);
    }
    
    // jezeli zmieniono email
    if($email != $user['email']){
        $edit_errors ['email']["message"] = validate_email($email) ? validate_email($email) : validate_if_email_exist($DB,$email);    
        
    }

    // jezeli nie zmieniono hasla
    if($new_password1 == ''){
        $new_password1 = $user['password'];
    }
    else{
        $edit_errors["new_password"]["message"] = validate_passwords($new_password1, $new_password2);
        $edit_errors["current_password"]["message"] = validate_current_password($current_password);
        $new_password1 = password_hash($new_password1, PASSWORD_DEFAULT);
    }

    foreach ($edit_errors as $key => $error) {
        $edit_errors[$key]["class"]  = $error["message"] != "" ?  "is-invalid" : "is-valid";
    }
    
    
    $check_errors = array_filter($edit_errors, function ($error) {
        return $error["message"] != "";
    });

    if (empty($check_errors)) {
        $stmt = $DB->prepare("update users set name=?, surname=?, email=?, number=?, login=?, password=? where id=?");
        $stmt->bind_param("ssssssi",$name, $surname, $email, $phone, $login, $new_password1, $user['id']);

        if($stmt->execute()){
            $edit_succes_message = 'Dane zostały zaktualizowane';
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['surname'] = $surname;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['login'] = $login;
            $_SESSION['user']['password'] = $new_password1;
            
        }
        else{
            $edit_errors["unexpected"]["message"] = "Wystąpił nieoczekiwany bład {$stmt->error}";
        }
    }
    


}

return<<<END
$edit_succes_message
<div class="errors">{$edit_errors["unexpected"]["message"]}</div>
<form class="container pt-5 pb-4 col-9"method="POST">
<div class="row">
    <div class="mb-3 col-6 form-group-has-danger">
        <label class="form-label" for="name" >Imie:</label>
        <input class="form-control {$edit_errors["name"]["class"]}" type="text" name="name" id="name" value="$name">
        <div class="invalid-feedback">{$edit_errors["name"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="surname">Nazwisko:</label>
        <input class="form-control   {$edit_errors["surname"]["class"]}" type="text" name="surname" id="surname" value="$surname">
        <div class="invalid-feedback">{$edit_errors["surname"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="login">Login:</label>
        <input class="form-control {$edit_errors["login"]["class"]}" type="text" name="login" id="login" value="$login">
        <div class="invalid-feedback">{$edit_errors["login"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="current_password">Obecne haslo:</label>
        <input class="form-control {$edit_errors["current_password"]["class"]}" type="password" name="current_password" id="current_password">
        <div class="invalid-feedback">{$edit_errors["current_password"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="new_password1">Nowe haslo:</label>
        <input class="form-control {$edit_errors["new_password"]["class"]}" type="password" name="new_password1" id="new_password1">
        <div class="invalid-feedback">{$edit_errors["new_password"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="new_password2">Powtórz nowe hasło:</label>
        <input class="form-control {$edit_errors["new_password"]["class"]}" type="password" name="new_password2" id="new_password2">
        <div class="invalid-feedback">{$edit_errors["new_password"]["message"]}</div>
    </div>
    <div class="mb-3 col-6">
        <label class="form-label" for="email">Email:</label>
        <input class="form-control {$edit_errors["email"]["class"]}" type="email" name="email" id="email" value="$email">
        <div class="invalid-feedback">{$edit_errors["email"]["message"]}</div>
    </div>

    <div class="mb-3 col-6">
        <label class="form-label" for="phone">Nr telefonu:</label>
        <input class="form-control {$edit_errors["phone"]["class"]}" type="text" name="phone" id="phone" value="$phone">
        <div class="invalid-feedback">{$edit_errors["phone"]["message"]}</div>
    </div>
    <div class="mb-3">
        <input class="btn btn-primary" type="submit" name="submit" value="Zapisz dane">
    </div>
</div>
</form>
END;
?>