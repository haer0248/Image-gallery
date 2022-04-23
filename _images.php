<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Taipei');

$getNowUrl = explode('?', urldecode($_SERVER['REQUEST_URI']));
$getNowUrlWithoutQuery = explode('/', urldecode($getNowUrl[0]));
$link = str_replace('/', '', $getNowUrlWithoutQuery[1]);

// Generate hash password : https://tools.haer0248.me/password
$users = [
  "admin@example.com" => ''
];

$site_name = ' | 庫庫ㄉ圖片庫';

if ($_POST['account'] != null){
  $account  = $_POST['account'];
  $password = $_POST['password'];
  if ($users[$account] != null AND password_verify($password, $users[$account])){
    $_SESSION['msg'] = '登入成功';
    $_SESSION['user'] = $account;
    setcookie('userid', base64_encode($account), time()+31556926, '/', '.'.$_SERVER['HTT_HOST'], TRUE);
    Header("Location: /");
  }else{
    $_SESSION['msg'] = '登入失敗';
    Header('Location: ../admin');
  }
  exit;
}

if ($_COOKIE['userid'] != null AND $_SESSION['user'] == null){
  $account = base64_decode($_COOKIE['userid']);
  if ($users[$account] != null){
    $_SESSION['msg'] = '自動登入成功';
    $_SESSION['user'] = $account;
  }else{
    $_SESSION['msg'] = '未知帳號';
  }
  Header("Location: ../{$_SERVER['HTTP_REFERER']}"); exit;
}

if ($_SESSION['user'] != null){
  if ($link == "logout"){
    unset($_SESSION['user']);
    unset($_SESSION['admin']);
    setcookie('userid', base64_encode($account), time()-31556926, '/', '.'.$_SERVER['HTT_HOST'], TRUE);
    Header('Location: ../');
    exit;
  }
}

function cm(){
  $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220)/i";
  return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}

