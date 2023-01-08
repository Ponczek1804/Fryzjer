<?php
$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];

$new_service_errors = [
    "service_name"=>["message"=>"","class"=>""],
    "service_price"=>["message"=>"","class"=>""],
    "unexpected"=>["message"=>"","class"=>""]
];

$service_name = $_POST['service_name'] ?? '';
$price = $_POST['price'] ?? '';

// czy kliknieto przycisk utwórz usługe
if (isset($_POST['submit'])) {
    try {
        require_once('@php/controler/validate.php');
        
        // walidacja
        $new_service_errors["service_name"]["message"] = validate_service_name($service_name);
        $new_service_errors["service_price"]["message"] = validate_service_price($price);
        foreach ($new_service_errors as $key => $error) {
            $new_service_errors[$key]["class"]  = $error["message"] != "" ?  "is-invalid" : "is-valid";
        }

        // czy istnieja bledy
        $check_errors = array_filter($new_service_errors, function ($error) {
            return $error["message"] != "";
        });
        if(!empty($check_errors))
            throw new Exception("");
        
        // wstaw do bazy
        $stmt = $DB->prepare("INSERT INTO `services` (`name`, `price`) VALUES (?, ?)");
        $stmt->bind_param("sd",$service_name, $price);
        if(!$stmt->execute())
            throw new Exception("Error Processing Request");

        // przekieruj
        $ROUTER->RedirectToPage("admin_home");

    } catch (Exception $e) {
        if ($e->getMessage() != "")
            $new_service_errors["unexpected"]["message"] = $e->getMessage();
    }
}

return <<<END
<div class="container pt-5 pb-4 g-col-6">
    <p class="text-center" >Dodaj nową usługę</p>
</div>
<div class="errors container">
    <p class='error text-center text-danger pt-5 pb-4'>{$new_service_errors["unexpected"]["message"]}</p>
</div>
<form class="container pt-5 pb-4 col-4"method="POST">
<div class="row" >
    <div class="mb-3 form-group-has-danger">
        <label class="form-label" for="service_name" >Nazwa usługi: </label>
        <input class="form-control {$new_service_errors["service_name"]["class"]}"  type="text" name="service_name" value="$service_name" id="service_name">
        <div class="invalid-feedback">{$new_service_errors["service_name"]["message"]}</div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="price">Cena za usługe: </label>
        <input class="form-control {$new_service_errors["service_price"]["class"]}"  type="number"name="price" value="$price" id="price"> 
        <div class="invalid-feedback">{$new_service_errors["service_price"]["message"]}</div>  
    </div>
    <div class="mb-3">
        <input class="btn btn-primary d-grid gap col-8 mx-auto" type="submit" name="submit" value="Utwórz usługę">
    </div>
</div>
</form>
END;
?>
