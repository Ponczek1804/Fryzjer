<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Fuynkcja pobiera z bazy juz zarezerwowane godziny w danym dniu
 * zwraca tablice napisow z godzina i minuta
 */
function get_appointments_booked_hours(mysqli $DB, DateTime $selected_date){
    $booked_hours = [];

    $selected_date_str = $selected_date->format("Y-m-d");
    $next_day = (clone $selected_date)->modify("+1 days")->format("Y-m-d");

    $stmt = $DB->prepare("SELECT `date` FROM `appointments` WHERE `date` >= ? and `date` < ?");
    $stmt->bind_param("ss", $selected_date_str, $next_day);
    if(!$stmt->execute()){
        throw new Exception("Wystąpił nieoczekiwany bład");
    }

    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $booked = new DateTime($row['date']);
        $booked_hours[] = $booked->format('H:i');
    }

    return $booked_hours;
}


/**
 * funkcja przygotowuje liste możliwych godzin na jakie można zarezerwować wizyte
 * i zraca liste elementów w ponizszym formacie. "disabled"==true oznacza ze wizyta na dana godzine jest juz zarezerwowana
 * [
 * "value"=>format("Y-m-d H:i"),
 * "name"=>format("H:i"),
 * "disabled"=>true/false
 * ]
 *
 **/
function get_appointments_hours(mysqli $DB, DateTime $selected_date){
    $selected_date = clone ($selected_date);
    $today = new DateTime();
    $booked_hours = get_appointments_booked_hours($DB, $selected_date);

    $options_hour_list = [];
    if(empty($book_errors)){

        // ustawienie godzin pracy
        $selected_date->setTime(10, 0);
        $end_date = (clone $selected_date)->setTime(18, 0);

        // jezeli wybrano dzisiejsza date
        if ($selected_date->format("Y-m-d") == $today->format("Y-m-d")) {
            // ustawienie aktualnej godziny
            $selected_date->setTime(intval($today->format("H")), intval($today->format("i")));

            // obliczenie i zaokraglenie godziny według interwału
            $minutes_to_add = 30 - intval($today->format("i")) % 30;
            $selected_date->modify("+$minutes_to_add minutes");
        }

        
        while($selected_date < $end_date){
            $options_hour_list[] = [
                "value" => $selected_date->format("Y-m-d H:i"),
                "name" => $selected_date->format("H:i"),
                "disabled" => in_array($selected_date->format("H:i"), $booked_hours)
            ];
            $selected_date->modify("+30 minutes");
        }
    }

    return $options_hour_list;
}

function get_appointments_hours_as_html_select($appointments_hours, DateTime $selected_date = null){
    $html = '<select class="form-select" aria-label="appointment_date" name="appointment_date" id="appointment_date">';

    foreach($appointments_hours as $appointments_hour){
        if(!$appointments_hour["disabled"]){
                $html .= "<option value='{$appointments_hour['value']}'>{$appointments_hour['name']}</option>";
        }
        else{
            if($selected_date != null && $appointments_hour["value"] == $selected_date->format("Y-m-d H:i")){
                $html .= "<option value='{$appointments_hour['value']}' selected>{$appointments_hour['name']}</option>";
            }
            else{
                $html .= "<option value='{$appointments_hour['value']}' disabled>{$appointments_hour['name']}</option>";
            }
        }
    }

    $html .= '</select>';
    return $html;
}


function get_appointments(mysqli $DB){
    $stmt = $DB->prepare("select appointments.id, appointments.date, users.name as firstname, users.surname, users.email, users.number, services.name, services.price from appointments INNER JOIN users on users.id = appointments.user_id INNER JOIN services on services.id = appointments.service_id order by date");
    if(!$stmt->execute()){
        throw new Exception("Wystąpił nieoczekiwany bład");
    }
    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC);

    return $appointments;
}

/**
 * Funkcja pobiera z bazy rodzaje usług ktore można wybrać podczas rezerwacji wizyty
 */
function get_services(mysqli $DB){
    $stmt = $DB->prepare("select * from services");
    if(!$stmt->execute()){
        throw new Exception("Wystąpił nieoczekiwany bład");
    }

    $result = $stmt->get_result();
    $services = $result->fetch_all(MYSQLI_ASSOC);

    return $services;
}



function get_service_by_id(mysqli $DB, int $service_id){
    $stmt = $DB->prepare("select * from services where id = ?");
    $stmt->bind_param("i", $service_id);
    if(!$stmt->execute()){
        throw new Exception("Wystąpił nieoczekiwany bład");
    }
    $resoult = $stmt->get_result();

    if($resoult->num_rows == 0)
        throw new Exception("Nie odnaleziono usługi");

    return $resoult->fetch_assoc();
}

function get_services_as_html_select($services, int $selected_service = 1){

    $html = '<select class="form-select" aria-label="service_id" name="service_id" id="service_id">';

    foreach($services as $service){
        if($service['id'] == $selected_service)
            $html.= "<option value='{$service['id']}' selected>";
        else
            $html.= "<option value='{$service['id']}'>";
        $html.= "{$service['name']} - {$service['price']} zł";
        $html .= "</option>";
    }

    $html .= "</select>";
    return $html;
}

function getAppointmentById(mysqli $DB, int $appointmentId){
    $stmt = $DB->prepare("SELECT * FROM `appointments` WHERE `id` = ?");
    $stmt->bind_param("i", $appointmentId);
    if(!$stmt->execute()){
        throw new Exception("Wystąpił nieoczekiwany bład");
    }
    $resoult = $stmt->get_result();

    if($resoult->num_rows == 0)
        throw new Exception("Nie odnaleziono wizyty");

    return $resoult->fetch_assoc();
}


function get_errors_as_html($errors){
    $html = '<div class="errors container">';

    foreach ($errors as $error) {
        $html.= "<p class='error text-center text-danger pt-5 pb-4'>$error</p>";
    }
    $html .= '</div>';
    
    return $html;
}


function get_user_by_email($DB, $email){
    $stmt = $DB->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->bind_param("s", $email);
    if(!$stmt->execute()){
        throw new Exception("Wystąpił nieoczekiwany bład");
    }
    $resoult = $stmt->get_result();

    if($resoult->num_rows == 0)
        throw new Exception("Nie ma takiego emaila w bazie");

    return $resoult->fetch_assoc();
}


function sendEmail(string $addres_email, string $addres_name, string $subject, string $html_body, string $text_body){
    require_once '@php/lib/phpMailer/Exception.php';
    require_once '@php/lib/phpMailer/PHPMailer.php';
    require_once '@php/lib/phpMailer/SMTP.php';

    $mail = new PHPMailer(true);

    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Timeout = 120;
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'twojfryzjer@outlook.com';
    $mail->Password   = 'qaz123QAZ!@#';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('twojfryzjer@outlook.com', 'TwojFryzjer');
    $mail->addAddress($addres_email, $addres_name);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $html_body;
    $mail->AltBody = $text_body;

    $mail->send();
}

?>