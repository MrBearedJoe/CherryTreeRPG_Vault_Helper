<?php

function openFile($filePath){
  $file = file_get_contents($filePath);
  return json_decode($file, true);
}

function updateFile($filePath,$JSONdates){
  $fp = fopen($filePath, 'w');
  fwrite($fp, json_encode($JSONdates));
  fclose($fp);
}