header('HTTP/1.1 200 OK');
?>
<!DOCTYPE HTML>
<html lang="zh-tw">
  <head>    
      
    <title><?php echo ($link == null)?'/':'/'.$link; ?> - <?= $site_name ?></title>

    <meta http-equiv="Content-Type" content="text/html" charset="uft-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <link rel="stylesheet" href="https://unpkg.com/@tabler/core@latest/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
    <script src="https://unpkg.com/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.0/js/bootstrap.min.js"></script>
    
    <style>
    body {
      color: #FFF!important;
    }
    .bg-remove {
      background-color: #FFF0!important;
      color: #FFF!important;
    }
    .text-white {
      color: #FFF!important;
    }
    a:hover {
      background-color: #242424!important;
    }
    .preview-image {
      object-fit: contain;
      height: 150px;
      width: 100%;
    }
    .preview-div {      
      position: relative;
      display: block;
      height: <?php if(!cm())echo'180px';else'300px'?>;
      width: <?php if(!cm())echo'260px';else'100%'?>;
    }
    .preview-body {
      display: flex;
      overflow: hidden;
      flex-wrap: wrap;
      max-width: 100%;
    }
    .desc {
      text-align: center;
      overflow : hidden;
    }
    a {
      color: #FFF;
    }
    .item {
      color: #FFF!important;
    }
    h3 {
      padding-top: 2px;
      padding-left: 15px;
      margin: 0px!important;
      width: 100%;
      text-align: center;
    }
    </style>
    
    <script type="text/javascript">
      <?php
        if ($_SESSION['msg'] != null){
          echo 'ui_alert("'.$_SESSION['msg'].'");';
          unset($_SESSION['msg']);
        }
      ?>
      
      function ui_alert(msg){
        toastr.info(msg)
      }

      function MouseWheel (e) {
        e = e || window.event;
        $("img").lazyload();
      }

      if ('onmousewheel' in window) {
        window.onmousewheel = MouseWheel;
      } else if ('onmousewheel' in document) {
        document.onmousewheel = MouseWheel;
      } else if ('addEventListener' in window) {
        window.addEventListener("mousewheel", MouseWheel, false);
        window.addEventListener("DOMMouseScroll", MouseWheel, false);
      }
      <?php 
        if ($_SESSION['user'] != null){
          echo '
          $(document).on("click", "button[id=add-folder-btn]", function() {
            $.ajax({
              type: "POST", url: "../file.action/folder-add.php", dataType: "json",
              data: { folder: $("input[id=folder]").val() },
              success: function(data) {
                ui_alert(data.msg);
                if (data.type == "success"){
                  readFile(data.dfolder);
                }
              },
              error: function(jqXHR) {
                ui_aleert("發生錯誤：" + jqXHR.status + "!");
              }
            })
          });

          document.addEventListener("paste", function (event) {
            var items = event.clipboardData && event.clipboardData.items;
            var file = null;
            if (items && items.length) {
              for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") !== -1) {
                  file = items[i].getAsFile();
                  break;
                }
              }
            } else {
              ui_alert("瀏覽器不支援貼上圖片！");
            }
            if (!file) {
              ui_alert("貼上內容非圖片！");
            }else{
              ui_alert("已檢測到圖片，正在上傳中！");
              var fd = new FormData();
              fd.append("file", file);
              var xhr = new XMLHttpRequest();
              uploadData(fd);
            }
          });
          
          $(function() {
            $("html").on("dragover", function(e) {
              e.preventDefault();
              e.stopPropagation();
            });
            $("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });
            $(".upload-area").on("dragenter", function (e) {
              e.stopPropagation();
              e.preventDefault();
            });
            $(".upload-area").on("dragover", function (e) {
              e.stopPropagation();
              e.preventDefault();
            });
            $(".upload-area").on("drop", function (e) {
              e.stopPropagation();
              e.preventDefault();
              var file = e.originalEvent.dataTransfer.files;
              var fd = new FormData();
              fd.append("file", file[0]);
              uploadData(fd);
            });
            $("#remove-folder").click(function(){
              $.ajax({
                url: "../file.action/folder-remove.php?dir="+$("span[id=loc]").html(),
                type: "post",
                dataType: "json",
                success: function(data) {
                  ui_alert(data.msg);
                  readFile("/");
                }
              });
            });
            $(document).on("click", "button[id=upload-files]", function() {
              $("#file").click();
            });
            $(document).on("change", "#file", function() {
              var fd = new FormData();
              var files = $("#file")[0].files[0];
              fd.append("file",files);
              uploadData(fd);
            });
          });
          
          function uploadData(formdata){
            $.ajax({
              url: "../file.action/images-upload.php?dir="+$("input[id=dir]").val(),
              type: "post",
              data: formdata,
              contentType: false,
              processData: false,
              dataType: "json",
              success: function(data) {
                readFile(data.dir);
                ui_alert(data.msg);
                runcopy(data.url);
                $("#upload_status").html(data.msg)
                $("a[data-src=\'"+data.filename+"\']").click();
                $("a[data-src=\'"+data.filename+"\']").addClass("uploaded");
              }
            });
          }

          $(document).on("click", "button[id=remove]", function (){
            $.ajax({
              type: "POST", url: "../file.action/images-remove.php", dataType: "json",
              data: { file: $(this).attr("data-file"), dir: $("input[id=dir]").val() },
              success: function(data) {
                ui_alert(data.msg);
                readFile(data.dir);
                $("#image-modal").modal("hide");
              },
              error: function(jqXHR) {
                ui_alert("發生錯誤："+ jqXHR.status + "!");
              }
            })
          });
          ';
        }
        echo '

        $(document).on("click", "a[id=preview-image]", function (){
          url = $(this).attr("data-src");

          $("[id=remove]").attr("data-file", $(this).attr("data-file"));
          $("[id=go]").attr("href", $(this).attr("data-rurl"));
          $("img[id=preview-image]").attr("src", url);
          runcopy($(this).attr("data-rurl"));
          $("#image-modal").modal("show");
        })';
      ?>
    </script>

  </head>
  <body class="theme-dark">
    <div class="wrapper">
      <aside class="navbar navbar-vertical navbar-expand-lg navbar-dark">
      
        <div class="container-fluid">
        
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <?php
            if ($_SESSION['user'] != null){
              $info['name'] = $_SESSION['user'];

              $folder_list .= '
              <li class="nav-item">
                <a class="nav-link" id="folder-switch" href="../logout">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M7 12h14l-3 -3m0 6l3 -3" /></svg>
                  </span>
                  <span class="nav-link-title">
                    登出
                  </span>
                </a>
              </li>';
            }else{
              $info['name'] = '訪客 Guest';

              $folder_list .= '
              <li class="nav-item">
                <a class="nav-link" id="folder-switch" href="../admin">
                  <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M20 12h-13l3 -3m0 6l-3 -3" /></svg>
                  </span>
                  <span class="nav-link-title">
                    登入
                  </span>
                </a>
              </li>';
            }
          ?>
          
          <!-- Blank Area -->
          <div class="navbar-nav flex-row d-lg-none">
          </div>
          
          <?php

          $folder_list .= '
          <li class="nav-item">
            <a class="nav-link" href="/">
              <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="5 12 3 12 12 3 21 12 19 12" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
              </span>
              <span class="nav-link-title">
                根目錄
              </span>
            </a>
          </li>
          ';
          $handle = opendir("./");
          $count  = count(glob("./*.*"));
          if ($count == 0){
            
          }else{
            $allow_folder = array();
            while($file = readdir($handle)){
              $allow_folder[] = $file; 
            }
            sort($allow_folder);
            foreach($allow_folder as $file) {
              $file_type = explode('.', $file);
              if ($file_type[2] != null){
                $file_type = end(explode('.', $file));
              }else{
                $file_type = $file_type[1];
              }
              $file_type = strtolower($file_type);
              if (!(in_array($file, array(".", "..", ".htaccess", "robotx.txt", ".disable")))) {
                if ($file_type == null){
                  $folder_list .= '
                  <li class="nav-item">
                    <a class="nav-link" id="folder-switch" data-folder="'.$file.'">
                      <span class="nav-link-icon d-md-none d-lg-inline-block">
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2" /></svg>
                      </span>
                      <span class="nav-link-title">
                        '.$file.'/
                      </span>
                    </a>
                  </li>
                  ';
                }
              }
            }
          }

          ?>
          
          <!-- Folder list -->
          <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">
            
              <?= $folder_list; ?>
              
            </ul>
          </div>
        </div>
      </aside>
      
      <div class="page-wrapper">
        <div class="page-body">
          <div class="container-fluid">
            <?php if ($_SESSION['user'] != null){ ?>
              <div class="row row-cards">
                <!-- Upload area -->
                <div class="col-lg-6">
                  <div class="card">
                    <div class="empty">
                      <p class="empty-title">上傳至 「<span id="loc"><?= $show_dirs?></span>」</p>
                      <span id="upload_status"></span>
                      <button class="btn btn-primary" id="upload-files">透過瀏覽器選擇</button>
                    </div>
                  </div>
                  <input type="file" name="file" id="file" style="display: none;">
                </div>
                
                <!-- Folder create area -->
                <div class="col-lg-6">
                  <div class="card">
                    <div class="empty">
                      <p class="empty-title">新增資料夾</p>
                      <div class="input-group">
                        <input type="text" class="form-control" id="folder">
                        <button class="btn" type="button" id="add-folder-btn">建立</button>
                      </div>
                    </div>
                  </div>
                </div>
                
              </div>
            <?php } else if ($link == "admin") { ?>
              <form action="images.php" method="POST">

                <fieldset class="form-fieldset">
                  <div class="mb-3">
                    <label class="form-label required">帳號</label>
                    <input id="account" name="account" type="text" class="form-control" required="required">
                  </div>
                  <div class="mb-3">
                    <label class="form-label required">密碼</label>
                    <input id="password" name="password" type="password" class="form-control" required="required">
                  </div>
                </fieldset>
                <button type="submit" class="btn btn-primary">登入帳號</button>

              </form>
            <?php } ?>
            
            <div class="preview-body" id="ImageList"></div>
            
            <div class="modal fade" tabindex="-1" aria-hidden="true" id="image-modal">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <img class="image" src="" id="preview-image" style="width: 100%;"/>
                  </div>
                  <div class="modal-footer">
                    <a href="" class="btn me-auto" id="go" target="_blank">新開分頁</a>
                    <button class="btn btn-danger" data-file="" id="remove">刪除</button>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>

    <input type="hidden" id="dir" value="/">
    <script>
      readFile("<?php echo ($link == null)?'/':$link; ?>");
      function readFile(folder){
        $("input[id=dir]").val(folder);
        $("span[id=loc]").html(`${folder}`);
        $.ajax({
          url: "../_image_list.php?dirs="+folder,
          type: "post",
          dataType: "json",
          success: function(data) {
            // ui_alert(data.msg);
            $("#ImageList").html(data.files);
            $("img.lazyload").lazyload();
          }    
        });
      }

      $(document).on("click", "#folder-switch", function(){
        var folder = $(this).attr("data-folder");
        history.pushState(null, folder + " - <?= $site_name ?>" , folder);
        document.title = folder + " - <?= $site_name ?>";
        $("[id=folder-switch]").removeClass("active");
        $(this).addClass("active");
        readFile(folder);
      });
      
      function runcopy(url) {
        var str = url;
        var el = document.createElement("textarea");
        el.value = str;
        document.body.appendChild(el);
        el.select();
        document.execCommand("copy");
        el.setAttribute("hidden", true);
          
        ui_alert("已複製"+url+"！");
      }
    </script>
  </body>
</html>
