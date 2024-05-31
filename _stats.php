<?php
$jsonData = openFile($filePath);

$totalCount = count($jsonData['codes']);
$totalNotChecked = 0;
foreach ($jsonData['codes'] as $data) if ($data['status'] == "invalid") $totalNotChecked++;

$creditersCount = [];
foreach ($jsonData['codes'] as $data) if ($data['credit'] != "") $creditersCount[$data['credit']] = $data['credit'];
$creditersCount = count($creditersCount);


echo "

<div class='row'>
<div class='col-12 py-1 px-4'>

<div class='card border border-primary bg-dark text-white'>
<h5 class='card-header bg-primary text-white'>Stats</h5>
<div class='card-body'>


<div class='row h4 px-2'>

<div class='col'>
<div class='card'>
$totalNotChecked of $totalCount <BR> Has been checked!
</div></div>

<div class='col'>
<div class='card'>
$creditersCount Users have helped<br> crack the vault!
</div></div>


<div class='col'>
<div class='card'>
Digits Needed to Crack:

</div></div>

</div>
</div>
</div>
</div>
</div>
";
