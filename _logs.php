<?php
echo "
<div class='row'>
<div class='col-12 py-1 px-4'>

<div class='card border border-info bg-dark text-white'>
<h5 class='card-header bg-info text-white'>Logs</h5>
<div class='card-body'>
";
foreach ($jsonData['logs'] as $key => $log) {
  echo "<p>{$log[0]}</p>";
}
echo "
</div>
</div>
</div>
</div>
";