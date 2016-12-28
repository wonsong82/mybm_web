<?php

class App {

  function home(){
    $css = defined('DEBUG') && DEBUG ?
      'http://localhost:8080/app.css' :
      '/static/app.css';
    $js = defined('DEBUG') && DEBUG ?
      'http://localhost:8080/app.js' :
      '/static/app.js';

    view('app/index.phtml', compact('css', 'js'));
  }
}