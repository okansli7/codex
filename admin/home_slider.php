<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);
require_csrf();
$pdo = db();
$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST' && $pdo) {
  try {
    $action=$_POST['action']??'';
    if ($action==='save') {
      $id=(int)($_POST['id']??0);
      $title=trim($_POST['title']??'');
      $subtitle=trim($_POST['subtitle']??'');
      $btnText=trim($_POST['button_text']??'');
      $btnUrl=trim($_POST['button_url']??'');
      $sort=(int)($_POST['sort_order']??0);
      $active=!empty($_POST['is_active'])?1:0;
      $image=trim($_POST['image_path']??'');
      try { $up=upload_image('image_file','slide_'); if($up){$image=$up;} } catch(Throwable $e){}
      if($id>0){
        $st=$pdo->prepare('UPDATE home_slides SET title=:t,subtitle=:s,button_text=:bt,button_url=:bu,image_path=:i,sort_order=:o,is_active=:a WHERE id=:id');
        $st->execute(['t'=>$title,'s'=>$subtitle,'bt'=>$btnText,'bu'=>$btnUrl,'i'=>$image,'o'=>$sort,'a'=>$active,'id'=>$id]);
      } else {
        $st=$pdo->prepare('INSERT INTO home_slides (title,subtitle,button_text,button_url,image_path,sort_order,is_active,created_at) VALUES (:t,:s,:bt,:bu,:i,:o,:a,NOW())');
        $st->execute(['t'=>$title,'s'=>$subtitle,'bt'=>$btnText,'bu'=>$btnUrl,'i'=>$image,'o'=>$sort,'a'=>$active]);
      }
      $msg='Slide kaydedildi.';
    } elseif ($action==='delete') {
      $pdo->prepare('DELETE FROM home_slides WHERE id=:id')->execute(['id'=>(int)($_POST['id']??0)]);
      $msg='Slide silindi.';
    }
  } catch(Throwable $e){ $err=$e->getMessage(); }
}
$rows=[]; if($pdo){$rows=$pdo->query('SELECT * FROM home_slides ORDER BY sort_order,id')->fetchAll();}
?><!doctype html><html><head><meta charset="utf-8"><title>Slider</title><link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css');?>"></head><body><main style="margin:20px"><h1>Homepage Slider Manager</h1><p><a href="<?php echo url_path('admin.php');?>">Admin</a></p><?php if($msg):?><p style="color:green"><?php echo e($msg);?></p><?php endif;?><?php if($err):?><p style="color:red"><?php echo e($err);?></p><?php endif;?>
<form method="post" enctype="multipart/form-data"><?php echo csrf_input();?><input type="hidden" name="action" value="save"><input name="title" placeholder="Title" required><input name="subtitle" placeholder="Subtitle"><input name="button_text" placeholder="Button text"><input name="button_url" placeholder="Button URL"><input name="image_path" placeholder="Image URL"><input type="file" name="image_file" accept="image/jpeg,image/png,image/webp"><input type="number" name="sort_order" value="0"><label><input type="checkbox" name="is_active" checked> Active</label><button>Kaydet</button></form>
<table><tr><th>ID</th><th>Title</th><th>Sort</th><th>Active</th><th>Action</th></tr><?php foreach($rows as $r):?><tr><td><?php echo (int)$r['id'];?></td><td><?php echo e($r['title']);?></td><td><?php echo (int)$r['sort_order'];?></td><td><?php echo (int)$r['is_active'];?></td><td><form method="post" style="display:inline"><?php echo csrf_input();?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$r['id'];?>"><button>Sil</button></form></td></tr><?php endforeach;?></table></main></body></html>
