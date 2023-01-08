<?php
$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];


$errors = [];
$appointment_id = intval($_GET['id']);
$today = new DateTime();
$selected_date = new DateTime();
$services_html = "";

require_once('@php/controler/miscellaneous.php');
require_once('@php/controler/validate.php');




// jezeli kliknieto przycisk edytuj wizyte
if(isset($_POST['submit_form'])){
    try {
        // pobranie danych
        $service_id = $_POST["service_id"] ?? 0;
        if($service_id == 0)
            throw new Exception("Nie podano rodzaju usługi");

        $appointment_date = $_POST["appointment_date"];
        $appointment_date = new DateTime($appointment_date);
        $appointment_date_str = $appointment_date->format("Y-m-d H:i");

        // walidacja
        $validate_errors = [];
        $validate_errors[] = validate_if_service_not_exist($DB, $service_id);
        $validate_errors []= validate_appointment($DB, $appointment_date, $appointment_id);
        $validate_errors = array_filter($validate_errors);
        if(!empty($validate_errors)){
            $errors = array_merge($errors, $validate_errors);
            throw new Exception("");
        }

        // aktualizacja
        $stmt = $DB->prepare("update appointments set service_id=?, date=? where id = ?");
        $stmt->bind_param("isi",$service_id, $appointment_date_str, $appointment_id);
        if(!$stmt->execute())
            throw new Exception("Błąd połączenia z bazą");

        // przekieruj
        $ROUTER->RedirectToPage("admin_home");

    } catch (Exception $e) {
        if($e->getMessage() != "")
            $errors[] = $e->getMessage();
    }
}

try {
    // pobierz usługe po id, jezeli usluga nie istnieje funkcja zwroci wyjatek
    $appointment = getAppointmentById($DB, $appointment_id);
    $appointment_date = new DateTime($appointment['date']);

    if($appointment_date < new DateTime())
        throw new Exception("Termin wizyty juz minał");

    //Data wizyty którą można wybrać
    $selected_date = new DateTime($_POST['date'] ?? $appointment_date->format('Y-m-d'));

    //pobranie wszystkich dostępnych godzin na ktore mozna arezerwowac wizyte w wybranym dniu
    // i wygenerowanie htmlowego selecta
    $appointments_hours = get_appointments_hours($DB, $selected_date);
    $appointments_hours_html = get_appointments_hours_as_html_select($appointments_hours, $appointment_date);

    // pobranie wszystkich uzlug z bazy
    // i wygenerowanie htmlowego selecta
    $services = get_services($DB);
    $services_html = get_services_as_html_select($services, $appointment["service_id"]);

} catch (Exception $e){
    $errors[] = $e->getMessage();
}


$errors_html = get_errors_as_html($errors);

return<<<END
$errors_html
<form id="chooseDate" class="container pt-5 pb-4 col-4" method="POST">
    <label class="form-label" for="date" >Data wizyty: </label>
    <input class="form-control" type="date" id="date" name="date" onchange="chooseDate.submit()" min="{$today->format("Y-m-d")}" value="{$selected_date->format("Y-m-d")}">
</form>

<form class="container pb-4 col-4"method="POST">
    <label class="form-label" for="appointment_date" >Godzina wizyty: </label>
    $appointments_hours_html
    <label class="form-label" for="service_id">Rodzaj usługi: </label>
    $services_html
    <input class="btn btn-primary mt-4 d-grid gap col-8 mx-auto" type="submit" name="submit_form" value="Edytuj wizyte">
    {$ROUTER->GetHtmlLink("admin_home","Powrót", "btn btn-primary mt-4 d-grid gap col-8 mx-auto")}
</form>

END
?>