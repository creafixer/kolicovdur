<?php
/**
 * Yönlendirici Sınıf
 */
class Router {
    private $routes = [];
    private $params = [];
    
    /**
     * Yeni bir rota ekle
     * 
     * @param string $route Rota deseni
     * @param string $controller Controller@method formatında
     * @return void
     */
    public function add($route, $controller) {
        // Rota desenini düzenli ifadeye dönüştür
        $pattern = $this->convertRouteToRegex($route);
        
        // Controller ve method adını ayır
        list($controllerName, $action) = explode('@', $controller);
        
        // Rotayı kaydet
        $this->routes[$pattern] = [
            'controller' => $controllerName,
            'action' => $action
        ];
    }
    
    /**
     * Rota desenini düzenli ifadeye dönüştür
     * 
     * @param string $route Rota deseni
     * @return string Düzenli ifade
     */
    private function convertRouteToRegex($route) {
        // URL parametrelerini bul ve düzenli ifade ile değiştir
        $route = preg_replace('/\{([a-z_]+)\}/', '(?P<\1>[^/]+)', $route);
        
        // Başlangıç ve bitiş sınırlayıcıları ekle
        $route = '/^' . str_replace('/', '\/', $route) . '$/i';
        
        return $route;
    }
    
    /**
     * İsteği uygun rotaya eşleştir
     * 
     * @param string $url İstek URL'i
     * @return bool Eşleşme durumu
     */
    public function match($url) {
        // URL'den parametreleri temizle
        $url = parse_url($url, PHP_URL_PATH);
        $url = trim($url, '/');
        
        // Boş URL ana sayfaya yönlendir
        if ($url === '') {
            $url = '/';
        } else {
            $url = '/' . $url;
        }
        
        // Tüm rotaları kontrol et
        foreach ($this->routes as $pattern => $params) {
            if (preg_match($pattern, $url, $matches)) {
                // Parametreleri ayıkla
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                
                $this->params = $params;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * İsteği işle ve uygun controller'a yönlendir
     * 
     * @return void
     */
    public function dispatch() {
        // URL'i al
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        
        // URL'i eşleştir
        if ($this->match($url)) {
            // Controller sınıfını oluştur
            $controllerName = $this->params['controller'];
            $controllerFile = CONTROLLER_DIR . '/' . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                $controller = new $controllerName();
                
                // Action metodu çağır
                $action = $this->params['action'];
                
                if (method_exists($controller, $action)) {
                    // Parametreleri hazırla
                    $actionParams = [];
                    foreach ($this->params as $key => $value) {
                        if ($key !== 'controller' && $key !== 'action') {
                            $actionParams[$key] = $value;
                        }
                    }
                    
                    // Metodu çağır
                    call_user_func_array([$controller, $action], $actionParams);
                } else {
                    // Metot bulunamadı
                    $this->renderError('Metot bulunamadı: ' . $action, 404);
                }
            } else {
                // Controller bulunamadı
                $this->renderError('Controller bulunamadı: ' . $controllerName, 404);
            }
        } else {
            // Rota bulunamadı
            $this->renderError('Sayfa bulunamadı', 404);
        }
    }
    
    /**
     * Hata sayfası göster
     * 
     * @param string $message Hata mesajı
     * @param int $code HTTP durum kodu
     * @return void
     */
    private function renderError($message, $code) {
        http_response_code($code);
        
        // Hata görünümünü yükle
        include VIEW_DIR . '/layout/header.php';
        include VIEW_DIR . '/error.php';
        include VIEW_DIR . '/layout/footer.php';
        
        exit;
    }
}