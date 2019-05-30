<?php
  
namespace Base;

use Base\Request;

class BaseController 
{

  public static function guard($file='', $parameters = array()){
    if(!empty($file)){
      if (file_exists("app/Http/Guards/".$file.".php")){
        include("app/Http/Guards/".$file.'.php');
        $middleware = 'App\Http\Guards\\'.str_replace('/', '\\', $file);
        $class = new $middleware($parameters);
      }
      else{ 
        throw new \Exception("Guard &quot;".$file.".php&quot; not found!");
      }
    }
  }

    // Fucntion for getting config
  public static function config($location='')
  {
    $locationArray =  explode(".",$location);

    $file = 'config';
    foreach ($locationArray as $loc) {
      $file .= '/'.$loc; 
    }
    $file .= '.php';

    if(file_exists($file)){
      $config = include($file);
      return $config;
    }
    else{
      throw new \Exception('Configuration file '.$_file.' not found!');
    }
  }

    // Function for generating view
  public static function view($_location='', $_data=array())
  {
    $_location_array =  explode(".",$_location);

    $_file = 'resources/views';
    foreach ($_location_array as $loc) {
      $_file .= '/'.$loc; 
    }
    $_file .= '.php';

    if(!empty($_data)){
      extract($_data);
    }

    if(file_exists($_file)){

      if(isset($_SESSION['processing_token'])){
        include($_file);
        unset($_SESSION['processing_token']);
      }
      else{
        $generated_token = bin2hex(random_bytes(32));
        $_SESSION['tokens'][$generated_token] = ['url' => $_SERVER['REQUEST_URI'], 'time' => time(), 'csrf_token' => $generated_token];
        include($_file);
      }
      
    }
    else{
      throw new \Exception('View '.$_file.' not found!');
    }
  }

    // Function for redirecting to location
  public static function redirect($route_url, $parameters= array())
  {

    $link = APP_URL.'/'.$route_url;

    if(!empty($parameters)){
      $link .= '/?';
      $count = 1;
      foreach($parameters as $key=>$value){
        if($count > 1){
          $link .= '&';
        }
        $link .= $key.'='.$value;
        $count++;
      }
    }

    header("Location: ".$link); 
    exit();

  }

    // Function for showing error
  public static function abort($err_type = 404, $message = 'The page you are looking for is not found!'){

    $err_file = 'resources/views/errors/'.$err_type.'.php';

    if(file_exists($err_file)){      
      include($err_file);   
      die();   
    }
    else{
      throw new \Exception($err_type.' | '.$message);
    }

  }

}

?>