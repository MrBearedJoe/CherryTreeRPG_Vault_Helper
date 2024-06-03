<?php
if ($_POST['success'] != "") {
  $jsonData = openFile($filePath);
  $jsonData['codes'][$_POST['success']]['status'] = 'success';
  updateFile($filePath, $jsonData);
  echo "Success Code ADD: {$_GET['success']}";
}

if ($_GET['remove'] != "") {
  $jsonData = openFile($filePath);
  unset($jsonData['codes'][$_GET['remove']]);
  updateFile($filePath, $jsonData);
  echo "Removed: {$_GET['remove']}";
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
  $jsonData = openFile($filePath);

  $count = 0;
  $codesList = [];

  if ($_POST['random'] == "yes") {
    $numbersNeeded = $_POST['numberOfCodes'];
    $randomCount = 0;
    while ($randomCount != $numbersNeeded) {
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
  <div class='alert alert-danger alert-dismissible' role='alert' 
  style='width:10ch; position: fixed; top:2rem; left: 48vw; z-index:10; '><BR>
  <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
  <button type='button' class='btn btn-sm btn-danger' data-copy-code-btn>Copy</button>
  <span data-copy-codes>
  $creditedList
  </span>
  </div>
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
