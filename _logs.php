<?php
echo "
<div class='row'>
  <div class='col-12 py-1 px-4'>
    <div class='card border border-info bg-dark text-white'>
      <h5 class='card-header bg-info text-white p-1'>Logs</h5>
      <div class='card-body p-1'>
      ";

foreach ($jsonData['logs'] as $key => $log) echo "<p class='my-1'>{$log[0]}</p>";

echo "
      </div>
    </div>
  </div>
</div>
";
