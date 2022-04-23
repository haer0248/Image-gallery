## Image-gallery
just a image gallery.

## 需求
PHP

## 檔案樹
/var/www/image<br>
│  nginx # nginx 設定檔案<br>
│  _images.php # 主要頁面<br>
│  _image_list.php # 主要頁面讀取列表檔案<br>
│<br>
└─file.action # 相關操作<br>
　│ folder-add.php # 新增資料夾<br>
　│ folder-remove.php # 移除資料夾<br>
　│ images-remove.php # 移除圖片<br>
　└─images-upload.php # 上傳圖片<br>
        
## 權限
chmod 755 /var/www/image -r<br>
chown www-data /var/www/image -r
