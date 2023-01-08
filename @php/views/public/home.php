<?php
$ROUTER = $GLOBALS['ROUTER'];

return<<<END
<div style="background-image: url(images/homebg.jpg); background-position: center; background-size: cover; width:100%; height:calc(100vh - 90px);">
    <div class="position-absolute top-50 start-50 translate-middle bg-primary opacity-75 rounded-4">
        <h1 class="text-light p-4 text-center">Profesjonalne usługi dla twoich włosów</h1>
        <div class="text-center text-light fs-1">
            <i class="bi bi-scissors center"></i>&nbsp;<i class="bi bi-scissors center"></i>&nbsp;<i class="bi bi-scissors center"></i>
        </div>
        <h5 class="text-light p-5 pb-4 text-center lh-base">Fryzjer stylista, już przed wzięciem do ręki nożyczek, potrafi zaplanować najlepszą fryzurę dla naszego Klienta. Ustala to na podstawie indywidualnych rysów i cech twarzy. Fryzura łagodzi wizerunek, dodać mu "pazura", a nawet odmłodzić o kilka lat.</h5>
        <div class="text-center ">
            {$ROUTER->GetHtmlLink("client_book_appointment", "Umów się na wizytę","btn btn-primary bg-secondary text-dark mb-4")}
        </div>
    </div>
</div>
END


?>