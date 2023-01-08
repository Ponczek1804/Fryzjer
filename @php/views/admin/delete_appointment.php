<?php
$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];
require_once('@php/controler/miscellaneous.php');

$errors = "";

$appointment_id = intval($_GET['id'] ?? '');

if(isset($_POST["submit_form"]))
try {
    // pobranie wizyt po id, jezeli taka nie istnie to funkcja zwroci wyjatek
    $appointment = getAppointmentById($DB, $appointment_id);
    
    // usun
    $stmt = $DB->prepare("DELETE FROM `appointments` WHERE `id` = ?");
    $stmt->bind_param("i", $appointment_id);
    if(!$stmt->execute())
        throw new Exception("Error Processing Request");
    
    // przekieruj
    $ROUTER->RedirectToPage("admin_home");
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
    {$ROUTER->GetHtmlLink("admin_home", "Powrót", "btn btn-primary btn-sm")}
</div>
</form>
END;

?>