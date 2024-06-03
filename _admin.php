<?php
if ($_POST['success'] != "") {
  $jsonData = openFile($filePath);
  $jsonData['codes'][$_POST['success']]['status'] = 'success';
  array_push($jsonData['logs'], ["CRACKED! Correct Code ADD: {$_GET['success']}. Status is now success"]);

  updateFile($filePath, $jsonData);
  echo "";
}

if ($_POST['massAddCodes'] == "massAddCodes") {
  $jsonData = openFile($filePath);
  if ($_POST['clearCodes'] == "yes") {
    $jsonData['codes'] = [];
    $jsonData['logs'] = [];
  }

  $codes = isset($_POST['codes']) ? $_POST['codes'] : "";
  $codes = explode("\n", str_replace("\r", "", $codes));

  foreach ($codes as $code) {
    if ($code == "" || $code == " ") continue;
    $jsonData['codes'][$code] = [
      "status" => "not_checked",
      "credit" => "",
    ];
  }
  array_push($jsonData['logs'], ["Added Codes: {$_POST['codes']}"]);
  updateFile($filePath, $jsonData);
}

if ($_POST['creditAdd'] == "creditAdd") {
  $_POST['creditTo'] = ($_POST['creditTo'] != "") ? $_POST['creditTo'] : "(Blank)";
  $jsonData = openFile($filePath);

  $count = 0;
  $codesList = [];

  if ($_POST['random'] == "yes") {
    $randomCount = 0;
    while ($randomCount != $_POST['numberOfCodes']) {
      $code = array_rand($jsonData['codes'], 1);
      if (
        $jsonData['codes'][$code]['credit'] == ""
        && $jsonData['codes'][$code]['status'] == "not_checked"
      ) {

        $randomCount++;
        $jsonData['codes'][$code]['credit'] = $_POST['creditTo'];
        array_push($codesList, $code);
      }
    }
  } else {

    foreach ($jsonData['codes'] as $code => $data) {

      if ($data['credit'] == "") {
        $count++;
        $jsonData['codes'][$code]['credit'] = $_POST['creditTo'];
        array_push($codesList, $code);
      }

      if ($count == $_POST['numberOfCodes']) break;
    }
  }

  foreach ($codesList as $codeList) $creditedList .= "$codeList\r";
  array_push($jsonData['logs'], ["Credited {$_POST['creditTo']} to: $creditedList"]);
  updateFile($filePath, $jsonData);
  echo "
  <div id='pulledCodes' class='modal' tabindex='-1'>
  <div class='modal-dialog modal-dialog-scrollable modal-sm'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title'>Pulled Codes</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body'>
      <button type='button' class='btn btn-sm btn-danger' data-copy-btn>Copy</button>
      <span data-copy-codes>
      $creditedList
      </span>
      </div>
    </div>
  </div>
</div>
<button type='button' class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#pulledCodes'>

</button>
  ";
}

if ($_POST['invalidCodes'] == "invalidCodes") {
  $jsonData = openFile($filePath);

  $codes = isset($_POST['codes']) ? $_POST['codes'] : "";
  $codes = explode("\n", str_replace("\r", "", $codes));

  foreach ($codes as $code) {
    if ($_POST['creditTo'] != "") $jsonData['codes'][$code]["credit"] = $_POST['creditTo'];
    $jsonData['codes'][$code]["status"] = "invalid";
  }

  array_push($jsonData['logs'], ["Codes marched Invalid: {$_POST['codes']}"]);
  updateFile($filePath, $jsonData);
}



if ($_POST['invalidCredited'] == "invalidCredited") {
  $jsonData = openFile($filePath);

  foreach ($jsonData['codes'] as $code => $data) {
    // echo "{$data['credit']} - {$_POST['creditTo']}";
    if ($data['credit'] == $_POST['creditTo']) $jsonData['codes'][$code]["status"] = "invalid";
  }

  array_push($jsonData['logs'], ["All codes credited to {$_POST['creditTo']} are now Invalid"]);
  updateFile($filePath, $jsonData);
}


if ($_POST['invalidAllCredited'] == "invalidAllCredited") {
  $jsonData = openFile($filePath);

  foreach ($jsonData['codes'] as $code => $data) {
    // echo "{$code}-{$data['credit']}";
    if ($data['credit'] != "") $jsonData['codes'][$code]["status"] = "invalid";
  }

  array_push($jsonData['logs'], ["All codes credited are now Invalid"]);
  updateFile($filePath, $jsonData);
}

//STILL TESTING!!!
if ($_POST['hint'] == "hint") {
  if ($_POST['place'] == 0) exit; //placement can not be 0 and shouldn't be over 7 currently
  $jsonData = openFile($filePath);
  $codesList = [];
  foreach ($jsonData['codes'] as $code => $data) {
    if ($data['status'] == "invalid") continue; //Skip currently invalid codes
    $placement = $_POST['place'] - 1; //convert placement to index
    $codeStr = "$code"; //cover number/key/index to string
    $code_placement_number = $codeStr[$placement]; //string index to single character

    if ($codeStr[$placement] != $_POST['digit']) {
      $jsonData['codes'][$code]["status"] = "invalid";
      $jsonData['codes'][$code]["credit"] = "(Hint)";
      array_push($codesList, $code);
    }
  }
  foreach ($codesList as $codeList) $creditedList .= "$codeList\r";

  array_push($jsonData['logs'], ["Add Hint of {$_POST['digit']} at place {$_POST['place']} for codes: $creditedList"]);
  updateFile($filePath, $jsonData);
}
include_once "_admin_forms.html";
