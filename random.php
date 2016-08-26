<?php
set_time_limit(0);

$sql = new MySqli('localhost', 'admin', '777');
if($sql->connect_error){
  die('Connection failed: ' . $sql->connect_error);
}

$sql->set_charset('utf8');
$sql->select_db('mybm');
date_default_timezone_set ('America/New_York');


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

$sql->close();

echo '<pre>';
//print_r($data);


// 지정된 사람들
$team1 = [
  $data[2] // 백승주
];
$team2 = [
  $data[3] // 김소연
];
$team3 = [
  $data[7] // 권수현
];
$team4 = [
  $data[29] // 송원철
];


$avgAge = 0;
foreach($data as $person) $avgAge += $person->age;
$avgAge /= count($data);
$avgAge = floor($avgAge);

$totalGender = getGenderCount($data);
$avgMale = floor($totalGender->male / 4);
$avgFemale = floor($totalGender->female / 4);

$totalRideRequire = getRideRequireCount($data);
$avgRideNeed = floor($totalRideRequire->need / 4);
$avgRideNotNeed = floor($totalRideRequire->notNeed / 4);

$totalCanRide = getCanRideCount($data);
$avgCanRide = floor($totalCanRide / 4);

$totalParticipation = getParticipationCount($data);
$avgParticipationHigh = floor($totalParticipation->high / 4);
$avgParticipationMid = floor($totalParticipation->mid / 4);
$avgParticipationLow = floor($totalParticipation->low / 4);



// 지정된 사람들 없앰
foreach([$team1, $team2, $team3, $team4] as $team){
  removePremades($data, $team);
}
function removePremades( &$data, $team ){
  foreach($team as $person){
    $index = array_search($person, $data);
    array_splice($data, $index, 1);
  }
}


$ok = false;
$iterationCount = 0;


do {
  $iterationCount++;

  // 클론 데이타들
  $d = []; $t1 = []; $t2 = []; $t3 = []; $t4 = [];
  $d  = array_merge($d,  $data);
  $t1 = array_merge($t1, $team1);
  $t2 = array_merge($t2, $team2);
  $t3 = array_merge($t3, $team3);
  $t4 = array_merge($t4, $team4);

  // 랜돔
  shuffle($d);

  // 4개로 나눔
  $numPerTeam = floor(count($d) / 4);
  $remainings = count($d) % 4;
  foreach ([&$t1, &$t2, &$t3, &$t4] as &$_team) {
    $cut = array_splice($d, 0, $numPerTeam);
    $_team = array_merge($_team, $cut);
  }


  // 남은사람들
  for ($i = 0; $i < count($d); $i++) {
    $teamName = 't' . ($i + 1);
    $$teamName = array_merge($$teamName, [$d[$i]]);
  }



  // 나이 체크 : 위 | 아래
  $ageThreshold = 3;

  $valid = true;
  for($i=1; $i<=4; $i++){
    $team = 't' . $i;
    $age = getAgeRangeCount($$team);
    if($age->diff > $ageThreshold ){
      $valid = false;
      break;
    }
  }
  if(!$valid) continue;



  // 성별
  $genderThreshold = 1;

  $valid = true;
  for($i=1; $i<=4; $i++){
    $team = 't' . $i;
    $gender = getGenderCount($$team);
    if(abs($gender->male - $avgMale) > $genderThreshold){
      $valid = false;
      break;
    }
    if(abs($gender->female - $avgFemale) > $genderThreshold){
      $valid = false;
      break;
    }
  }
  if(!$valid) continue;

  // 라이드 필요
  $needRideThreshold = 1;

  $valid = true;
  for($i=1; $i<=4; $i++){
    $team = 't' . $i;
    $needRide = getRideRequireCount($$team);
    if(abs($needRide->need - $avgRideNeed) > $needRideThreshold){
      $valid = false;
      break;
    }
    if(abs($needRide->notNeed - $avgRideNotNeed) > $needRideThreshold){
      $valid = false;
      break;
    }
  }
  if(!$valid) continue;


  // 라이드 가능
  $canRideThreshold = 1;

  $valid = true;
  for($i=1; $i<=4; $i++){
    $team = 't' . $i;
    $canRide = getCanRideCount($$team);
    if(abs($canRide - $avgCanRide) > $canRideThreshold){
      $valid = false;
      break;
    }
  }
  if(!$valid) continue;


  // 참여도
  $participationThreshold = 2;

  $valid = true;
  for($i=1; $i<=4; $i++){
    $team = 't' . $i;
    $participation = getParticipationCount($$team);
    if(abs($participation->high - $avgParticipationHigh) > 1){
      $valid = false;
      break;
    }
    if(abs($participation->mid - $avgParticipationMid) > $participationThreshold){
      $valid = false;
      break;
    }
    if(abs($participation->low - $avgParticipationLow) > $participationThreshold){
      $valid = false;
      break;
    }
  }
  if(!$valid) continue;



  echo '돌린숫자: ' . $iterationCount . '번<br/>';


  $age = getAgeRangeCount($t1);
  echo '팀1 나이 => (기준:' . $avgAge . '살) 위:' . $age->up . '명, 아래:' . $age->down . '명. <br/>';
  $age = getAgeRangeCount($t2);
  echo '팀2 나이 => (기준:' . $avgAge . '살) 위:' . $age->up . '명, 아래:' . $age->down . '명. <br/>';
  $age = getAgeRangeCount($t3);
  echo '팀3 나이 => (기준:' . $avgAge . '살) 위:' . $age->up . '명, 아래:' . $age->down . '명. <br/>';
  $age = getAgeRangeCount($t4);
  echo '팀4 나이 => (기준:' . $avgAge . '살) 위:' . $age->up . '명, 아래:' . $age->down . '명. <br/>';

  $gender = getGenderCount($t1);
  echo '팀1 성별 => 남:' . $gender->male . '명, 여:' . $gender->female . '명. <br/>';
  $gender = getGenderCount($t2);
  echo '팀2 성별 => 남:' . $gender->male . '명, 여:' . $gender->female . '명. <br/>';
  $gender = getGenderCount($t3);
  echo '팀3 성별 => 남:' . $gender->male . '명, 여:' . $gender->female . '명. <br/>';
  $gender = getGenderCount($t4);
  echo '팀4 성별 => 남:' . $gender->male . '명, 여:' . $gender->female . '명. <br/>';

  $needRide = getRideRequireCount($t1);
  echo '팀1 라이드필요 => 예:' . $needRide->need . '명, 아니요:' . $needRide->notNeed . '명. <br/>';
  $needRide = getRideRequireCount($t2);
  echo '팀2 라이드필요 => 예:' . $needRide->need . '명, 아니요:' . $needRide->notNeed . '명. <br/>';
  $needRide = getRideRequireCount($t3);
  echo '팀3 라이드필요 => 예:' . $needRide->need . '명, 아니요:' . $needRide->notNeed . '명. <br/>';
  $needRide = getRideRequireCount($t4);
  echo '팀4 라이드필요 => 예:' . $needRide->need . '명, 아니요:' . $needRide->notNeed . '명. <br/>';

  $canRide = getCanRideCount($t1);
  echo '팀1 라이드가능 => ' . $canRide . '명. <br/>';
  $canRide = getCanRideCount($t2);
  echo '팀2 라이드가능 => ' . $canRide . '명. <br/>';
  $canRide = getCanRideCount($t3);
  echo '팀3 라이드가능 => ' . $canRide . '명. <br/>';
  $canRide = getCanRideCount($t4);
  echo '팀4 라이드가능 => ' . $canRide . '명. <br/>';

  $participation = getParticipationCount($t1);
  echo '팀1 참여도 => 상:' . $participation->high . '명, 중:' . $participation->mid . '명, 하:' . $participation->low . '명. <br/>';
  $participation = getParticipationCount($t2);
  echo '팀2 참여도 => 상:' . $participation->high . '명, 중:' . $participation->mid . '명, 하:' . $participation->low . '명. <br/>';
  $participation = getParticipationCount($t3);
  echo '팀3 참여도 => 상:' . $participation->high . '명, 중:' . $participation->mid . '명, 하:' . $participation->low . '명. <br/>';
  $participation = getParticipationCount($t4);
  echo '팀4 참여도 => 상:' . $participation->high . '명, 중:' . $participation->mid . '명, 하:' . $participation->low . '명. <br/>';


  $ok = true;
}
while(!$ok);

