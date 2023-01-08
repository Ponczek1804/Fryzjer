<?php
$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];
require_once('@php/controler/validate.php');

$errors = "";
$service_id = intval($_GET['id'] ?? '');

if(isset($_POST["submit_form"]))
try {
    // czy usuluga istnieje
    $validate_error = validate_if_service_not_exist($DB, $service_id);
    if ($validate_error != "")
        throw new Exception($validate_error);
    
    // usun zaleznosci
    $stmt = $DB->prepare("DELETE FROM `appointments` WHERE `service_id` = ?");
    $stmt->bind_param("i", $service_id);
    if(!$stmt->execute())
        throw new Exception("Error Processing Request");

    // usun usluge
    $stmt = $DB->prepare("DELETE FROM `services` where `id` = ?");
    $stmt->bind_param("i", $service_id);
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
        Kliknięcie przycisku usun, spowoduje usunięcie usługi oraz wszystkich powiązanych z nią wizyt.
        Czy na pewno chcesz usunąć ?
    </div>
    <div class="mb-3">
        <input class="btn btn-primary d-grid gap col-8 mx-auto" type="submit" name="submit_form" value="Usuń usługe">
    </div>
    {$ROUTER->GetHtmlLink("admin_home", "Powrót", "btn btn-primary btn-sm")}
</div>
</form>
END;

?>