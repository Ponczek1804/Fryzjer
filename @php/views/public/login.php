<?php

$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];

// Tablica przechowujaca błedy
$login_errors = [];
require_once('@php/controler/miscellaneous.php');

// czy kliknieto przycisk zaloguj sie
if (isset($_POST['submit'])) {
    try {
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $DB->prepare("Select * from users where login= ? or email= ?");
        $stmt->bind_param("ss", $login, $email);

        if (!$stmt->execute())
            throw new Exception("Bład podczas wykonania zapytania");

        $result = $stmt->get_result();
        if($result->num_rows == 0)
            throw new Exception("Błedny login lub hasło");

        $user = $result->fetch_assoc();
        if (!password_verify($password, $user["password"]))
            throw new Exception("Błedny login lub hasło");
        
        $_SESSION['user'] = $user;
        if ($user['admin'] == 1){
            $ROUTER->RedirectToPage('admin_home');
        }
        else{
            $ROUTER->RedirectToPage('client_home');
        }
        

    } catch (Exception $e) {
        $login_errors []= $e->getMessage();
    }
}

$login_errors_html = get_errors_as_html($login_errors);

return <<<END
{$login_errors_html}
<form class="container pt-5 pb-4 col-4"method="POST">
<h1 class="pb-3">Logowanie</h1>
<div class="row" >
    <div class="mb-3 form-group-has-danger">
        <label class="form-label" for="login" >Login: </label>
        <input class="form-control" type="text" name="login" id="login">
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Hasło: </label>
        <input class="form-control" type="password" name="password" id="password">   
    </div>
    <div class="mb-3">
        <input class="btn btn-primary d-grid gap col-8 mx-auto" type="submit" name="submit" value="Zaloguj">
    </div>
    <div class="mb-3 text-center">
        {$ROUTER->GetHtmlLink("password_forgot", "Zapomniałem hasła")}
    </div>
</div>
</form>
END;

?>