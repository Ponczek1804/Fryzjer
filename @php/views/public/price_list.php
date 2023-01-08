<?php

$DB = $GLOBALS['DB'];

$errors = [];
require_once('@php/controler/miscellaneous.php');

try {
    $serices = get_services($DB);
    $services_html = <<<END
    <h1 class="text-center pt-4 pb-3">Cennik usług</h1>
        <table class="table table-bordered container pt-5 pb-4">
        <thead class="table-primary">
            <tr>
                <th scope="col">Nazwa usługi</th>
                <th scope="col">Cena</th>
            </tr>
        </thead>
        <tbody>
    END;

    foreach ($serices as $serice) {
        $services_html.=<<<END
        <tr>
            <td>{$serice['name']}</td>
            <td>{$serice['price']}</td>
        </tr>
        END;
    }
    
    $services_html .= "</tbody></table>";


} catch (Exception $e) {
    $errors[] = $e->getMessage();
}



return <<<END
$services_html
END;
?>