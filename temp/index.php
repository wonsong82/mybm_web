<?php
set_time_limit(0);
$data = file_get_contents(dirname(__FILE__) . '/data.csv');




$groupList = getValidGroupList(getList($data));





function getValidGroupList($list){

  $valid = false;
  $groupList = null;
  $numOfTrials = 0;

  do {
    $groupList = randomize($list, 12);
    $valid = checkAgeConflict($groupList);
    $numOfTrials++;
  }
  while(!$valid);

  var_dump($numOfTrials);
  return $groupList;
}




function randomize($list, $numOfGroups){

  $randomizedList = array_merge([], $list);

  shuffle($randomizedList);
  $totalNum = count($randomizedList);
  $numPpl =  floor($totalNum / $numOfGroups);
  $remainders = $totalNum % $numOfGroups;



  $groups = [];
  $i = 0;
  $currentGroupIndex = 0;
  foreach($randomizedList as $person){
    if($i==$numPpl){
      $i=0;
      $currentGroupIndex++;
      $groups[$currentGroupIndex] = [];
    }

    $groups[$currentGroupIndex][] = $person;
    $i++;
  }

  if($numOfGroups != count($groups)){
    $groups = array_splice($groups, 0, $numOfGroups);
  }

  $remaindersList = array_splice($randomizedList, $numOfGroups * $numPpl, $remainders);

  for($i=0; $i<$remainders; $i++){
    $groups[$i][] = $remaindersList[$i];
  }

  return $groups;
}


function checkAgeConflict($groupList){

  foreach($groupList as $group){
    $foundA = false;
    $foundD = false;
    $aCount = 0;
    $dCount = 0;
    foreach($group as $person){
      if($person['age_cat'] == 'A') {
        $foundA = true;
        $aCount++;
      }
      if($person['age_cat'] == 'D') {
        $foundD = true;
        $dCount++;
      }
    }
    if($foundA && $foundD){
      return false;
    }
    if($aCount > 2 || $dCount > 2){
      return false;
    }

  }

  return true;
}




function getList($data){

  $return = [];
  $list = explode("\n", $data);
  foreach($list as $p){
    $li = explode(',', $p);
    $person = [
      "name" => trim($li[0]),
      "age_cat" => trim($li[5])
    ];
    $return[] = $person;
  }

  return $return;
}


?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" >
</head>
<body>

  <table>
    <tr>
      <td>Name</td>
      <td>Age Category</td>
      <td>Group</td>
    </tr>
    <tr>
      <td colspan="3">-----------------------------------------------------</td>
    </tr>

    <?php for($i=0; $i<count($groupList); $i++): ?>
      <?php foreach($groupList[$i] as $member): ?>
        <tr>
          <td><?php echo $member['name']?></td>
          <td><?php echo $member['age_cat']?></td>
          <td><?php echo $i+1?></td>
        </tr>
      <?php endforeach;?>
      <tr>
        <td colspan="3">-----------------------------------------------------</td>
      </tr>
    <?php endfor;?>

  </table>


</body>
</html>
