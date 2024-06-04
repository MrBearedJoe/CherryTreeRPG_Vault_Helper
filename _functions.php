<?php

function openFile($filePath)
{
  $file = file_get_contents($filePath);
  return json_decode($file, true);
}

function updateFile($filePath, $JSONdates)
{
  $fp = fopen($filePath, 'w');
  fwrite($fp, json_encode($JSONdates));
  fclose($fp);
}

function sanitize($data)
{
  if (is_array($data)) {
    foreach ($data as $key => $str) {
      $data[$key] = filter_var($str, FILTER_SANITIZE_STRING);
    }
  } else {
    $data = filter_var($data, FILTER_SANITIZE_STRING);
  }

  return $data;
}
