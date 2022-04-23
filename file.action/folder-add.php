<?php
session_start();
error_reporting(0);
header('Content-Type: application/json; charset=UTF-8');

if ($_SESSION['user'] != null){
  if ($_SESSION['user'] == "test@user.com"){
    $msg = '測試者帳號無法新增資料夾。';
  }else{
    $folder = '../'.$_POST['folder'];

    if (!is_dir($folder)) {
      if (mkdir($folder)){
        $msg = '資料夾' . $folder . '建立成功!';
        $type = 'success';
        $dfolder = $_POST['folder'];
      }else{
        $msg = '資料夾' . $folder . '建立失敗!';
      }
    }else{
      $msg = '資料夾' . $folder . '已存在!';
    }
  }
}else{
  $msg = '權限不足!';
}

echo json_encode(array('msg'=>$msg, 'type'=>$type, 'dfolder'=>$dfolder));