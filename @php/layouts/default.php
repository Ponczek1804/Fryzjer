
<?php


function layout_default($title, $view){
    $ROUTER = $GLOBALS['ROUTER'];

    if (isset($_SESSION['user']) && $_SESSION['user']['admin'] == 1) {
        $navbar = <<<END
            <li class="nav-item">{$ROUTER->GetHtmlLink("home_page", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("admin_home", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("price_list", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("log_out", null, "nav-link")}</li>
            END;
        }
        else if(isset($_SESSION['user'])){
            $navbar = <<<END
            <li class="nav-item">{$ROUTER->GetHtmlLink("home_page", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("client_home", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("price_list", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("client_edit_data", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("client_book_appointment", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("log_out", null, "nav-link")}</li>
            END;
        }
        else{
            $navbar = <<<END
            <li class="nav-item">{$ROUTER->GetHtmlLink("home_page", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("price_list", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("login", null, "nav-link")}</li>
            <li class="nav-item">{$ROUTER->GetHtmlLink("register", null, "nav-link")}</li>
        END;
    }
    
    return<<<END
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{$ROUTER->GetFullUrl('/css/bootstrap.css')}">
        <link rel="stylesheet" href="{$ROUTER->GetFullUrl('/css/bootstrap-icons.css')}">
        <title>$title</title>
    </head>
    <body style="min-height: calc(100vh - 90px);">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top border-bottom">
            <div class="container-fluid">
                {$ROUTER->GetHtmlLink("home_page", "TwojFryzjer", "navbar-brand")}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarColor02">
                <ul class="navbar-nav me-auto">
                    $navbar
                </ul>
                </div>
            </div>
        </nav>
        <main role="main" style="margin-top: 90px;">
            $view
        </main>
        <footer class="border-top footer text-muted bg-primary fixed-bottom p-2">
            <div class="container text-end">
                &copy; 2022 - Karol Bródka, Jędrzej Rusak, Marcin Dołżański, Adam Ciesielski
            </div>
        </footer>
        
        <script src="{$ROUTER->GetFullUrl("/css/bootstrap.bundle.min.js")}"></script>
    </body>
    </html>
    END;
}
?>


