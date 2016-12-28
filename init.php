<?php

$sql = new MySqli( DB_HOST, DB_USER, DB_PASS );
if($sql->connect_error){
  die('Connection failed: ' . $sql->connect_error);
}

$sql->set_charset('utf8');
$sql->select_db(DB_DB);
date_default_timezone_set ('America/New_York');



function escape($str){
  global $sql;
  return trim($sql->escape_string($str));
}


function view($template, $data){
  extract($data);
  require __DIR__ . '/template/' . $template;
}




