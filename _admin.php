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
  if ($_POST['clearCodes'] == "yes") $jsonData['codes'] = [];

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
  <button type='button' class='btn  btn-smbtn-danger' data-copy-code-btn>Copy</button>
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



echo "
<div class='row'>
<div class='col-12 py-1 px-4'>

<div class='card border border-danger bg-dark text-white'>
<h5 class='card-header bg-danger text-white'>Admin</h5>

<form action='?admin' method='POST'>
<div class='input-group input-group-sm'>
<input class='form-control' type='numbers' name='success' placeholder='Correct Code!!'>
<button class='btn btn-sm btn-success' type='submit'>Cracked</button>
</div>
</form>


<div class='card-body'>
<div class='row'>

  <div class='col-3'>
  <div class='card p-0 border border-secondary bg-dark text-white'>
<h6 class='card-header bg-secondary text-white py-1'>Mass Add Codes</h6>
<div class='card-body p-2'>
    <form action='?admin' method='POST'>
    <input  type='hidden' name='massAddCodes' value='massAddCodes'>
      <label  class='form-label'>Clear Current Codes?</label>  
      <input type='checkbox' name='clearCodes' value='yes'>
      <textarea  class='form-control form-control-sm mb-1' name='codes' placeholder='Codes. One Per Line'></textarea>
      <button class='btn btn-sm btn-primary' type='submit'>New/Add Codes</button>
    </form>
  </div>
  </div>
  </div>


  <div class='col-3'>
  <div class='card p-0 border border-secondary bg-dark text-white'>
  <h6 class='card-header bg-secondary text-white py-1'>Poll from not Check and Credit</h6>
  <div class='card-body p-2'>
    <form action='?admin' method='POST'>
      <label  class='form-label'>Random Spots? <input type='checkbox' name='random' value='yes'></label>  
      
      <input  class='form-control form-control-sm mb-1' type='hidden' name='creditAdd' value='creditAdd'>
      <input  class='form-control form-control-sm mb-1' type='text' name='creditTo' placeholder='Credit To:'>
      <input  class='form-control form-control-sm mb-1' type='number' step='1' name='numberOfCodes' placeholder='How many you need?'>
      <button class='btn  btn-sm btn-primary' type='submit'>Add Credits</button>
    </form>
  </div>
  </div>
  </div>


  <div class='col-3'>
  <div class='card p-0 border border-secondary bg-dark text-white'>
  <h6 class='card-header bg-secondary text-white py-1'>Change Codes to Invalid</h6>
  <div class='card-body p-2'>
    <form action='?admin' method='POST'>
      <input  class='form-control form-control-sm mb-1' type='hidden' name='invalidCodes' value='invalidCodes'>
      <input  class='form-control form-control-sm mb-1' name='creditTo' placeholder='Credit To (If not already)'>
      <textarea  class='form-control form-control-sm mb-1' name='codes' placeholder='Codes. One Per Line. No spaces after code, Just line break'></textarea>
      <button class='btn  btn-sm btn-primary' type='submit'>Change to Invalid</button>
    </form>
  </div>
  </div>
  </div>



  <div class='col-3'>
  <div class='card p-0 border border-secondary bg-dark text-white'>
  <h6 class='card-header bg-secondary text-white py-1'>Invalid Credited Codes</h6>
  <div class='card-body p-2'>
    <form action='?admin' method='POST'>
      <input  class='form-control form-control-sm mb-1' type='hidden' name='invalidCredited' value='invalidCredited'>
      <input  class='form-control form-control-sm my-1' name='creditTo' placeholder='Invalid all Credited To: '>
      <button class='btn  btn-sm btn-primary d-block' type='submit'>Change Credited to Invalid</button>
    </form>
    <hr class='mx-0 bg-white'>
    <form action='?admin' method='POST'>
      <input  class='form-control form-control-sm mb-1' type='hidden' name='invalidAllCredited' value='invalidAllCredited'>
      <button class='btn btn-sm btn-danger d-block' type='submit'>Change All Credited to Invalid</button>

    </form>
  </div>
  </div>
  </div>

</div>
</div>
</div>
</div>
</div>




<div class='modal fade' id='correctCodeModal' tabindex='-1'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title text-dark'>Correct Code</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body'>
      <form action='?admin' method='POST'>
      <div class='input-group input-group-sm'>
      <input class='form-control' type='numbers' name='success' placeholder='Correct Code!!'>
      <button class='btn btn-sm btn-success' type='submit'>Cracked</button>
      </div>
      </form>

      </div>
    </div>
  </div>
</div>
";
