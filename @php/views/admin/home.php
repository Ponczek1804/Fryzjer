<?php


$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];

require_once('@php/controler/miscellaneous.php');

try {
    $services = get_services($DB);
    $appointments = get_appointments($DB);

    // wygenerowanie tabeli z usługami
    $services_html = <<<END
        <table class="table " style="width : 50%;">
            <thead>
                <tr>
                    <th scope="col">Nazwa usługi</th>
                    <th scope="col">Cena</th>
                    <th scope="col">Zarządrzaj</th>
                </tr>
            </thead>
            <tbody>
        END;
    foreach ($services as $service) {
        $services_html.=<<<END
            <tr>
                <td>{$service['name']}</td>
                <td>{$service['price']}</td>
                <td>
                    {$ROUTER->GetHtmlLink("admin_edit_service", "Edytuj", "btn btn-primary btn-sm", "?id={$service["id"]}")}
                    {$ROUTER->GetHtmlLink("admin_delete_service", "Usuń", "btn btn-primary btn-sm", "?id={$service["id"]}")}
                </td>
            </tr>
        END;
    }
    $services_html.="</tbody></table>";

    // wygenerowanie tabeli z terminem usługi i infromacja o osobie
    $appointments_html = <<<END
    <h1 class="p-4 pb-0">Zarządzaj wizytami</h1>
    <table class="table pt-10 pb-4 col-4 mt-5" >
    <thead>
        <tr>
            <th scope="col">Data</th>
            <th scope="col">Imie</th>
            <th scope="col">Nazwisko</th>
            <th scope="col">Email</th>
            <th scope="col">Telefon</th>
            <th scope="col">Usługa</th>
            <th scope="col">Cena</th>
            <th scope="col">Zarządrzaj</th>
        </tr>
    </thead>
    <tbody>
    END;
    foreach ($appointments as  $appointment) {
        $appointments_html.=<<<END
        <tr>
            <td>{$appointment['date']}</td>
            <td>{$appointment['firstname']}</td>
            <td>{$appointment['surname']}</td>
            <td>{$appointment['email']}</td>
            <td>{$appointment['number']}</td>
            <td>{$appointment['name']}</td>
            <td>{$appointment['price']}</td>
            <td>
                {$ROUTER->GetHtmlLink("admin_edit_appointment", "Edytuj", "btn btn-primary btn-sm", "?id={$appointment["id"]}")}
                {$ROUTER->GetHtmlLink("admin_delete_appointment", "Usuń",   "btn btn-primary btn-sm", "?id={$appointment["id"]}")}
            </td>
        </tr>
        END;
    }
    $appointments_html.="</tbody></table>";
} catch (Exception $e) {
    //throw $th;
}


return <<<END
<div class="container pt-5 pb-4 g-col-6">
    {$ROUTER->GetHtmlLink("admin_create_service", "Utwórz usługe", "btn btn-primary btn-sm col-6")}
    $services_html
</div>
$appointments_html
END;
?>