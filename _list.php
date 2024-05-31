<?php
$jsonData = openFile($filePath);
echo "
<table class='table table-sm table-dark my-3 border'>
<tr><th colspan='3' class='h3'>Codes</th></tr>
<tr>
<th>Code</th>
<th>Status</th>
<th>Credit</th>
</tr>
";

foreach($jsonData['codes'] as $code => $data){
  echo "<tr>
  <td>$code </td>
  <td>{$data['status']} </td>
  <td>{$data['credit']}</td>
  </tr>";
}

echo "

</table>";

