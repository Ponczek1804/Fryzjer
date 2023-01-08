<?php

enum Authentication{
    case All;
    case Admin;
    case Client;
}

function authentication_check(Authentication $level){
    $auth = true;

    if ($level == Authentication::All)
        return $auth;

    // jezeli uzytkownik nie jest zalogowany
    if(!isset($_SESSION['user'])){
        $auth = false;
    }
    
    if($auth){
        $user = $_SESSION['user'];
        $admin = $user['admin'];

        // jezeli jest uzytkownikiem i che wejsc na strone z prawami administratora
        if($level == Authentication::Admin && $admin != 1){
            $auth = false;
        }

        // jezeli jest administratorem i chce wejsc na strone z prawami uzytkownika
        else if($level == Authentication::Client && $admin != 0){
            $auth = false;
        }

    }

    // jezeli uzytkownik nie ma uprawnien przenies na strone logowania
    if(!$auth){
        $ROUTER = $GLOBALS['ROUTER'];
        $ROUTER->RedirectToPage("login");
    }

    


}

?>