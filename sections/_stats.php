<?php
$totalCount = count($jsonData['codes']);
$total_NotChecked_Count = 0;
$creditedCount = 0;
$creditorsList = [];
$uniqueCreditors = [];
$successAlert = "";
foreach ($jsonData['codes'] as $code => $data) {
  if ($data['status'] == "invalid") $total_NotChecked_Count++;
  if ($data['credit'] != "") {
    $creditedCount++;
    $creditorsList[$data['credit']] = $data['credit'];
    if (!in_array($data['credit'], $uniqueCreditors)) array_push($uniqueCreditors, $data['credit']);
  }
  if ($data['status'] == "success") $successAlert .= "<div class='col-12 text-center '><div class='alert alert-success p-2  placeholder-glow' role='alert'><h4 class='alert-heading'>Cracked! The code is: <b>$code</b></h4></div> </div>";
}

$randomColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light'];
$creditors = '';
$datalistOfCreditor = "<datalist id='creditors'>";
foreach ($uniqueCreditors as $uniqueCreditor) {
  if ($uniqueCreditor == "(Hint)" || $uniqueCreditor == "(Blank)") continue;
  $random_keys = array_rand($randomColors, 1);
  $creditors .= "<badge class='badge text-bg-{$randomColors[$random_keys]} mx-1'>@$uniqueCreditor&nbsp;</badge>";
  $datalistOfCreditor .= "<option value='$uniqueCreditor'>$uniqueCreditor</options>";
}
$datalistOfCreditor .= "</datalist>";
echo $datalistOfCreditor;


$creditorsList = count($creditorsList);

$randomCode = ($totalCount > 0) ? array_rand($jsonData['codes'], 1) : "";

$randomCodeSplitArr = str_split($randomCode);
$uniqueDigitsArr = array_unique(str_split($randomCode));
$uniqueDigitsList = '';
foreach ($uniqueDigitsArr as $d) $uniqueDigitsList .= "$d ";
$uniqueCount = count($uniqueDigitsArr);
$digitsNeededCount = count($randomCodeSplitArr);


$invalidPercent = ($totalCount > 0) ? ($total_NotChecked_Count / $totalCount) * 100 : 0;
$invalidPercent = number_format($invalidPercent, 2, '.', '') . "%";


$creditedPercent = ($totalCount > 0) ? ($creditedCount / $totalCount) * 100 : 0;
$creditedPercent = number_format($creditedPercent, 2, '.', '') . "%";


if (!empty($jsonData['hints'])) {
  $ifHints = "";
  foreach ($jsonData['hints'] as $key => $h) $ifHints .= "<BR><b>Hints: </b>{$h[0]}";
}

echo "


  <div class='col-12 py-1 px-4'>

      <div class='card border border-primary bg-dark text-white'>
        <h5 class='card-header bg-primary text-white p-1'>
          <img src='./images/vault.png' alt='Stats: image of Vault door' height='18' class='ms-3' style='margin-top:-0.4rem;'> 
          Stats
          <a class='btn btn-sm btn-light align-right' href='./login/out.php'>Logout</a>
        </h5>
        <div class='card-body p-2'>
          <div class='row'>
          $successAlert
          ";
if ($login_type == "user") include_once "./sections/_user_form.php";
echo "

            <div class='col'>
              <div class='card border-secondary'>
                <h6 class='card-header p-1 bg-secondary text-white text-center'>Digit Info</h6>
                <div class='card-body p-1'>
                  Unique Numbers: <b>$uniqueCount</b> <BR>
                  Code Length: <b>$digitsNeededCount</b> <BR>
                  Numbers Needed: <b>$uniqueDigitsList</b>
                  $ifHints
                </div>
              </div>
            </div>

            <div class='col'>
              <div class='card border-secondary'>
                <h6 class='card-header p-1 bg-secondary text-white text-center'>Codes Done</h6>
                <div class='card-body p-1'>
                  Invalid: <b>$total_NotChecked_Count</b> of $totalCount ($invalidPercent)<BR>
                  Credited: <b>$creditedCount</b> of $totalCount ($creditedPercent)<BR> 
                  Total Codes: <b>$totalCount</b>
                </div>
              </div>
            </div>

            <div class='col-xl-4 col-lg-12 col-12 mt-sm-2 mt-xl-0'>
              <div class='card border-secondary'>
                <h6 class='card-header p-1 bg-secondary text-white text-center'>Credits Go To</h6>
                <div class='card-body p-1'>
                  <b>$creditorsList</b> Users have helped crack the vault!<hr class='bg-primary my-0'>
                  <small>$creditors</small>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
  </div>
";
