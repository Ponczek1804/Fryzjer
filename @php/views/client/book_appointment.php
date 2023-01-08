<?php

$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];

$errors = [];

require_once('@php/controler/miscellaneous.php');
require_once('@php/controler/validate.php');

$today = new DateTime();

// wybrana data
$_SESSION['selected_date'] = $_POST['date'] ?? $_SESSION['selected_date'] ?? $today->format("Y-m-d");
$selected_date = new DateTime($_SESSION['selected_date']);

// walidacjia
if($selected_date->format('N') == "7")
    $errors []= "W niedziele nasz salon jest zamknięty wybierz inną date";


// gdy kliknieto przycisk umow wizyte
if (empty($errors) && isset($_POST['submit_form'])){
    try {
        // walidacja
        if(!isset($_POST['appointment_date']))
            throw new Exception("Nie wybrano godziny");
        if(!isset($_POST['service_id']))
            throw new Exception("Nie wybrano usługi");

        $appointment_date =  new DateTime($_POST['appointment_date']);
        $appointment_date_str = $appointment_date->format("Y-m-d H:i");
        $service_id = intval($_POST['service_id']);

        $validate_errors = [];
        $validate_errors[] = validate_if_service_not_exist($DB, $service_id);
        $validate_errors []= validate_appointment($DB, $appointment_date);

        // czy sa błedy
        $validate_errors = array_filter($validate_errors);
        if(!empty($validate_errors)){
            $errors = array_merge($errors, $validate_errors);
            throw new Exception("");
        }

        // wstaw do bazy
        $stmt = $DB->prepare("INSERT INTO `appointments` (`user_id`, `service_id`, `date`) VALUES (?, ?, ?)");
        $stmt->bind_param("iis",$_SESSION['user']['id'], $service_id, $appointment_date_str);
         if(!$stmt->execute())
            throw new Exception("Wystąpił nieoczekiwany błąd: {$stmt->error}");

        // przekieruj
        $ROUTER->RedirectToPage("client_home");

    } catch (Exception $e) {
        if($e->getMessage() != "")
            $errors[] = $e->getMessage();
    }
}



try {
    // pobierz usługi
    // i wygeneruj htmlowego selecta
    $services = get_services($DB);
    $services_html = get_services_as_html_select($services);

    // pobierz dostępne godziny na które można umówić wizyte
    // i wygeneruj htmlowego selecta
    $appointment_hours =  get_appointments_hours($DB, $selected_date);
    $appointment_hours_html =  get_appointments_hours_as_html_select($appointment_hours);

    // jezeli nie ma dostępnych godzin
    if(empty($appointment_hours)){
        throw new Exception("Wygląda na to że nie ma już dostępnych godzin, spróbuj zarezerwować wizyte w inny dzień");
    }

} catch (Exception $e) {
    if($e->getMessage() != "")
        $errors[] = $e->getMessage();
}


$errors_html = get_errors_as_html($errors);

return <<<END
$errors_html
<form class="container pt-5 col-4" id="chooseDate" method="POST">
    <div class="row">
        <div class="mb-3 form-group-has-danger">
            <label class="form-label" for="date" >Data wizyty: </label>
            <input class="form-control" type="date" id="date" name="date" onchange="chooseDate.submit()" min="{$today->format("Y-m-d")}" value="{$_SESSION['selected_date']}">
        </div>
    </div>
</form>
    
<form class="container pb-4 col-4" method="POST">
    <div class="row">
        <div class="mb-3 form-group-has-danger">
            <label class="form-label" for="appointment_date" >Godzina Wizyty: </label>
            $appointment_hours_html
        </div>
        <div class="mb-3">
            <label class="form-label" for="service_id">Rodzaj usługi: </label>
            $services_html
        </div>  
        <input class="btn btn-primary d-grid gap col-8 mx-auto" type="submit" name="submit_form" value="Umow wizyte">
    </div>
</form>
END;
?>