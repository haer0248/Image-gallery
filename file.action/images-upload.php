<?php
session_start();
error_reporting(0);
header('Content-Type: application/json; charset=UTF-8');

function randid(){
  $id = '';
  $word = 'ABCDEFGHIJKLMONPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz1234567890';
  $len = strlen($word);

  for($i = 0; $i < 6; $i++){
    $id .= $word[rand() % $len];
  }
  return $id;
}

if ($_SESSION['user'] != null){
  if ($_SESSION['user'] == "test@user.com"){
    $msg = '測試者帳號無法上傳圖片。';
  }else{
    $dirs = $_GET['dir'];
    if ($dirs == null){
      $url = '../';
    }else{
      $url = '../'.$dirs.'/';
    }
    $error = $_FILES["file"]["error"];
    if ($error == UPLOAD_ERR_OK) {
      $filename = $_FILES['file']['name'];
      // $filename = iconv("UTF-8", "big5", $_FILES["file"]["name"]);
      // $filename = mb_convert_encoding($_FILES["file"]["name"], "UTF-8", "big5");
      // $filename = mb_convert_encoding($_FILES["file"]["name"], "big5", "UTF-8");
      $filesize = $_FILES['file']['size'];
      $filetype = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
      $filetype = strtolower($filetype);
      if ($filetype == 'jpg' OR $filetype == 'gif' OR $filetype == 'png' OR $filetype == 'bmp' OR $filetype == 'jpeg'){
        $new_name = randid().'.'.$filetype;
      }else{
        $new_name = $filename;
      }
      $location = $url.$new_name;
      move_uploaded_file($_FILES['file']['tmp_name'],$location);
    }
    if ($new_name) {
      $msg = $new_name.'上傳成功！';
    } else {
      $msg = '未知錯誤'.$error;
    }
  }
}else{
  $msg = '權限不足。';
}
$url = str_replace("../", "https://{$_SERVER['HTTP_HOST']}/", $location);
$url = str_replace("///", "/", $url);
echo json_encode(array('msg'=> $msg, 'filename' => $new_name, 'dir' => $dirs, 'url' => $url));
