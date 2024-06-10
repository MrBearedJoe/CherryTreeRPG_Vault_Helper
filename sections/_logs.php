<div class='col-md-6 col-sm-12 py-1 px-4'>
  <div class='card border border-info bg-dark text-white'>
    <h5 class='card-header bg-info text-dark p-1' data-bs-toggle='collapse' data-bs-target='#collapseLogs'>
      Logs
      <small class='text-muted'>Click/Tap to show logs</small>
    </h5>
    <div class='card-body p-1 collapse' id='collapseLogs'>
      <?php
      $jsonData['logs'] = array_reverse($jsonData['logs']);
      foreach ($jsonData['logs'] as $key => $log) echo "<p class='my-1'>{$log[0]}</p>";
      ?>
    </div>
  </div>
</div>