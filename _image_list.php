<?php
session_start();
error_reporting(0);
header('Content-Type: application/json; charset=UTF-8');

function images($dirs, $files, $img){
  $img = str_replace("./", "/", $img);
  $dirs = str_replace("./", "/", $dirs);
  $url = 'https://'.$_SERVER['HTTP_HOST'].$img;
  $r_url = 'https://'.$_SERVER['HTTP_HOST'].$dirs.$files;
  $r_file = $dirs.$files;
  if ($_SESSION['user'] == null){
    $display = '
      <a class="preview-div" href="'.$url.'" target="_blank">
        <div class="image">
          <img class="preview-image lazyload" data-original="'.$img.'" />
        </div>
        <div class="desc" title="'.$files.'">'.$files.'</div>
      </a>';
  }else{
    $display = '
      <a class="preview-div" id="preview-image" data-file="'.$r_file.'" data-src="'.$url.'" data-rurl="'.$r_url.'">
        <div class="image">
          <img class="preview-image lazyload" data-original="'.$img.'" />
        </div>
        <div class="desc" title="'.$files.'">'.$files.'</div>
      </a>';
  }
  return $display;
}

$link = $_GET['dirs'];
$link = htmlspecialchars_decode($link);
$link = str_replace("../", "", $link);

if ($link == "/" OR $link == "./"){
  $dirs = '.'.$link;
}else{
  $dirs = './'.$link.'/';
}

$checkExist = is_dir($dirs);

if ($checkExist){
  $handle = opendir($dirs);
  $count = count(glob("$dirs/*.*"));
  if ($count == 0){
    $files .= '
    <div class="alert alert-danger" role="alert">
      <h4 class="alert-title">資料夾 '.$dirs.' 不存在或是沒有檔案喔！</h4>
    </div>
    ';
  }else{
    $i = 1;
    while($file = readdir($handle)){
      $array[] = $file; 
    }
    sort($array);

    foreach($array as $file) {
      $file_type = explode('.', $file);
      if ($file_type[2] != null){
        $file_type = end(explode('.', $file));
      }else{
        $file_type = $file_type[1];
      }
      $file_type = strtolower($file_type);
      if (!(in_array($file, array(".", "..", ".htaccess", "robotx.txt", ".disable",)))) {
        if (!(in_array($file_type, array("php", "action", "disable", "otf", "ttf")))) {
          if (in_array($file_type, array('png', 'jpg', 'jpeg', 'webp', 'gif', 'bmp'))){
            $files .= images($dirs, $file, $dirs.$file);
          }else{
            if ($file_type == null){

            }else{
              if (file_exists("core/{$file_type}.png")){
                $files .= images($dirs, $file, './core/'.$file_type.'.png');
              }else{
                $files .= images($dirs, $file, './core/error.png');  
              }
            }
          }
        }
      }
    }

  }
  closedir($handle);

  $other .= "</div></div>";

  echo json_encode(array('files'=>$files, 'dir' => $dirs, 'msg' => "{$dirs}讀取成功。"));

}else{
  if ($dirs != "./admin/"){
    echo json_encode(array('msg'=> $dirs.' 不存在。'));
  }
}