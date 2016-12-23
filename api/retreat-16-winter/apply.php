<?php
$sql = new MySqli('localhost', 'admin', '777');
if($sql->connect_error){
  die('Connection failed: ' . $sql->connect_error);
}
$sql->set_charset('utf8');
$sql->select_db('mybm');

date_default_timezone_set ('America/New_York');
$date     = date('Y-m-d H:i:s');
$name     = escape($_POST['name']);
$age      = escape($_POST['age']);
$gender   = escape($_POST['gender']);
$phone    = escape($_POST{'phone'});
$size     = escape($_POST['size']);
$needRide = escape($_POST['needRide']);
$canRide  = escape($_POST['canRide']);
$needRide = $needRide=='' ? null : (int)(bool)$needRide;
$canRide  = $canRide==''  ? null : (int)(bool)$canRide;

//var_dump($date, $name, $age, $gender, $phone, $size, $needRide, $canRide);

$query = "INSERT INTO `retreat16winter_registration` SET `requested_date`='{$date}', `name`='{$name}', `age`='{$age}', `gender`='{$gender}', `phone`='{$phone}', `size`='{$size}', `need_ride`={$needRide}";
if(!is_null($canRide)){
  $query .= ", `can_ride`={$canRide}";
}
$query .= ";";

$result = $sql->query($query);

if($result){
  echo 'ok';
}
else {
  echo mysqli_error($sql);
  echo $query;

}

$sql->close();


function escape($str){
  global $sql;
  return trim($sql->escape_string($str));
}




