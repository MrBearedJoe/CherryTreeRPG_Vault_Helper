<?php
if ($_POST) $_POST = sanitize($_POST);
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

if ($_POST['importFile'] == "importFile" && $_FILES['csv']) {

  //File data example:
  //TRUE,0000000,name
  //FALSE,0000010,name

  $tmpName = $_FILES['csv']['tmp_name'];
  $csvArr = array_map('str_getcsv', file($tmpName));
  $count = 0;

  $jsonData = openFile($filePath);
  $jsonData['codes'] = [];
  $jsonData['logs'] = [];

  for ($i = 1; $i < count($csvArr); $i++) {
    if ($csvArr[$i][0] != "TRUE" && $csvArr[$i][0] != "FALSE") continue;

    $code_status = ($csvArr[$i][0] == "TRUE") ? "invalid" : "not_checked";
    $code_numbers = str_replace(" ", "", $csvArr[$i][1]);
    $code_credit = ($csvArr[$i][2] != "") ? $csvArr[$i][2] : "";

    $jsonData['codes'][$code_numbers] = [
      "status" => $code_status,
      "credit" => $code_credit,
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

  $rowSize = $_POST["numberOfCodes"] + 1;
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
      <div class='modal-body' id='copyCodes'>
        <div class='d-grid'>
          <button type='button' class='btn btn-sm btn-danger mb-1' >Copy (Under Construction)</button>
        </div>
        <textarea rows='$rowSize' class='form-control'>$creditedList</textarea>
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

?>

<div class="row">
  <div class="col-12 py-1 px-4">
    <div class="card border border-danger bg-dark text-white">
      <h5 class="card-header bg-danger text-white">
        <img src='./images/vein.png' alt='Admin picture of Vein' height='24' class='ms-2' style='margin-top:-0.4rem;border-radius: 3rem;'>
        Admin
        <button type="button" class="btn btn-success btn-sm mx-2" data-bs-toggle="modal" data-bs-target="#correctCodeModal">
          Correct Code
        </button>

        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#generateNewCodesModal">
          Generate New Codes and Add to List
        </button>


        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#sheetsImport">
          Import From Sheets (CSV)
        </button>

      </h5>

      <div class="card-body p-1">
        <div class="row g-1">


          <div class="col-lg-3 col-md-6">
            <div class="card p-0 border border-secondary bg-dark text-white">
              <h6 class="card-header bg-secondary text-white py-1">
                Pull Codes to distribute
              </h6>
              <div class="card-body p-2">
                <form action="?admin=<?= $_GET['admin'] ?>" method="POST">
                  <label class="form-label">Random Spots?
                    <input type="checkbox" name="random" value="yes" /></label>
                  <input class="form-control form-control-sm mb-1" type="hidden" name="creditAdd" value="creditAdd" />
                  <input class="form-control form-control-sm mb-1" name="creditTo" placeholder="Credit To:" list="creditors" />
                  <input class="form-control form-control-sm mb-1" type="number" step="1" name="numberOfCodes" placeholder="How many you need?" required />
                  <button class="btn btn-sm btn-primary" type="submit">
                    Get Codes
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card p-0 border border-secondary bg-dark text-white">
              <h6 class="card-header bg-secondary text-white py-1">
                Change Codes to Invalid
              </h6>
              <div class="card-body p-2">
                <form action="?admin=<?= $_GET['admin'] ?>" method="POST">
                  <input class="form-control form-control-sm mb-1" type="hidden" name="invalidCodes" value="invalidCodes" />
                  <input class="form-control form-control-sm mb-1" name="creditTo" placeholder="Credit To (If not already) *optional" list="creditors" />
                  <textarea class="form-control form-control-sm mb-1" name="codes" placeholder="Codes. One Per Line. No spaces after code, Just line break" rows='3' required></textarea>
                  <button class="btn btn-sm btn-primary" type="submit">
                    Change status of codes to Invalid
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card p-0 border border-secondary bg-dark text-white">
              <h6 class="card-header bg-secondary text-white py-1">
                Invalid Credited Codes
              </h6>
              <div class="card-body p-2">
                <form action="?admin=<?= $_GET['admin'] ?>" method="POST">
                  <input class="form-control form-control-sm mb-1" type="hidden" name="invalidCredited" value="invalidCredited" />
                  <input class="form-control form-control-sm my-1" name="creditTo" list="creditors" placeholder="Invalid all Credited To:" required />
                  <button class="btn btn-sm btn-primary d-block" type="submit">
                    Change Credited to Invalid
                  </button>
                </form>
                <hr class="mx-0 bg-white" />
                <form action="?admin=<?= $_GET['admin'] ?>" method="POST">
                  <input class="form-control form-control-sm mb-1" type="hidden" name="invalidAllCredited" value="invalidAllCredited" />
                  <button class="btn btn-sm btn-danger d-block" type="submit">
                    Change All Credited to Invalid
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <div class="card p-0 border border-success bg-dark text-white">
              <h6 class="card-header bg-success py-1">
                Got a Hint?
              </h6>
              <div class="card-body p-2">
                <form action="?admin=<?= $_GET['admin'] ?>" method="POST">
                  <input type="hidden" name="hint" value="hint" />
                  <div class="input-group input-group-sm mb-1">
                    <input class="form-control" type="number" pattern="[0-9]{1}" min="0" max="9" name="digit" placeholder="Digit" required />
                    <input class="form-control" type="number" pattern="[1-7]{1}" min="1" max="7" name="place" placeholder="Placement" required />
                    <button class="btn btn-success" type="submit">
                      Add Hint
                    </button>
                  </div>
                  <div class="form-text text-secondary">
                    This will invalid all codes without this digit placement.
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Hidden behind button click-->
<div class="modal fade" id="correctCodeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Correct Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="?admin=<?= $_GET['admin'] ?>" method="POST">
          <div class="input-group input-group-sm">
            <input class="form-control" type="numbers" name="success" placeholder="Correct Code!!" />
            <button class="btn btn-sm btn-success" type="submit">
              Cracked
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="generateNewCodesModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Generate New Codes & Mass Add Codes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="generateCodesForm">

        <div class='card p-0 my-2 border-success'>
          <h5 class="card-header bg-success text-white">Generate Codes and Add to Form below</h5>
          <div class='card-body p-2'>
            <label class="form-label">Unique Digits <em>ex: 1,2,3,</em></label>
            <input class="form-control form-control-sm" type="numbers" name="digits" placeholder="Digits: ex: 1,2,3,4,5" />
            <label class="form-label">Code Length</label>
            <input class="form-control form-control-sm" type="numbers" name="codeLength" placeholder="How Many?" />
            <button class="btn btn-sm btn-success my-2" type="button" onclick="generateNewCodes()">
              Generate and Add Codes to Textarea
            </button>
          </div>
        </div>



        <div class='card p-0 border-danger'>
          <h5 class="card-header bg-danger text-white">Mass Add/Replace in list.</h5>
          <div class='card-body p-2'>
            <form action="?admin=<?= $_GET['admin'] ?>" method="POST">
              <input type="hidden" name="massAddCodes" value="massAddCodes" />
              <label class="form-label">Clear Current Codes?</label>
              <input type="checkbox" name="clearCodes" value="yes" checked />
              <textarea class="form-control form-control-sm mb-1" name="codes" placeholder="Codes. One Per Line" rows='3' required></textarea>
              <button class="btn btn-sm btn-danger" type="submit">
                New/Add Codes to List
              </button>
            </form>
          </div>
        </div>



      </div>
    </div>
  </div>
</div>






<div class="modal fade" id="sheetsImport" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Import Codes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="?admin=<?= $_GET['admin'] ?>" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <input type="hidden" name='importFile' value='importFile'>
            <label for="formFile" class="form-label">Download Codes from Google Sheet with CSV format</label>
            <input class="form-control" type="file" name='csv' accept=".csv">
          </div>
          <button class="btn btn-sm btn-success" type="submit">
            Import
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function generateCombinations(digits, length) {
    let combinations = [];
    const recursiveGenerate = (prefix, remainingLength) => {
      if (remainingLength === 0) {
        combinations.push(prefix);
        return;
      }
      for (let digit of digits) {
        recursiveGenerate(prefix + digit, remainingLength - 1);
      }
    };
    recursiveGenerate("", length);
    return combinations.filter((combination) => {
      return digits.every((digit) => combination.includes(digit));
    });
  }

  function generateNewCodes() {
    const digits = document.querySelector(`#generateCodesForm input[name="digits"]`).value.split(",")
    let length = document.querySelector(`#generateCodesForm input[name="codeLength"]`).value

    const combinations = generateCombinations(digits, length);
    combinations.value = ``
    for (let c of combinations) {
      document.querySelector(`#generateCodesForm textarea[name="codes"]`).value += `${c}\r\n`;
    }
  }
</script>