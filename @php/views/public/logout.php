<?php
    $ROUTER = $GLOBALS['ROUTER'];

    session_unset();
    session_destroy();

    $ROUTER->RedirectToPage("home_page");
    return <<<END
    Wylogowano
    END;
?>