function getParticipationCount($team){
  $return = new stdClass();
  $return->high = 0;
  $return->mid = 0;
  $return->low = 0;
  foreach($team as $p){
    switch($p->participation){
      case '상':
        $return->high++;
        break;
      case '중':
        $return->mid++;
        break;
      case '하':
        $return->low++;
    }
  }
  return $return;
}


function getCanRideCount($team){
  $return = 0;
  foreach($team as $p){
    if($p->canride == '예') $return++;
  }
  return $return;
}


function getRideRequireCount($team){
  $return = new stdClass();
  $return->need = 0;
  $return->notNeed = 0;
  foreach($team as $p){
    if($p->needride == '예') $return->need++;
    else $return->notNeed++;
  }
  return $return;
}


function getGenderCount($team){
  $return = new stdClass();
  $return->male = 0;
  $return->female = 0;
  foreach($team as $p){
    if($p->gender == '남') $return->male++;
    else $return->female++;
  }
  return $return;
}


function getAgeRangeCount($team){
  global $avgAge;
  $return = new stdClass();
  $return->up = 0;
  $return->down = 0;
  foreach($team as $p){
    if($p->age > $avgAge) $return->up++;
    else $return->down++;
  }
  $return->diff = abs($return->up - $return->down);
  return $return;
}


function getAverageAge($team){
  $age = 0;
  foreach($team as $p)
    $age += $p->age;
  $age /= count($team);
  return floor($age);
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>브릿지메이커스: 팀구성</title>
</head>

<body>

<?php $i=1; foreach( [$t1, $t2, $t3, $t4] as $team ): ?>
<h2>Team<?=$i?></h2>
<table width="100%" cellpadding="5" border="1" cellspacing="0">
  <thead>
    <tr>
      <th width="13%" align="left" valign="top">이름</th>
      <th width="13%" align="left" valign="top">나이</th>
      <th width="13%" align="left" valign="top">성별
      <th width="13%" align="left" valign="top">사는곳</th>
      <th width="12%" align="left" valign="top">순신청</th>
      <th width="12%" align="left" valign="top">라이드필요</th>
      <th width="12%" align="left" valign="top">라이드가능</th>
      <th width="12" align="left" valign="top">참여도</th>
    </tr>
  </thead>

  <tbody>
  <?php foreach($team as $row):?>
    <tr>
      <td><?=$row->name?></td>
      <td><?=$row->age?></td>
      <td><?=$row->gender?></td>
      <td><?=$row->address?></td>
      <td><?=$row->register?></td>
      <td><?=$row->needride?></td>
      <td><?=$row->canride?></td>
      <td><?=$row->participation?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php $i++; endforeach; ?>


</body>
</html>




