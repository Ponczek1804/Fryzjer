<?php
$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];


$errors = [];


// pobierz wszystkie aktualne wizyty dla zalogowanego uzytkownika
$appointments = [];
try {
    $today = new DateTime();
    $today_str = $today->format("Y-m-d H:i");
    $stmt = $DB->prepare("SELECT `appointments`.`id`, `date`, `name`, `price` from `appointments` INNER JOIN `services` on `appointments`.`service_id` = `services`.`id` where `date` > ? and `user_id` = ? order by `date`");
    $stmt->bind_param("ss", $today_str, $_SESSION['user']['id']);
    if(!$stmt->execute())
        throw new Exception("Error Processing Request");
    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    if($e->getMessage() != "")
        $errors[] = $e->getMessage();
}

 // wygenerrowanie html z zblizajacymi sie wizytami
if(empty($appointments)){
    $appointments_html = <<<END
    <div class="container pt-5 pb-4 g-col-6">
        Wygląda na to że nie masz zadnych zbliżających się termiów.
        {$ROUTER->GetHtmlLink("client_book_appointment", "Umów wizyte teraz")}
    </div>
    END;
}
else{
    $appointments_html = <<<END
    <div class="container pt-5 pb-4 g-col-6">
    <p class="text-center" >Zbliżające się wizyty</p>
    </div>
    <table class="table container pt-5 pb-4">
        <thead>
            <tr>
                <th scope="col">Data</th>
                <th scope="col">Nazwa usługi</th>
                <th scope="col">Cena</th>
                <th scope="col">Zarządrzaj</th>
            </tr>
        </thead>
        <tbody>    
    END;
    foreach($appointments as $appointment){
        $appointment_date = new DateTime($appointment['date']);
        $appointments_html.=<<<END
            <tr>
                <td>{$appointment_date->format("Y-m-d H:i")}</td>
                <td>{$appointment["name"]}</td>
                <td>{$appointment["price"]}</td>
                <td>
                    {$ROUTER->GetHtmlLink("client_edit_appointment","Edytuj","btn btn-primary btn-sm","?id={$appointment["id"]}")}
                    {$ROUTER->GetHtmlLink("client_delete_appointment","Usuń","btn btn-primary btn-sm","?id={$appointment["id"]}")}
                </td>
            </tr>
        END;
    }
    $appointments_html.= "</tbody></table>";
}

return <<<END
$appointments_html
END;
?>