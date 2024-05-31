<?php
$jsonData = openFile($filePath);

$totalCount = count($jsonData['codes']);
$totalNotChecked = 0;
$creditersCount = [];
$uniqueCrediters = [];

foreach ($jsonData['codes'] as $data) {

  if ($data['status'] == "invalid") $totalNotChecked++;
  if ($data['credit'] != "") $creditersCount[$data['credit']] = $data['credit'];
  if ($data['credit'] != "" && !in_array($data['credit'], $uniqueCrediters)) array_push($uniqueCrediters, $data['credit']);
}

$crediters = '';
foreach ($uniqueCrediters as $uniqueCrediter) $crediters .= "$uniqueCrediter ";
$creditersCount = count($creditersCount);

$randomCode = array_rand($jsonData['codes'], 1);

$randomCodeSplitArr = str_split($randomCode);
$uniqueDigitsArr = array_unique(str_split($randomCode));
$uniqueDigitsList = '';
foreach ($uniqueDigitsArr as $d) $uniqueDigitsList .= "$d ";
$uniqueCount = count($uniqueDigitsArr);
$digitsNeededCount = count($randomCodeSplitArr);

echo "

<div class='row'>

  <div class='col-12 py-1 px-4'>

      <div class='card border border-primary bg-dark text-white'>
        <h5 class='card-header bg-primary text-white'>Stats</h5>
        <div class='card-body'>

          <div class='row h4 px-2'>

          <div class='col'>
            <div class='card p-2'>
            Invalid: $totalNotChecked<BR> 
            Total Codes: $totalCount
            </div>
          </div>

          <div class='col'>
            <div class='card p-2'>
            $creditersCount Users have helped crack the vault!
            <small>Credits go to: $crediters</small>
            </div>
          </div>


          <div class='col'>
            <div class='card p-2'>
            Unique Numbers: $uniqueCount <BR>
            Code Length: $digitsNeededCount <BR>
            Numbers Needed: $uniqueDigitsList

            </div>
          </div>

          </div>
        </div>
      </div>
  </div>
</div>
";