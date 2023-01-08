<?php
$ROUTER = $GLOBALS['ROUTER'];

return<<<END
<div class="container pt-5 pb-4 g-col-6">
    <p class="text-center" > Rejestracja przebiegła pomyślnie. Teraz możesz przejść do strony logowania. </p>
    {$ROUTER->GetHtmlLink("login",null, "btn btn-primary d-grid gap-2 col-3 mx-auto")}
</div>

END;
?>