<?php
class Retreat16Winter {

  function apply(){
    global $sql;

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

  }


  function list(){
    global $sql;

    // Edit
    if(isset($_POST['id'])){
      for($i=0; $i<count($_POST['id']); $i++ ){

        $id       = (int)escape($_POST['id'][$i]);
        $name     = escape($_POST['name'][$i]);
        $age      = escape($_POST['age'][$i]);
        $gender   = escape($_POST['gender'][$i]);
        $phone    = escape($_POST['phone'][$i]);
        $size     = escape($_POST['size'][$i]);
        $needride = escape($_POST['needride'][$i])=='예' ? 1:0;
        $canride  = escape($_POST['canride'][$i])=='예' ? 1:0;
        $paid     = escape($_POST['paid'][$i]);
        if(!$paid) $paid = '0';


        $query = "UPDATE `retreat16winter_registration` SET `name`='{$name}', `age`='{$age}', `gender`='{$gender}', `phone`='{$phone}', `size`='{$size}', `need_ride`={$needride}, `can_ride`={$canride}, `paid`='{$paid}' WHERE `id`={$id} ";

        $sql->query($query);

      }
    }

    // List
    $result = $sql->query("SELECT * FROM retreat16winter_registration ORDER BY requested_date DESC");
    $data=[];

    while($row = $result->fetch_assoc()){
      $e = new StdClass();
      $e->id = (int)$row['id'];
      $e->date = date('Y/m/d', strtotime($row['requested_date']));
      $e->name = $row['name'];
      $e->age = $row['age'];
      $e->gender = $row['gender'];
      $e->phone = $row['phone'];
      $e->size = $row['size'];
      $e->needride = (bool)$row['need_ride'] ? '예' : '아니요';
      if(is_null($row['can_ride'])){
        $e->canride = '';
      } else {
        $e->canride = (bool)$row['can_ride'] ? '예' : '아니요';
      }
      $e->paid = $row['paid'];
      $data[] = $e;
    }

    view('retreat-16-winter/list.phtml', compact('data'));

  }


  function download(){

  }

}
