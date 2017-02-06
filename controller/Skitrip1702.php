<?php
class Skitrip1702 {

  function apply(){
    global $sql;

    $date     = date('Y-m-d H:i:s');
    $name     = escape($_POST['name']);
    $city     = escape($_POST['city']);
    $whichRide   = escape($_POST['whichRide']);
    $haveEquipment    = escape($_POST{'haveEquipment'});
    $needTicket     = escape($_POST['needTicket']);
    $joinDinner = escape($_POST['joinDinner']);
    $needRide = escape($_POST['needRide']);
    $canRide  = escape($_POST['canRide']);
    $needRide = $needRide=='' ? null : (int)(bool)$needRide;
    $canRide  = $canRide==''  ? null : (int)(bool)$canRide;

    $query = "INSERT INTO `skitrip1702_registration` SET `requested_date`='{$date}', `name`='{$name}', `city`='{$city}', `which_ride`='{$whichRide}', `have_equipment`='{$haveEquipment}', `need_ticket`='{$needTicket}', `join_dinner`='{$joinDinner}', `need_ride`={$needRide}";
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
        $city      = escape($_POST['city'][$i]);
        $whichRide   = escape($_POST['whichRide'][$i]);
        $haveEquipment    = escape($_POST['haveEquipment'][$i]);
        $needTicket     = escape($_POST['needTicket'][$i]);
        $joinDinner = escape($_POST['joinDinner'][$i]);
        $needride = escape($_POST['needride'][$i])=='예' ? 1:0;
        $canride  = escape($_POST['canride'][$i])=='예' ? 1:0;


        $query = "UPDATE `skitrip1702_registration` SET `name`='{$name}', `city`='{$city}', `which_ride`='{$whichRide}', `have_equipment`='{$haveEquipment}', `need_ticket`='{$needTicket}', `join_dinner`='{$joinDinner}', `need_ride`={$needride}, `can_ride`={$canride} WHERE `id`={$id} ";

        $sql->query($query);

      }
    }

    // List
    $result = $sql->query("SELECT * FROM skitrip1702_registration ORDER BY requested_date DESC");
    $data=[];

    while($row = $result->fetch_assoc()){
      $e = new StdClass();
      $e->id = (int)$row['id'];
      $e->date = date('Y/m/d', strtotime($row['requested_date']));
      $e->name = $row['name'];
      $e->city = $row['city'];
      $e->whichRide = $row['which_ride'];
      $e->haveEquipment = $row['have_equipment'];
      $e->needTicket = $row['need_ticket'];
      $e->joinDinner = $row['join_dinner'];
      $e->needride = (bool)$row['need_ride'] ? '예' : '아니요';
      if(is_null($row['can_ride'])){
        $e->canride = '';
      } else {
        $e->canride = (bool)$row['can_ride'] ? '예' : '아니요';
      }
      $data[] = $e;
    }

    view('skitrip-1702/list.phtml', compact('data'));

  }


  function download(){

  }

}
