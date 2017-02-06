<?php
require( __DIR__ . '/env.php' );
require( __DIR__ . '/init.php' );

// Routes
foreach( require( __DIR__ . '/routes.php' ) as $route => $controller ){

  if(preg_match('#'.$route.'#', $_SERVER['REQUEST_URI'])){

    $cont = explode(':', $controller);
    $class = $cont[0];
    $method = $cont[1];
    $path = __DIR__ . '/controller/' . $class . '.php';

    if(file_exists($path)){
      require_once $path;

      if(method_exists($class, $method)){
        $stdClass = new $class();
        $stdClass->$method();
      }
    }

    $sql->close();
    exit;
  }
}



// Default Route
require_once __DIR__ . '/controller/App.php';
$app = new App();
$app->home();