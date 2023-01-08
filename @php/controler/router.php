<?php



class Router{
    // adres bazowy ktory, gdy ztrona nie znajduje się w głównym katalogu na serwerze
    private $base_url;

    // sciezka do folderu z layoutami
    private $layouts_path;

    // sciezka do folderu z widokami
    private $views_path;

    // domyslny routing gdy nie znajdzie strony
    private $default_route;

    // tablica asjocacyjna przechowujaca podstawowe dane o routingu
    private $routes;


    function __construct($base_url, $layouts_path, $views_path, $default_route){
        $this->routes = [];
        $this->base_url = $base_url;
        $this->layouts_path = $layouts_path;
        $this->views_path = $views_path;
        $this->default_route = $default_route;
    }


    public function AddRoute($name, $url, $title, Authentication $auth, $view_file, $layout_name){
        $this->routes []= [
            "name" => $name,
            "url" => $url,
            "title" => $title,
            "auth" => $auth,
            "view_file" => $view_file,
            "layout_name" => $layout_name
        ];
    }

    public function GetCurrentPage(){
        // przefiltrowanie tablicy routingu na podstawie aktualnego adresu w pasku adresu
        $current_routing = array_filter($this->routes, function ($routing_element){
            return $routing_element["url"] == $this->GetCurrentURL(); 
        });

        // jezeli nie udalo sie nic znaleźć wyświetl widok domyślny
        if(empty($current_routing)){
            $routing = $this->GetRoutingByName($this->default_route);

            // jak juz wszystko zwiedzie zróć kod błedu 404
            if(empty($routing)){
                http_response_code(404);
                die("404 Not Found");
            }
            $view = require  $this->views_path.$routing["view_file"];
            $func = $routing["layout_name"];
            return $func($routing["title"], $view);
        }

        // pobranie 1 elementu z przefiltrowanej tablicy routingu
        $current_routing = reset($current_routing);

        // funkcja sprawdzajaca czy uzytkownik ma dostep do danej strony
        // jezeli nie przekieruj do strony logowania
        authentication_check($current_routing['auth']);


        // wczytanie widoku
        $view = require  $this->views_path.$current_routing["view_file"];


        // wywołanie funkcji odpowiedzialnej za generowanie layoutu i przekazanie widoku
        $page = call_user_func($current_routing["layout_name"], $current_routing["title"], $view);

        return $page;
    }

    
    public function RedirectToPage($route_name){
        $routing = $this->GetRoutingByName($route_name);
        $url = $this->GetFullUrl($routing['url']);

        header("Location: {$url}");
    }

    public function GetHtmlLink($route_name, $link_title = null, $class = "", $link_params = ""){
        $routing = $this->GetRoutingByName($route_name);
        if ($link_title == null)
            $link_title = $routing["title"];

        if ($routing["url"] == $this->GetCurrentURL())
            return "<a class='active {$class}' href='{$this->GetFullUrl($routing["url"])}{$link_params}'>$link_title</a>";
        else
            return "<a class='$class' href='{$this->GetFullUrl($routing["url"])}{$link_params}'>$link_title</a>";
    }

    public function GetFullUrl($url){
        return $this->base_url . $url;
    }

    private function GetRoutingByName($route_name){

        $current_routing = array_filter($this->routes, function ($routing_element) use ($route_name){
            return $routing_element["name"] == $route_name; 
        });

        if (empty($current_routing))
            return [];

        $current_routing = reset($current_routing);
        return $current_routing;
    }


    private function GetCurrentURL(){
        $CURRENT_URL = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $CURRENT_URL = substr_replace($CURRENT_URL,"",0,strlen($this->base_url));

        return $CURRENT_URL;
    }


}



?>