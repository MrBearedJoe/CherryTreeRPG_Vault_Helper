<?php
$jsonData = openFile($filePath);

$totalCount = count($jsonData['codes']);
$totalNotChecked = 0;
foreach($jsonData['codes'] as $data) if($data['status'] == "invalid") $totalNotChecked++;

$creditersCount = [];
foreach($jsonData['codes'] as $data) if($data['credit'] != "") $creditersCount[$data['credit']]=$data['credit'];
$creditersCount = count($creditersCount);


echo "
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
";


echo "<HR class='bg-white my-2'>";