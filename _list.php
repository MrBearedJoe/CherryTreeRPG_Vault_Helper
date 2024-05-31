<?php
$jsonData = openFile($filePath);
echo "
<div class='row'>
<div class='col-12 py-1 px-4'>

<div class='card border border-success bg-dark text-white'>
<h5 class='card-header bg-success text-white'>Codes</h5>
<div class='card-body'>
<table class='table table-sm table-dark my-3 border'>
<tr>
<th>Code</th>
<th>Status</th>
<th>Credit</th>
</tr>
";

foreach ($jsonData['codes'] as $code => $data) {
  echo "<tr>
  <td>$code </td>
  <td>{$data['status']} </td>
  <td>{$data['credit']}</td>
  </tr>";
}

echo "

</table>
</div>
</div>
</div>
</div>
";
