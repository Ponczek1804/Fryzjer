<?php

// wyłączenie raportowania błędów, błąd spowoduje zwrócenie kodu 500
// error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);

// start sesji
session_start();

require_once('@php/controler/db.php');
require_once('@php/layouts/default.php');
require_once('@php/controler/authentication.php');
require_once('@php/controler/router.php');


// połączenie z baza danych
$DB = new mysqli($db_host, $db_user, $db_password, $db_name);
$DB->set_charset('utf8mb4');
if ($DB -> connect_errno){
    echo 'Błąd podczas połączenia z bazą';
    exit(0);
}

$ROUTER = new Router("/fryzjer", __DIR__ . "/@php/layouts", __DIR__ . "/@php/views/", "notFound");
$ROUTER->AddRoute("notFound",                 "/404",                        "Nie odnaleziono",    Authentication::All,     "public/404.php",                "layout_default");
$ROUTER->AddRoute("home_page",                 "/",                          "Strona Główna",      Authentication::All,     "public/home.php",               "layout_default");
$ROUTER->AddRoute("login",                     "/login",                     "Logowanie",          Authentication::All,     "public/login.php",              "layout_default");
$ROUTER->AddRoute("register",                  "/register",                  "Rejestracja",        Authentication::All,     "public/register.php",           "layout_default");
$ROUTER->AddRoute("register_finish",           "/register_finish",           "Rejestracja",        Authentication::All,     "public/register_finish.php",    "layout_default");
$ROUTER->AddRoute("price_list",                "/cennik",                    "Cennik",             Authentication::All,     "public/price_list.php",         "layout_default");
$ROUTER->AddRoute("log_out",                   "/logout",                    "Wyloguj",            Authentication::All,     "public/logout.php",             "layout_default");
$ROUTER->AddRoute("password_forgot",           "/password_forgot",           "Resetowanie hasła",  Authentication::All,     "public/passwordForgot.php",     "layout_default");

$ROUTER->AddRoute("admin_home",                "/admin/home",                "adminHome",          Authentication::Admin,   "admin/home.php",                "layout_default");
$ROUTER->AddRoute("admin_create_service",      "/admin/create_service",      "Doddaj usługe",      Authentication::Admin,   "admin/create_service.php",      "layout_default");
$ROUTER->AddRoute("admin_edit_service",        "/admin/edit_service",        "Edytuj usługe",      Authentication::Admin,   "admin/edit_service.php",        "layout_default");
$ROUTER->AddRoute("admin_delete_service",      "/admin/delete_service",      "Usuń usługe",        Authentication::Admin,   "admin/delete_service.php",      "layout_default");
$ROUTER->AddRoute("admin_edit_appointment",    "/admin/edit_appointment",    "Edytuj Wizyte",      Authentication::Admin,   "admin/edit_appointment.php",    "layout_default");
$ROUTER->AddRoute("admin_delete_appointment",  "/admin/delete_appointment",  "Usuń Wizyte",        Authentication::Admin,   "admin/delete_appointment.php",  "layout_default");

$ROUTER->AddRoute("client_home",               "/client/home",               "clientHome",         Authentication::Client,  "client/home.php",               "layout_default");
$ROUTER->AddRoute("client_edit_data",          "/client/edit_personal_data", "Edytuj swoje dane",  Authentication::Client,  "client/edit_personal_data.php", "layout_default");
$ROUTER->AddRoute("client_book_appointment",   "/client/book_appointment",   "Umów wizyte",        Authentication::Client,  "client/book_appointment.php",   "layout_default");
$ROUTER->AddRoute("client_edit_appointment",   "/client/edit_appointment",   "Edytuj wizyte",      Authentication::Client,  "client/edit_appointment.php",   "layout_default");
$ROUTER->AddRoute("client_delete_appointment", "/client/delete_appointment", "Usuń wizyte",        Authentication::Client,  "client/delete_appointment.php", "layout_default");

// wyswetl strone
echo $ROUTER->GetCurrentPage();

// zamkniecie połaczenia z baza
$DB->close();
?>