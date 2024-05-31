<?php
echo"<h4>Logs</h4>";
foreach($jsonData['logs'] as $key => $log){
  echo "<p>{$log[0]}</p>";
}