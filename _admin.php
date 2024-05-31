<?php


if ($_POST['removed'] != "") {
  $jsonData = openFile($filePath);
  unset($jsonData['codes'][$_GET['removed']]);
  updateFile($filePath, $jsonData);
  echo "Removed: {$_POST['removed']}";
}

if ($_POST['massAddCodes'] == "massAddCodes") {
  $jsonData = openFile($filePath);
  if ($_POST['clearCodes'] == "yes") $jsonData['codes'] = [];

  $codes = isset($_POST['codes']) ? $_POST['codes'] : "";
  $codes = explode("\n", str_replace("\r", "", $codes));

  foreach ($codes as $code) {
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
  foreach ($codesList as $codeList) $creditedList .= "$codeList ";
  array_push($jsonData['logs'], ["Add Credited {$_POST['creditTo']} to: $creditedList"]);
  updateFile($filePath, $jsonData);
  echo "$creditedList";
}

if ($_POST['invalidCodes'] == "invalidCodes") {
  $jsonData = openFile($filePath);

  $codes = isset($_POST['codes']) ? $_POST['codes'] : "";
  $codes = explode("\n", str_replace("\r", "", $codes));

  foreach ($codes as $code) {
    $jsonData['codes'][$code]["status"] = "invalid";
  }

  array_push($jsonData['logs'], ["Codes marched Invalid: {$_POST['codes']}"]);
  updateFile($filePath, $jsonData);
}

echo "
<div class='row'>
<div class='col-12 py-1 px-4'>

<div class='card border border-danger bg-dark text-white'>
<h5 class='card-header bg-danger text-white'>Admin</h5>
<div class='card-body'>
<div class='row'>

  <div class='col-4'>
    <form action='?admin' method='POST'>
      <label class='form-label  font-weight-bold'>Mass Add Codes: </label><BR>
      <input  class='form-control' type='hidden' name='massAddCodes' value='massAddCodes'>
      <label  class='form-label'>Clear Current Codes?</label>  
      <input type='checkbox' name='clearCodes' value='yes'><br>
      <textarea  class='form-control' name='codes' placeholder='Codes. One Per Line'></textarea><BR>
      <button class='btn btn-primary' type='submit'>New/Add Codes</button>
    </form>
  </div>


  <div class='col-4'>
    <form action='?admin' method='POST'>
      <label  class='form-label font-weight-bold'>Pull codes to credit to: </label><BR>
      <label  class='form-label'>Random Spot?</label>  
      <input type='checkbox' name='random' value='yes' checked><br>
      <input  class='form-control' type='hidden' name='creditAdd' value='creditAdd'>
      <input  class='form-control' type='text' name='creditTo' placeholder='Credit To:'><BR>
      <input  class='form-control' type='number' step='1' name='numberOfCodes' placeholder='How many you need?'><br>
      <button class='btn btn-primary' type='submit'>Add Credits</button>
    </form>
  </div>


  <div class='col-4'>
    <form action='?admin' method='POST'>
      <label class='form-label'>Change to Invalid</label><BR>
      <input  class='form-control' type='hidden' name='invalidCodes' value='invalidCodes'>
      <textarea  class='form-control' name='codes' placeholder='Codes. One Per Line. No spaces after code, Just line break'></textarea><Br>
      <button class='btn btn-primary' type='submit'>Change to Invalid</button>
    </form>
  </div>

</div>
</div>
</div>
</div>
</div>
";
