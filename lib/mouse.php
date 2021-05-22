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

/* autoloader, now we can instantiate model classes in controller without using "require". Models path define in the controller */
spl_autoload_register(function ($model_class_name){
    require MODELS."/".strtolower($model_class_name).".php";
});

/**** Custom Exceptions ****/
// class RenderException extends Exception
// {
//     public function __construct($message) {
// 	    $message = "MOUSE: ".$message;
//         parent::__construct($message, 0, null);
//     }
// }

class Mouse{
   private static $instance;
   private static $route_found = false;
   private $route = "";
   private $messages = array();
   private $method = "";

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
      $this->route_segments = explode("/",trim($this->route,"/"));        
   }

   public function accepts($accept="text/html") {
      $accept_header = "";

      if(!empty($_SERVER['HTTP_ACCEPT'])){
         $accept_header = strtolower($_SERVER['HTTP_ACCEPT']);
      }

      if(!empty($accept_header)){
         $accept = str_replace("/","\/",$accept);
         $accept = str_replace("+","\+",$accept);

         if(preg_match("/{$accept}/i",$accept_header)) {
            return true;
         }
      }
      return false;
   }    

   /****** Ajax (XHR) methods ******/
   public function is_xhr(){
      if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
         return true;
      } 
      return false; 
   }

   public function is_json(){
      if($this->is_xhr() && $this->accepts("application/json")) {
         return true;
      }
      return false;
   }
  
  /****** end Ajax methods ******/

   public function get_route(){
      return $_SERVER['REQUEST_URI'];  
   }

   public static function register($route, $callback, $method) {
      if(!static::$route_found){
         $application = static::get_instance();
         $url_parts = explode("/",trim($route,"/"));
         $matched = null;
      
         if(count($application->route_segments) == count($url_parts)) {
            foreach($url_parts as $key=>$part) {
               if(strpos($part,":") !== false) {    
                  //This means we have a route variable

                  //Reject if URI segment is empty? e.g. /admin/user/12. is /admin//12. Invalid URI.
                  if(empty($application->route_segments[$key])) {
                     $matched = false;
                     break;
                  }

                  if(strpos($part,";") !== false) {
                     //means we have a regex
                     $parts = explode(";",trim($part," "));

                     if(count($parts) === 2) {                    
                        if(!preg_match("/^{$parts[1]}$/",$application->route_segments[$key])) {
                           // Regex failed, invalid route.
                           $matched = false;
                           break;
                        }
                     }
                     $part = $parts[0];
                  }
                  $application->route_variables[substr($part,1)] = $application->route_segments[$key];
                  $matched = true;
               } else {
                  //Means we do not have a route variable
                  if($part == $application->route_segments[$key]) {
                     if(!$matched) {
                        $matched = true;
                     }
                  } else {
                     //Means routes don't match
                     $matched = false;
                     break;
                  }
               }
            }
         } else {
            //The routes have different sizes i.e. they don't match
            $matched = false;
         }

         if(!$matched || $application->method != $method) {
            if(!$matched) {
               $matcher = "NULL";
            }
            return false;
         } else {
            static::$route_found = true;
            echo $callback($application);
         }
      }
   }
   
   /**
    * Returns the value of the route in route_variables
    *
    * @param  string $key
    * @return string
    */
   public function route_var($key) {
      return $this->route_variables[$key];
   }
    
   public function render($layout, $content) {

      foreach($this->messages As $key => $val) {
         $$key = $val;
      }
      

      $flash = $this->get_flash();

      $content = VIEWS."templates/{$content}.php";
      
      if(!empty($layout)) {
         require VIEWS."templates/{$layout}.layout.php";
      } else {
         // AJAX response.
         header('Content-Type: application/json');
         require $content;
         echo json_encode($data);
      }
      exit();
   }

   public function get_request() {
      return $_SERVER['REQUEST_URI'];
   }
    
   public function is_https(){
      if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on') {
         return true;
      }
      return false;
   }

   public function force_to_https($path="/") {
      if(!$this->is_https()){
         $host = $_SERVER['HTTP_HOST'];
         $redirect_to_path = "https://".$host.$path;
         $this->redirect_to($redirect_to_path);
         exit();
      }
   }

   public function force_to_http($path="/") {
      if($this->is_https()){
         $host = $_SERVER['HTTP_HOST'];
         $redirect_to_path = "http://".$host.$path;
         $this->redirect_to($redirect_to_path);
         exit();
      }
   }

   public function get_method() {
      $request_method = "GET";
      
      if(!empty($_SERVER['REQUEST_METHOD'])) {
         $request_method = strtoupper($_SERVER['REQUEST_METHOD']);
      }
            
      if($request_method === "POST"){
         if(strtoupper($this->form("_method")) === "POST") {
            return "POST";
         }
         if(strtoupper($this->form("_method")) === "PUT") {
            return "PUT";
         }
         if(strtoupper($this->form("_method")) === "DELETE") {
            return "DELETE";
         }   
         return "POST";
      }
      if($request_method === "PUT") {
         return "PUT";
      }

      if($request_method === "DELETE") {
         return "DELETE";
      }           

      return "GET";
   }
    
   /**
    * Gets sanitised value from $_POST[]
    *
    * Data types:
    *
    * str, email
    * 
    * @param string $key Key of value to return.
    * @param string $datatype Specify type of data.
    *
    * @return string Returns empty string if key not found.
    */
   public function form($key, $datatype = "") {
      if(!empty($_POST[$key])){
         switch ($datatype) {
            case "email":
               $value = filter_input(INPUT_POST, $key, FILTER_SANITIZE_EMAIL);
               break;
            default:
               $value = sanitise_str($_POST[$key]);
               break;
         }
         return $value;
      }
      return "";
   }

   public function redirect_to($path="/") {
      header("Location: {$path}");
      exit();
   }

    public function set_session_message($key,$message) {
       if(!empty($key) && !empty($message)) {
          session_start();
          $_SESSION[$key] = $message;
          session_write_close();
       }
    }
    
    /**
     * Returns value stored in $_SESSION[] given the **$key**.
     *
     * @param string $key
     * @return string $value
     */
    public function get_session_message($key) {
      $msg = "";
      if(!empty($key) && is_string($key)) {
         session_start();
         if(!empty($_SESSION[$key])) {
            $msg = $_SESSION[$key];
         }
         session_write_close();
      }
      return $msg;
    }
    
   /**
    * Pops value stored in $_SESSION[] given the **$key**.
    *
    * @param string $key
    * @return string $value
    */
   public function pop_session_message($key) {
      $msg = "";
      if(!empty($key) && is_string($key)) {
         session_start();
         if(!empty($_SESSION[$key])) {
            $msg = $_SESSION[$key];
            unset($_SESSION[$key]);
         }
         session_write_close();
      }
      return $msg;
   }
    
   /**
    * Sets the message 'flash' to the given value.
    *
    * @param  mixed $msg
    * @return void
    */
   public function set_flash($msg) {
         $this->set_session_message("flash",$msg);
   }

   public function get_flash() {
         return $this->pop_session_message("flash");   
   }
    

   public function set_message($key,$value) {
      $this->messages[$key] = $value;
   }

 
   public static function resolve() {
      if(!static::$route_found) {
         $app = static::get_instance();
         navbar_init($app);                              // Set variables for navbar if logged in.
         header("HTTP/1.0 404 Not Found");
         $app->set_message("title", "404 Page not Found");
         $app->render("standard","404");
      }
   }

   /* if a complex route matches, it may not be the type (e.g. numeric) we want. We may have to reset route_found to false, so the next callback can have an opportunity to handle the request. */
   public function reset_route() {
       static::$route_found	= false;
   }

   public function set_csrftoken() {
      $token = hash("md5", uniqid(mt_rand(), true));
      $this->set_session_message("csrf_token", $token);
      $this->set_message("csrf_token", $token);
      return $token;
   }

   public function check_csrftoken($form_token): bool {
      $server_token = $this->get_session_message("csrf_token");
      if ($server_token === $form_token) {
         return true;
      }
      return false;
   }
}




