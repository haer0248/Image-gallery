<?php
session_start();
error_reporting(0);
header('Content-Type: application/json; charset=UTF-8');

if ($_SESSION['user'] != null){
  if ($_SESSION['user'] == "test@user.com"){
    $msg = '測試者帳號無法移除圖片。';
    $type = 'error';
  }else{
    // $filesname = str_replace('https://'.$_SERVER['HTTP_HOST'].'/', '../', $_POST['file']);
    $filesname = "..".$_POST['file'];
    // $filesname = str_replace('/', '../', $filesname);
    // $filesname = str_replace('./', '../', $filesname);

    if ($filesname != null){
      $url = iconv('UTF-8', 'BIG5', "$filesname");
      if(file_exists($url)){
        unlink($url);
        $msg = $url.'移除成功！';
        $type = 'success';
      }else{
        $msg = $url.'不存在！';
        $type = 'error';
      }
    }else{
      $msg = '相片不存在！(0)';
      $type = 'error';
    }
  }
}else{
  $msg = '權限不足!';
  $type = 'error';
}
echo json_encode(array('msg' => $msg, 'type' => $type, 'dir' => $_POST['dir']));