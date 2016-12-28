<?php

class Soon16 {


  function request(){
    global $sql;

    $date     = date('Y-m-d H:i:s');
    $name     = escape($_POST['name']);
    $age      = escape($_POST['age']);
    $gender   = escape($_POST['gender']);
    $address  = escape($_POST['address']);
    $register = escape($_POST['register']);
    $needRide = escape($_POST['needRide']);
    $canRide  = escape($_POST['canRide']);
    $register = $register=='' ? null : (int)(bool)$register;
    $needRide = $needRide=='' ? null : (int)(bool)$needRide;
    $canRide  = $canRide==''  ? null : (int)(bool)$canRide;

    $query = "INSERT INTO `soon_registration` SET `requested_date`='{$date}', `name`='{$name}', `age`='{$age}', `gender`='{$gender}', `register`={$register}, `address`='{$address}', `need_ride`={$needRide}";
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
        $address  = escape($_POST['address'][$i]);
        $participation = escape($_POST['participation'][$i]);


        $query = "UPDATE `soon_registration` SET `name`='{$name}', `age`='{$age}', `gender`='{$gender}', `address`='{$address}', `participation`='{$participation}' WHERE `id`={$id} ";

        $sql->query($query);
      }
    }

    // List
    $result = $sql->query("SELECT * FROM soon_registration ORDER BY requested_date DESC");
    $data=[];

    while($row = $result->fetch_assoc()){
      $e = new StdClass();
      $e->id = (int)$row['id'];
      $e->date = date('Y/m/d', strtotime($row['requested_date']));
      $e->name = $row['name'];
      $e->age = $row['age'];
      $e->gender = $row['gender'];
      $e->address = $row['address'];
      $e->register = (bool)$row['register'] ? '예' : '아니요';
      $e->needride = (bool)$row['need_ride'] ? '예' : '아니요';
      $e->participation = $row['participation'];
      if(is_null($row['can_ride'])){
        $e->canride = '';
      } else {
        $e->canride = (bool)$row['can_ride'] ? '예' : '아니요';
      }
      $data[] = $e;
    }

    view('soon-16/list.phtml', compact('data'));
  }


  function download(){
    global $sql;

    $result = $sql->query("SELECT * FROM soon_registration ORDER BY requested_date DESC");
    $data=[];

    while($row = $result->fetch_assoc()){
      $e = new StdClass();
      $e->id = (int)$row['id'];
      $e->date = date('Y/m/d', strtotime($row['requested_date']));
      $e->name = $row['name'];
      $e->age = $row['age'];
      $e->gender = $row['gender'];
      $e->address = $row['address'];
      $e->register = (bool)$row['register'] ? '예' : '아니요';
      $e->needride = (bool)$row['need_ride'] ? '예' : '아니요';
      $e->participation = $row['participation'];
      if(is_null($row['can_ride'])){
        $e->canride = '';
      } else {
        $e->canride = (bool)$row['can_ride'] ? '예' : '아니요';
      }
      $data[] = $e;
    }

    $export_file = "soon_16_list.xls";
    ob_end_clean();
    ini_set('zlib.output_compression','Off');

    header('Pragma: public');
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");                 // Date in the past
    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
    header("Pragma: no-cache");
    header("Expires: 0");
    header('Content-Transfer-Encoding: none');
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');                // This should work for IE & Opera
    header("Content-type: application/x-msexcel; charset=UTF-8");                    // This should work for the rest
    header('Content-Disposition: attachment; filename="'.basename($export_file).'"');

    echo chr(255) . chr(254);
    echo mb_convert_encoding("신청일자\t이름\t나이\t성별\t순신청\t라이드필요\t라이드가능\t참여도\n", 'UTF-16LE', 'UTF-8');
    foreach($data as $row){
      echo mb_convert_encoding("{$row->date}\t{$row->name}\t{$row->age}\t{$row->gender}\t{$row->register}\t{$row->needride}\t{$row->canride}\t{$row->participation}\n", 'UTF-16LE', 'UTF-8');
    }

  }


  function random(){
    global $sql;
    set_time_limit(0);

    $result = $sql->query("SELECT * FROM soon_registration");
    $data=[];

    while($row = $result->fetch_assoc()){
      $e = new StdClass();
      $e->id = (int)$row['id'];
      $e->name = $row['name'];
      $e->age = (int)date('Y') - (int)('19'.$row['age']);
      $e->gender = $row['gender'];
      $e->address = $row['address'];
      $e->register = (bool)$row['register'] ? '예' : '아니요';
      $e->needride = (bool)$row['need_ride'] ? '예' : '아니요';
      $e->participation = $row['participation'];
      if(is_null($row['can_ride'])){
        $e->canride = '';
      } else {
        $e->canride = (bool)$row['can_ride'] ? '예' : '아니요';
      }
      $data[] = $e;
    }

    view('soon-16/random.phtml', compact('data'));
  }



}