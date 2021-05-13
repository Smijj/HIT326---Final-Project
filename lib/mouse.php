<?php
/* NEW added third argument "GET" */
function get($route,$callback){
    Mouse::register($route,$callback,"GET");
}

/* NEW added next three functions */
function post($route,$callback){
    Mouse::register($route,$callback,"POST");
}

function put($route,$callback){
    Mouse::register($route,$callback,"PUT");
}

function delete($route,$callback){
    Mouse::register($route,$callback,"DELETE");
}

function resolve(){
    Mouse::resolve();
}

/* autoloader, now we can instantiate model clases in controller without using "require". Models path define in the controller */
spl_autoload_register(function ($model_class_name){
    require MODELS."/".strtolower($model_class_name).".php";
});


class Mouse{
    private static $instance;
    private static $route_found = false;
    private $route = "";
    private $messages = array();
    private $method = "";

    /* 2 NEW members for complex routes */
    private $route_segments = array();
    private $route_variables = array();

    
    public static function get_instance(){
        if(!isset(self::$instance)){
            self::$instance = new Mouse();
        }
        return self::$instance;
    }
    
    protected function __construct(){
        $this->route = $this->get_route();
        $this->method = $this->get_method();
        
        /* NEW for complex routes */
        $this->route_segments = explode("/",trim($this->route,"/"));        
    }
    
    public function get_route(){
      return $_SERVER['REQUEST_URI'];  
    }
 

    /* Modified register method to handle complex routes */
    
    public static function register($route,$callback, $method){
       if(!static::$route_found){
          $application = static::get_instance();
          $url_parts = explode("/",trim($route,"/"));
          $matched = null;
          if(count($application->route_segments) == count($url_parts)){
             foreach($url_parts as $key=>$part){
                if(strpos($part,":") !== false){
                   //This means we have a route variable
                   $application->route_variables[substr($part,1)] = $application->route_segments[$key];
                }
                else{
                  //Means we do not have a route variable
                  if($part == $application->route_segments[$key]){
                      if(!$matched){
                         $matched = true;
                      }
                  }
                  else{
                     //Means routes don't match
                     $matched = false;
                  }           
                }
             }
          }
          else{
            //The routes have different sizes i.e. they don't match
            $matched = false;
          }
          if(!$matched || $application->method != $method){
            return false;
          }
          else{
            static::$route_found = true;
            echo $callback($application);
          }
       }     
    }   

    /* New function which accepts $key and returns the value of the route in route_variables*/
    public function route_var($key){
        return $this->route_variables[$key];
    }
    
    public function render($layout, $content){

       foreach($this->messages As $key => $val){
            $$key = $val;
       }
       

       $flash = $this->get_flash();

       $content = VIEWS."/partials/{$content}.php";

       if(!empty($layout)){
          require VIEWS."/templates/{$layout}.layout.php";
       }
       else{
          // What is this part for? When would we not need a layout? Think about it.
       }
       exit();
    }

    public function get_request(){
      return $_SERVER['REQUEST_URI'];
    }
    
    public function is_https(){
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'){
            return true;
        }
        return false;
    }

    public function force_to_https($path="/"){
        if(!$this->is_https()){
           $host = $_SERVER['HTTP_HOST'];
           $redirect_to_path = "https://".$host.$path;
           $this->redirect_to($redirect_to_path);
           exit();
        }
    }

    public function force_to_http($path="/"){
        if($this->is_https()){
           $host = $_SERVER['HTTP_HOST'];
           $redirect_to_path = "http://".$host.$path;
           $this->redirect_to($redirect_to_path);
           exit();
        }
    }

    public function get_method(){
       $request_method = "GET";
       
       if(!empty($_SERVER['REQUEST_METHOD'])){
             $request_method = strtoupper($_SERVER['REQUEST_METHOD']);
       }
              
       if($request_method === "POST"){
           if(strtoupper($this->form("_method")) === "POST"){
              return "POST";
           }
           if(strtoupper($this->form("_method")) === "PUT"){
              return "PUT";
           }
           if(strtoupper($this->form("_method")) === "DELETE"){
              return "DELETE";
           }   
          return "POST";
       }
       if($request_method === "PUT"){
            return "PUT";
       }

       if($request_method === "DELETE"){
            return "DELETE";
       }           
 
       return "GET";
    }
    
    /**
     * Gets value from $_POST
     * 
     * @param string $key [explicite description]
     *
     * @return string Returns false if key not found
     */
    public function form($key){
       if(!empty($_POST[$key])){
          return $_POST[$key];
       }
       return false;
    }

    public function redirect_to($path="/"){
       header("Location: {$path}");
       exit();
    }

    public function set_session_message($key,$message){
       if(!empty($key) && !empty($message)){
          session_start();
          $_SESSION[$key] = $message;
          session_write_close();
       }
    }

    public function get_session_message($key){
       $msg = "";
       if(!empty($key) && is_string($key)){
          session_start();
          if(!empty($_SESSION[$key])){
             $msg = $_SESSION[$key];
             unset($_SESSION[$key]);
          }
          session_write_close();
       }
       return $msg;
    }

    public function set_flash($msg){
          $this->set_session_message("flash",$msg);
    }

    public function get_flash(){
          return $this->get_session_message("flash");   
    }
    

    public function set_message($key,$value){
      $this->messages[$key] = $value;
    }

 
    public static function resolve(){
      if(!static::$route_found){
        $application = static::get_instance();
        header("HTTP/1.0 404 Not Found");
        $application->render("standard","404");
      }
    }

   /* if a complex route matches, it may not be the type (e.g. numeric) we want. We may have to reset route_found to false, so the next callback can have an opportunity to handle the request. */
   public function reset_route(){
       static::$route_found	= false;
   }
}




