<?php
require_once('@php/controler/miscellaneous.php');

$DB = $GLOBALS['DB'];
$ROUTER = $GLOBALS['ROUTER'];

$service_id = intval($_GET['id'] ?? '');
$service_name = "";
$service_price = "";

$edit_service_errors = [
    "service_name" => ["message"=>"", "class"=>""],
    "price" => ["message"=>"", "class"=>""],
    "unexpected" => ["message"=>"", "class"=>""],
];


try {
    $service = get_service_by_id($DB, $service_id);
    $service_name = $service['name'];
    $service_price = $service['price'];
}catch (Exception $e) {
    $edit_service_errors["unexpected"]["message"] = $e->getMessage();
}

// Gdy kliknieto edytuj
if (isset($_POST['submit']) && $edit_service_errors["unexpected"]["message"] == ""){
    try {
        $service_name = $_POST['service_name'] ?? '';
        $price = $_POST['price'] ?? '';

        require_once('@php/controler/validate.php');

        // validacja
        $edit_service_errors['service_name']['message'] = validate_service_name($service_name);
        $edit_service_errors['price']['message'] = validate_service_price($price);

        foreach ($edit_service_errors as $key => $error) {
            $edit_service_errors[$key]["class"]  = $error["message"] != "" ?  "is-invalid" : "is-valid";
        }
        // czy istnieja bledy
        $check_errors = array_filter($edit_service_errors, function ($error) {
            return $error["message"] != "";
        });
        if(!empty($check_errors))
            throw new Exception("");
        
        // aktualizacja
        $stmt = $DB->prepare("update services set name=?, price=? where id=?");
        $stmt->bind_param("sdi",$service_name, $price, $service_id);
        if(!$stmt->execute())
            throw new Exception("Error Processing Request");

        // przekieruj
        $ROUTER->RedirectToPage('admin_home');
    } catch (Exception $e) {
        if ($e->getMessage() != "")
            $edit_service_errors["unexpected"]["message"] = $e->getMessage();
    }
}



return <<<END
<div class="container pt-5 pb-4 g-col-6">
    <p class="text-center">Edytuj usługe</p>
</div>
<div class="errors container">
    <p class='error text-center text-danger pt-5 pb-4'>{$edit_service_errors["unexpected"]["message"]}</p>
</div>
<form class="container pt-5 pb-4 col-4"method="POST">
<div class="row" >
    <div class="mb-3 form-group-has-danger">
        <label class="form-label" for="service_name" >Nazwa usługi: </label>
        <input class="form-control {$edit_service_errors["service_name"]["class"]}"  type="text" name="service_name" value="$service_name" id="service_name">
        <div class="invalid-feedback">{$edit_service_errors["service_name"]["message"]}</div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="price">Cena za usługe: </label>
        <input class="form-control {$edit_service_errors["price"]["class"]}"  type="number" name="price" value="$service_price" id="price"> 
        <div class="invalid-feedback">{$edit_service_errors["price"]["message"]}</div>  
    </div>
    <div class="mb-3">
        <input class="btn btn-primary d-grid gap col-8 mx-auto" type="submit" name="submit" value="Edytuj">
    </div>
</div>
</form>

END;
?>