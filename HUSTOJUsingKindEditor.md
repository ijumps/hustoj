## 更改编辑器为KindEditor ##

1、从http://www.kindsoft.net/ 下载最新版KindEditor。

2、将KindEditor解压到OJ的kindeditor目录。

3、将kindeditor/php目录下的file\_manager\_json.php，upload\_json.php，JSON.php复制到OJ的根目录。

4、编辑upload\_json.php文件，下面两行对应修改如下：

```
$root_path = $php_path . 'upload/';
$root_url = $php_url . 'upload/';
```

同样的修改file\_manager\_json.php。

5、编辑admin/problem\_edit.php文件（下面行号以svn1109为例）。

1)删除16-18行：<?php

include\_once("../fckeditor/fckeditor.php") ;

?>

2)在第4行下面添加如下内容：
```
<link rel="stylesheet" href="../kindeditor/themes/default/default.css" />
<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript">
	KindEditor.ready(function(K){
			K.create('textarea[name="description"]',{
				uploadJson : '../upload_json.php',
				fileManagerJson : '../file_manager_json.php',
				allowFileManager: true,
			});
			K.create('textarea[name="input"]',{
				uploadJson : '../upload_json.php',
				fileManagerJson : '../file_manager_json.php',
				allowFileManager: true,
			});
			K.create('textarea[name="output"]',{
				uploadJson : '../upload_json.php',
				fileManagerJson : '../file_manager_json.php',
				allowFileManager: true,
			});
			K.create('textarea[name="hint"]',{
				uploadJson : '../upload_json.php',
				fileManagerJson : '../file_manager_json.php',
				allowFileManager: true,
			});
	});

</script>
```

6、修改第44行，
```
<p align=left>Description:<br><textarea rows=13 name=description cols=80><?php echo $row->description;?></textarea>
```

7、删除第46-54行，
```
<?php
$description = new FCKeditor('description') ;
$description->BasePath = '../fckeditor/' ;
$description->Height = 600 ;
$description->Width=600;

$description->Value = $row->description ;
$description->Create() ;
?>
```

8、重复6、7两部，修改57、71、86行，注意86行需要添加
```
<p align=left>Description:<br><textarea rows=13 name=description cols=80><?php echo $row->hint;?></textarea>
```

9、如果需要对上传进行权限设置，可以修改file\_manager\_json.php，upload\_json.php这两个文件