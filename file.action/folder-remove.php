<?php
session_start();
error_reporting(0);
header('Content-Type: application/json; charset=UTF-8');

if ($_SESSION['user'] != null){
  $dir = $_GET['dir'];
  if (in_array($dir, array('/', '/core/'))){
    $msg = '資料夾' .$dir. '無法移除!';
  }else{
    $dir = '../'.$dir;
    
    if (is_dir($dir)) {
      if (rmdir($dir)){
        $msg = '資料夾' .$dir. '移除成功!';
      }else{
        $msg = '資料夾' .$dir. '移除失敗!';
      }
      // $msg = '資料夾' .$dir. '存在!';
    }else{
      $msg = '資料夾' .$dir. '不存在!';
    }
    
  }
}else{
  $msg = '權限不足!';
}

echo json_encode(array('msg' => $msg));