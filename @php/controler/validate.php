<?php


function validate_name($name){
    if((strlen($name) < 3) || (strlen($name) > 24)){
        return "Imie powinno się składać od 3 do 24 znaków";
    }
}


function validate_surname($surname){
    if((strlen($surname) < 3) || (strlen($surname) > 24)){
        return "Nazwisko powinno się składać od 3 do 24 znaków";
    }
}

function validate_login($login){
    if(strlen($login) < 5){
        return "login powinien posiadać co najmniej 5 zanków";
    }
}

function validate_if_login_exist($db, $login){
    $stmt = $db->prepare("Select * from users where login=?");
    $stmt->bind_param("s",$login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return 'Taki login jest już zajęty';
    }

}

function validate_passwords($password1, $password2){
    if(strlen($password1) < 8){
        return "Hasło powinno posiadać co najmniej 8 znaków";
    }
    else if($password1 != $password2){
        return "Hasła nie są zgodne";
    }
}

function validate_current_password($password){
    if(!password_verify($password, $_SESSION['user']['password'])){
        return "Obecne hasło jest nieprawidłowe";
    }
}

function validate_email($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Wprowadzono email w  niewłaściwym formacie";
    }
}

function validate_if_email_exist($db, $login){
    $stmt = $db->prepare("Select * from users where email=?");
    $stmt->bind_param("s",$login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return 'Taki email jest już zajęty';
    }

}

function validate_phone($phone){
    // \+ ucieczka do znaku specjalnego +
    // 48 sprawdz przedrostek nr telefonu
    // [0-9] cyfry od 0 do 9
    // {9} dokładnie 9 razy
    if(!preg_match("/\+48[0-9]{9}/", $phone)){
        return "Wprowadzono niewłaściwy numer telefonu";
    }
}

function validate_service_name($service_name){
    if((strlen($service_name) < 5) || (strlen($service_name) > 100)){
        return "Nazwa usługi powinna się składać od 3 do 24 znaków";
    }
}

function validate_service_price($price){
    $price = floatval($price);
    if($price < 1 || $price > 1000){
        return "Cena za usługe musi mieścić się w przedziale od 1zł do 1000zł";
    }
}

function validate_if_service_not_exist(mysqli $DB, int $service_id){
    $stmt = $DB->prepare("Select * from services where id=?");
    $stmt->bind_param("s",$service_id);

    if (!$stmt->execute())
        return "Coś poszło nie tak";


    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        return 'Taka usługa nie istnieje';
    }
}


function validate_appointment(mysqli $DB, DateTime $appointment_date, int $appointment_id = null){
    $today = new DateTime();

    if($appointment_id != null){
        try {
            $appointment = getAppointmentById($DB, $appointment_id);
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }

    if($appointment_date->format('N') == "7")
        return "W niedziele nasz salon jest zamknięty wybierz inną date";

    if ($appointment_date < $today)
        return "Data usułgi nie może być wcześniejsza od obecnej";

    try {
        require_once('@php/controler/miscellaneous.php');
        $appointment_date_hours = get_appointments_hours($DB, $appointment_date);
    }catch (Exception $e) {
        return $e->getMessage();
    }
    
    $filtered = array_filter($appointment_date_hours, function ($appointment_hour) use ($appointment_date){
        return $appointment_hour["value"] == $appointment_date->format("Y-m-d H:i");
    });

    if (empty($filtered))
        return "Podano nieprawidłową date";

    $filtered = reset($filtered);

    if ($appointment_id != null) {
        $ad = new DateTime($appointment['date']);
        if($filtered['value'] != $ad->format("Y-m-d H:i") && $filtered["disabled"] == true)
            return "Ten termin xd jest juz zajęty";
    }
    else{
        if($filtered["disabled"] == true)
            return "Ten termin jest juz zajęty";
    }
}



?>