<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);
require_csrf();
$pdo=db();$msg='';$err='';
if($_SERVER['REQUEST_METHOD']==='POST' && $pdo){
 try{
  $a=$_POST['action']??'';
  if($a==='save'){
    $id=(int)($_POST['id']??0);$type=$_POST['type']??'featured_listings';$title=trim($_POST['title']??'');$settings=trim($_POST['settings_json']??'{}');$sort=(int)($_POST['sort_order']??0);$active=!empty($_POST['is_active'])?1:0;
    json_decode($settings,true); if(json_last_error()!==JSON_ERROR_NONE){$settings='{}';}
    if($id>0){$st=$pdo->prepare('UPDATE home_sections SET type=:t,title=:ti,settings_json=:s,sort_order=:o,is_active=:a WHERE id=:id');$st->execute(['t'=>$type,'ti'=>$title,'s'=>$settings,'o'=>$sort,'a'=>$active,'id'=>$id]);}
    else {$st=$pdo->prepare('INSERT INTO home_sections (type,title,settings_json,sort_order,is_active,created_at) VALUES (:t,:ti,:s,:o,:a,NOW())');$st->execute(['t'=>$type,'ti'=>$title,'s'=>$settings,'o'=>$sort,'a'=>$active]);}
    $msg='Section kaydedildi.';
  } elseif($a==='delete'){$pdo->prepare('DELETE FROM home_sections WHERE id=:id')->execute(['id'=>(int)($_POST['id']??0)]);$msg='Section silindi.';}
 }catch(Throwable $e){$err=$e->getMessage();}
}
$rows=$pdo?$pdo->query('SELECT * FROM home_sections ORDER BY sort_order,id')->fetchAll():[];
?><!doctype html><html><head><meta charset="utf-8"><title>Sections</title><link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css');?>"></head><body><main style="margin:20px"><h1>Homepage Sections Builder</h1><p><a href="<?php echo url_path('admin.php');?>">Admin</a></p><?php if($msg):?><p style="color:green"><?php echo e($msg);?></p><?php endif;?><?php if($err):?><p style="color:red"><?php echo e($err);?></p><?php endif;?>
<form method="post"><?php echo csrf_input();?><input type="hidden" name="action" value="save"><select name="type"><option>featured_listings</option><option>auction_ending_soon</option><option>categories_grid</option><option>banner</option><option>html_block</option></select><input name="title" placeholder="Title" required><textarea name="settings_json" placeholder='{"limit":6}'></textarea><input type="number" name="sort_order" value="0"><label><input type="checkbox" name="is_active" checked>Active</label><button>Kaydet</button></form>
<table><tr><th>ID</th><th>Type</th><th>Title</th><th>Sort</th><th>Active</th><th>Action</th></tr><?php foreach($rows as $r):?><tr><td><?php echo (int)$r['id'];?></td><td><?php echo e($r['type']);?></td><td><?php echo e($r['title']);?></td><td><?php echo (int)$r['sort_order'];?></td><td><?php echo (int)$r['is_active'];?></td><td><form method="post" style="display:inline"><?php echo csrf_input();?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$r['id'];?>"><button>Sil</button></form></td></tr><?php endforeach;?></table></main></body></html>
