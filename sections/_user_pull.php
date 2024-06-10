<?php
if ($_POST['creditAdd'] == "creditAdd") {
  $_POST['creditTo'] = ($_POST['creditTo'] != "") ? $_POST['creditTo'] : "(Blank)";
  $jsonData = openFile($filePath);

  $count = 0;
  $codesList = [];

  if ($_POST['pullHow'] == "random") {
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
  $lastCodesPulledBtn_user = "
  <button type='button' class='btn btn-light btn-sm' data-bs-toggle='modal' data-bs-target='#pulledCodes'>Last Codes Pulled</button>
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
