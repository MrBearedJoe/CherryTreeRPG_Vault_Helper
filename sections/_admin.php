<?php
if ($_POST) $_POST = sanitize($_POST);
if ($_POST['success'] != "") {
  $jsonData = openFile($filePath);
  updateFile($filePath_backup, $jsonData);

  $jsonData['codes'][$_POST['success']]['status'] = 'success';
  array_push($jsonData['logs'], ["CRACKED! Correct Code ADD: {$_GET['success']}. Status is now success"]);

  updateFile($filePath, $jsonData);
  echo "";
}

if ($_POST['massAddCodes'] == "massAddCodes") {
  $jsonData = openFile($filePath);
  updateFile($filePath_backup, $jsonData);

  if ($_POST['clearCodes'] == "yes") {
    $jsonData['codes'] = [];
    $jsonData['logs'] = [];
    $jsonData['hints'] = [];
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

  if ($_FILES['csv']['type'] == "text/csv") {
    $tmpName = $_FILES['csv']['tmp_name'];
    $csvArr = array_map('str_getcsv', file($tmpName));
    $count = 0;

    $jsonData = openFile($filePath);
    updateFile($filePath_backup, $jsonData);
    $jsonData['codes'] = [];
    $jsonData['logs'] = [];
    $jsonData['hints'] = [];

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
  } elseif ($_FILES['csv']['type'] == "application/json") {

    $tmpName = $_FILES['csv']['tmp_name'];
    $jsonData = file($tmpName);
    $jsonData = json_decode($jsonData[0], true);
    updateFile($filePath, $jsonData);
  }
}
if ($_POST['lastSave'] == "lastSave") {
  $jsonData = openFile($filePath_backup);
  updateFile($filePath, $jsonData);
}



if ($_POST['creditAdd'] == "creditAdd") {
  $_POST['creditTo'] = ($_POST['creditTo'] != "") ? $_POST['creditTo'] : "(Blank)";
  $jsonData = openFile($filePath);
  updateFile($filePath_backup, $jsonData);

  $count = 0;
  $codesList = [];

  if ($_POST['pullHow'] == "random") {

    $divideByRange = [2, 3, 4, 5, 6, 7, 8, 9, 10];
    $divideByIndex = array_rand($divideByRange, 1);
    $divideBy = $divideByRange[$divideByIndex];
    $total_count = count($jsonData['codes']);
    $start = round($total_count / $divideBy);

    $randomSpotCount = 0;
    $pulledCount = 0;

    foreach ($jsonData['codes'] as $code => $data) {
      $randomSpotCount++;

      if (
        $randomSpotCount >= $start
        && $data['credit'] == ""
        && $data['status'] == "not_checked"
      ) {
        $pulledCount++;
        $jsonData['codes'][$code]['credit'] = $_POST['creditTo'];
        array_push($codesList, $code);
      }

      if ($pulledCount >= $_POST['numberOfCodes']) break;
    }

    //OLD RANDOM
    // $randomCount = 0;
    // while ($randomCount != $_POST['numberOfCodes']) {
    //   $code = array_rand($jsonData['codes'], 1);
    //   if (
    //     $jsonData['codes'][$code]['credit'] == ""
    //     && $jsonData['codes'][$code]['status'] == "not_checked"
    //   ) {

    //     $randomCount++;
    //     $jsonData['codes'][$code]['credit'] = $_POST['creditTo'];
    //     array_push($codesList, $code);
    //   }
    // }

  } else {
    if ($_POST['pullHow'] == "fromBottom") $jsonData['codes'] = array_reverse($jsonData['codes'], true);
    foreach ($jsonData['codes'] as $code => $data) {

      if ($data['credit'] == "") {
        $count++;
        $jsonData['codes'][$code]['credit'] = $_POST['creditTo'];
        array_push($codesList, $code);
      }

      if ($count == $_POST['numberOfCodes']) break;
    }
    if ($_POST['pullHow'] == "fromBottom") $jsonData['codes'] = array_reverse($jsonData['codes'], true);
  }


  $rowSize = $_POST["numberOfCodes"] + 1;
  foreach ($codesList as $codeList) $creditedList .= "$codeList\r";
  array_push($jsonData['logs'], ["Credited {$_POST['creditTo']} to: $creditedList"]);
  updateFile($filePath, $jsonData);
  $lastCodesPulledBtn = "
  <button type='button' class='btn btn-dark btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#pulledCodes'>Last Codes Pulled</button>
  ";
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
          <button type='button' class='btn btn-sm btn-danger mb-1' onclick='copyCodes()'>Copy Codes</button>
        </div>
        <textarea rows='$rowSize' class='form-control' id='codesList'>$creditedList</textarea>
      </div>
    </div>
  </div>
</div>

  ";
}

if ($_POST['invalidCodes'] == "invalidCodes") {
  $jsonData = openFile($filePath);
  updateFile($filePath_backup, $jsonData);

  $codes = isset($_POST['codes']) ? $_POST['codes'] : "";
  $codes = explode("\n", str_replace("\r", "", $codes));

  foreach ($codes as $code) {
    if ($code == "" || $code == " ") continue;
    if ($_POST['creditTo'] != "") $jsonData['codes'][$code]["credit"] = $_POST['creditTo'];
    $jsonData['codes'][$code]["status"] = "invalid";
  }

  array_push($jsonData['logs'], ["Codes marched Invalid: {$_POST['codes']}"]);
  updateFile($filePath, $jsonData);
}



if ($_POST['invalidCredited'] == "invalidCredited") {
  $jsonData = openFile($filePath);
  updateFile($filePath_backup, $jsonData);


  foreach ($jsonData['codes'] as $code => $data) {
    // echo "{$data['credit']} - {$_POST['creditTo']}";
    if ($data['credit'] == $_POST['creditTo']) $jsonData['codes'][$code]["status"] = "invalid";
  }

  array_push($jsonData['logs'], ["All codes credited to {$_POST['creditTo']} are now Invalid"]);
  updateFile($filePath, $jsonData);
}


if ($_POST['invalidAllCredited'] == "invalidAllCredited") {
  $jsonData = openFile($filePath);
  updateFile($filePath_backup, $jsonData);


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
  updateFile($filePath_backup, $jsonData);

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
  array_push($jsonData['hints'], ["Digit: <b>{$_POST['digit']}</b> / Place: <b>{$_POST['place']}</b>"]);
  updateFile($filePath, $jsonData);
}

if ($_POST['hintText'] == "hintText") {
  $jsonData = openFile($filePath);
  updateFile($filePath_backup, $jsonData);

  array_push($jsonData['hints'], [$_POST['text']]);
  updateFile($filePath, $jsonData);
}


if ($_POST['addUser'] == "addUser") {
  $users = openFile($users_filePath);
  array_push($users[$_POST['userType']], base64_encode($_POST['pass']));
  print_r($users);
  updateFile($users_filePath, $users);
}

if (isset($_GET['delUser'])) {
  $users = openFile($users_filePath);
  foreach ($users[$_GET['type']] as $key => $user) {
    echo $user . " /// " . base64_encode($_GET['pass']);
    if ($user == base64_encode($_GET['pass'])) unset($users[$_GET['type']][$key]);
  }
  updateFile($users_filePath, $users);
  echo "<meta http-equiv='refresh' content=\"0; url=./\">";
  die("");
}


?>

<div class="col-12 py-1 px-4">
  <div class="card border border-danger bg-dark text-white">
    <h5 class="card-header bg-danger text-white">
      <img src='./images/vein.png' alt='Admin picture of Vein' height='24' class='ms-2'
        style='margin-top:-0.4rem;border-radius: 3rem;'>
      Admin
      <button type="button" class="btn btn-success btn-sm ms-2 mb-1" data-bs-toggle="modal"
        data-bs-target="#correctCodeModal">
        Correct Code
      </button>
      <?php if ($login_type == "admin") {
        echo "
          <button type='button' class='btn btn-warning btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#generateNewCodesModal'>
          Add New Codes/Reset Codes
          </button>


          <div class='btn-group'>
            <button type='button' class='btn btn-info btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#sheetsImport'>
              Import File
            </button>
            <button type='button' class='btn btn-lg btn-info btn-sm mb-1 dropdown-toggle dropdown-toggle-split' data-bs-toggle='dropdown' aria-expanded='false'>
              <span class='visually-hidden'>Toggle Dropdown</span>
            </button>
            <ul class='dropdown-menu'>
              <li><a class='dropdown-item' href='./src/backup.json' download>
              DL Backup</a></li>
            </ul>
          </div>

          
          <button type='button' class='btn btn-light btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#userManager'>
          User Manager
          </button>

          ";
      }
      ?>
      <?= $lastCodesPulledBtn ?>

    </h5>

    <div class="card-body p-1">
      <div class="row g-1">


        <div class="col-lg-3 col-md-6">
          <div class="card p-0 border border-secondary bg-dark text-white">
            <h6 class="card-header bg-secondary text-white py-1">
              Pull Codes to distribute
            </h6>
            <div class="card-body p-2">
              <form action="" method="POST">
                <input class="form-control form-control-sm mb-1" type="hidden" name="creditAdd" value="creditAdd"
                  autocomplete="off" />

                <div class="btn-group mb-1" role="group">
                  <input type="radio" class='btn-check' name="pullHow" value="fromTop" id='fromTop' autocomplete="off"
                    checked />
                  <label class="btn btn-sm  btn-outline-light" for='fromTop'>From Top?</label>
                  <input type="radio" class='btn-check' name="pullHow" value="fromBottom" id="fromBottom"
                    autocomplete="off" />
                  <label class="btn btn-sm  btn-outline-light" for="fromBottom">From Bottom?</label>
                  <input type="radio" class='btn-check' name="pullHow" value="random" id='random' autocomplete="off" />
                  <label class="btn btn-sm  btn-outline-light" for='random'>Random Spots?</label>
                </div>

                <input class="form-control form-control-sm mb-1" name="creditTo" placeholder="Credit To:"
                  list="creditors" />
                <input class="form-control form-control-sm mb-1" type="number" step="1" name="numberOfCodes"
                  placeholder="How many you need?" required />
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
              <form action="" method="POST">
                <input class="form-control form-control-sm mb-1" type="hidden" name="invalidCodes"
                  value="invalidCodes" />
                <input class="form-control form-control-sm mb-1" name="creditTo"
                  placeholder="Credit To (If not already) *optional" list="creditors" />
                <textarea class="form-control form-control-sm mb-1" name="codes"
                  placeholder="Codes. One Per Line. No spaces after code, Just line break" rows='3' required></textarea>
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
              <form action="" method="POST">
                <input class="form-control form-control-sm mb-1" type="hidden" name="invalidCredited"
                  value="invalidCredited" />
                <input class="form-control form-control-sm my-1" name="creditTo" list="creditors"
                  placeholder="Invalid all Credited To:" required />
                <button class="btn btn-sm btn-primary d-block" type="submit">
                  Change Credited to Invalid
                </button>
              </form>
              <hr class="mx-0 bg-white" />
              <form action="" method="POST">
                <input class="form-control form-control-sm mb-1" type="hidden" name="invalidAllCredited"
                  value="invalidAllCredited" />
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
              <form action="" method="POST">
                <input type="hidden" name="hint" value="hint" />
                <div class="input-group input-group-sm mb-1">
                  <input class="form-control" type="number" pattern="[0-9]{1}" min="0" max="9" name="digit"
                    placeholder="Digit" required />
                  <input class="form-control" type="number" pattern="[1-7]{1}" min="1" max="7" name="place"
                    placeholder="Placement" required />
                  <button class="btn btn-success" type="submit">
                    Add Hint
                  </button>
                </div>
                <div class="form-text text-secondary">
                  This will invalid all codes without this digit placement.
                </div>
              </form>
              <hr class='my-2'>
              <form action="" method="POST">
                <input type="hidden" name="hintText" value="hintText" />
                <div class="input-group input-group-sm mb-1">
                  <input class="form-control" type="text" name="text" placeholder="Add Hint Text Here" required />
                  <button class="btn btn-success" type="submit">
                    Add Hint
                  </button>
                </div>
                <div class="form-text text-secondary">
                  This will add text above to "Digit Info" area.
                </div>
              </form>
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
        <form action="" method="POST">
          <div class="input-group input-group-sm">
            <input class="form-control" type="number" name="success" placeholder="Correct Code!!" />
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
            <input class="form-control form-control-sm" type="number" name="digits"
              placeholder="Digits: ex: 1,2,3,4,5" />
            <label class="form-label">Code Length</label>
            <input class="form-control form-control-sm" type="number" name="codeLength" placeholder="How Long?" />
            <button class="btn btn-sm btn-success my-2" type="button" onclick="generateNewCodes()">
              Generate & Add Codes to Form Below
            </button>
          </div>
        </div>



        <div class='card p-0 border-danger'>
          <h5 class="card-header bg-danger text-white">Mass Add/Replace in list.</h5>
          <div class='card-body p-2'>
            <form action="" method="POST">
              <input type="hidden" name="massAddCodes" value="massAddCodes" />
              <label class="form-label">Clear Current Codes?</label>
              <input type="checkbox" name="clearCodes" value="yes"
                onchange="confirm('Checking this will CLEAR CODES. Are you sure?')" />
              <textarea class="form-control form-control-sm mb-1" name="codes" placeholder="Codes. One Per Line"
                rows='3' required></textarea>
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

        <form action="" method="POST" enctype="multipart/form-data">
          <label for="formFile" class="form-label">Download CSV from Google Sheet or backup.json file.</label>
          <div class="input-group input-group-sm">
            <input type="hidden" name='importFile' value='importFile'>
            <input class="form-control" type="file" name='csv' accept=".csv,.json" required>
            <button class="btn btn-sm btn-success" type="submit">
              Upload
            </button>
          </div>
        </form>
        <hr>
        <form action="" method="POST">
          <label for="formFile" class="form-label">Use Last save on site.</label>
          <div class="input-group input-group-sm">
            <input type="hidden" name='lastSave' value='lastSave'>
            <button class="btn btn-sm btn-success" type="submit">
              Load From Last Backup
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="userManager" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Users</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='row'>
          <div class='col-12' id='form'>
            <form action="" method="POST">
              <input type="hidden" name='addUser' value='addUser'>
              <div class="input-group input-group-sm my-2">
                <input type="radio" class='btn-check' name="userType" value="admin" id='admin' autocomplete="off" />
                <label class="btn btn-sm  btn-outline-light" for='admin'>Admin</label>
                <input type="radio" class='btn-check' name="userType" value="mod" id="mod" autocomplete="off" checked />
                <label class="btn btn-sm  btn-outline-light" for="mod">Mod</label>

                <input class="form-control" type="text" name='pass' placeholder="Enter pass here">
                <button class="btn btn-sm btn-success" type="submit">Add</button>
              </div>
            </form>
          </div>

          <div class='col-6' id='admin'>
            <h4>Admin:</h4>
            <?php
            foreach ($users['admin'] as $user) {
              $user_decoded = base64_decode($user);
              echo "<p><a href='?delUser&type=admin&pass=$user_decoded' class='badge rounded-pill text-bg-danger'>X</a> $user_decoded </p>";
            }
            ?>
          </div>


          <div class='col-6' id='mod'>
            <h4>Mod:</h4>
            <?php
            foreach ($users['mod'] as $user) {
              $user_decoded = base64_decode($user);
              echo "<p><a href='?delUser&type=mod&pass=$user_decoded' class='badge rounded-pill text-bg-danger'>X</a> $user_decoded </p>";
            }
            ?>
          </div>


        </div>

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