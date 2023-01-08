<?php
$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];
require_once('@php/controler/miscellaneous.php');

$errors = "";

$appointment_id = intval($_GET['id'] ?? '');

if(isset($_POST["submit_form"]))
try {
    $appointment = getAppointmentById($DB, $appointment_id);
    if($appointment['user_id'] != $_SESSION['user']['id'])
        throw new Exception("Nie masz uprawnień aby usunąć tą wizyte");
    
    $stmt = $DB->prepare("DELETE FROM `appointments` WHERE `id` = ? and `user_id` = ?");
    $stmt->bind_param("ii", $appointment_id, $_SESSION['user']['id']);

    if(!$stmt->execute())
        throw new Exception("Error Processing Request");
        
    $ROUTER->RedirectToPage("client_home");
} catch (Exception $e) {
    if ($e->getMessage() != "")
        $errors = $e->getMessage();
}



return <<<END
<div class="errors container">
    <p class='error text-center text-danger pt-5'>{$errors}</p>
</div>
<form class="container pb-4 col-4"method="POST">
<div class="row" >
    <div class="mb-3">
        Czy chcesz anulować wizyte ?
    </div>
    <div class="mb-3">
        <input class="btn btn-primary d-grid gap col-8 mx-auto" type="submit" name="submit_form" value="Anuluj Wizyte">
    </div>
    {$ROUTER->GetHtmlLink("client_home", "Powrót", "btn btn-primary btn-sm")}
</div>
</form>
END;

?>