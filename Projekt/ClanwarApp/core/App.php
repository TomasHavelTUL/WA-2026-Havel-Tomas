<?php
/**
 * Frontový řadič — parsuje URL ve tvaru controller/metoda/param1/param2
 * a deleguje volání na příslušný kontroler.
 */
class App {
    protected $controller = 'ClanwarController';
    protected $method     = 'index';
    protected $params     = [];

    /**
     * Načte URL, zjistí kontroler a metodu, a zavolá ji s parametry.
     * Pokud kontroler nebo metoda neexistuje, použije výchozí hodnoty
     * (ClanwarController::index).
     */
    public function __construct() {
        $url = $this->parseUrl();

        // Konvence: segment[0] je název kontroleru bez přípony "Controller"
        if (isset($url[0]) && file_exists('../app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Zbývající segmenty jsou parametry metody (např. ID záznamu)
        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Rozloží GET parametr "url" na pole segmentů rozdělených lomítkem.
     * Odstraní trailing lomítko a sanitizuje hodnotu přes FILTER_SANITIZE_URL.
     *
     * @return array Segmenty URL, nebo prázdné pole pokud parametr chybí.
     */
    public function parseUrl(): array {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
