<?php
$sql = new MySqli('localhost', 'admin', '777');
if($sql->connect_error){
  die('Connection failed: ' . $sql->connect_error);
}

$sql->set_charset('utf8');
$sql->select_db('mybm');
date_default_timezone_set ('America/New_York');


if(isset($_POST['id'])){
  for($i=0; $i<count($_POST['id']); $i++ ){

    $id       = (int)escape($_POST['id'][$i]);
    $name     = escape($_POST['name'][$i]);
    $age      = escape($_POST['age'][$i]);
    $gender   = escape($_POST['gender'][$i]);
    $address  = escape($_POST['address'][$i]);


    $query = "UPDATE `soon_registration` SET `name`='{$name}', `age`='{$age}', `gender`='{$gender}', `address`='{$address}' WHERE `id`={$id} ";

    $sql->query($query);
  }
}

function escape($str){
  global $sql;
  return trim($sql->escape_string($str));
}





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
  if(is_null($row['can_ride'])){
    $e->canride = '';
  } else {
    $e->canride = (bool)$row['can_ride'] ? '예' : '아니요';
  }
  $data[] = $e;
}

$sql->close();

if(!$download) : ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>브릿지메이커스: 신청현황</title>
</head>

<body>

<form action="/list" method="post">
  <button type="submit">저장</button>
  <table width="100%" cellpadding="5" border="1" cellspacing="0">
    <thead>
      <tr>
        <th align="left" valign="top">신청일자</th>
        <th align="left" valign="top">이름</th>
        <th align="left" valign="top">나이</th>
        <th align="left" valign="top">성별
        <th align="left" valign="top">사는곳</th>
        <th align="left" valign="top">순신청</th>
        <th align="left" valign="top">라이드필요</th>
        <th align="left" valign="top">라이드가능</th>
      </tr>
    </thead>

    <tbody>
      <?php $i=0;foreach($data as $row):?>
      <tr>
        <input type="hidden" name="id[<?=$i?>]" value="<?=$row->id?>" />
        <td><input type="text" name="date[<?=$i?>]" value="<?=$row->date?>" /></td>
        <td><input type="text" name="name[<?=$i?>]" value="<?=$row->name?>" /></td>
        <td><input type="text" name="age[<?=$i?>]" value="<?=$row->age?>" /></td>
        <td><input type="text" name="gender[<?=$i?>]" value="<?=$row->gender?>" /></td>
        <td><input type="text" name="address[<?=$i?>]" value="<?=$row->address?>" /></td>
        <td><input type="text" name="register[<?=$i?>]" value="<?=$row->register?>" /></td>
        <td><input type="text" name="needride[<?=$i?>]" value="<?=$row->needride?>" /></td>
        <td><input type="text" name="canride[<?=$i?>]" value="<?=$row->canride?>" /></td>
      </tr>
      <?php $i++;endforeach;?>
    </tbody>

  </table>
</form>

<div>총 <?=count($data)?>명</div>

<style>
  button {
    cursor: pointer;
    padding: 10px 20px;
    font-size: 18px;
    margin-bottom: 20px;
  }
  input {
    width: 100%;
    font-size: 14px;
    border: none;
    outline: none;
  }
</style>

</body>





<?php else :




  $export_file = "bm_request_soon_list.xls";
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
  echo mb_convert_encoding("신청일자\t이름\t나이\t성별\t순신청\t라이드필요\t라이드가능\n", 'UTF-16LE', 'UTF-8');
  foreach($data as $row){
    echo mb_convert_encoding("{$row->date}\t{$row->name}\t{$row->age}\t{$row->gender}\t{$row->register}\t{$row->needride}\t{$row->canride}\n", 'UTF-16LE', 'UTF-8');
  }



endif;