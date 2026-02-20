<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);
require_csrf();
$pdo=db();$msg='';$err='';
if($_SERVER['REQUEST_METHOD']==='POST' && $pdo){
 try{
  $a=$_POST['action']??'';
  if($a==='save'){
    $slug=slugify($_POST['slug']??'');$title=trim($_POST['title']??'');$body=$_POST['body_html']??'';
    if($slug===''){throw new RuntimeException('Slug zorunlu');}
    $st=$pdo->prepare('INSERT INTO pages_static (slug,title,body_html,updated_at) VALUES (:s,:t,:b,NOW()) ON DUPLICATE KEY UPDATE title=VALUES(title),body_html=VALUES(body_html),updated_at=NOW()');
    $st->execute(['s'=>$slug,'t'=>$title,'b'=>$body]);log_audit('static_page_saved','pages_static', null);$msg='Sayfa kaydedildi.';
  } elseif($a==='delete'){ $slugDel = $_POST['slug']??''; $pdo->prepare('DELETE FROM pages_static WHERE slug=:s')->execute(['s'=>$slugDel]); log_audit('static_page_deleted','pages_static', null); $msg='Sayfa silindi.'; }
 }catch(Throwable $e){$err=$e->getMessage();}
}
$rows=$pdo?$pdo->query('SELECT * FROM pages_static ORDER BY slug')->fetchAll():[];
?><!doctype html><html><head><meta charset="utf-8"><title>Static Pages</title><link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css');?>"></head><body><main style="margin:20px"><h1>Static Pages Manager</h1><p><a href="<?php echo url_path('admin.php');?>">Admin</a></p><?php if($msg):?><p style="color:green"><?php echo e($msg);?></p><?php endif;?><?php if($err):?><p style="color:red"><?php echo e($err);?></p><?php endif;?>
<form method="post"><?php echo csrf_input();?><input type="hidden" name="action" value="save"><input name="slug" placeholder="about/privacy/terms/faq" required><input name="title" placeholder="Başlık" required><textarea name="body_html" placeholder="HTML içerik"></textarea><button>Kaydet</button></form>
<table><tr><th>Slug</th><th>Title</th><th>Action</th></tr><?php foreach($rows as $r):?><tr><td><?php echo e($r['slug']);?></td><td><?php echo e($r['title']);?></td><td><a href="<?php echo url_path('pages/static.php');?>?slug=<?php echo urlencode($r['slug']);?>" target="_blank">Gör</a> <form method="post" style="display:inline"><?php echo csrf_input();?><input type="hidden" name="action" value="delete"><input type="hidden" name="slug" value="<?php echo e($r['slug']);?>"><button>Sil</button></form></td></tr><?php endforeach;?></table></main></body></html>
