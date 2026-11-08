<?php
require __DIR__ . '/config.php';
header('Content-Type: application/json');
function np($p){ return rtrim(str_replace('\\','/', $p),'/'); }
function normalize_text($s){ $s=mb_strtolower($s,'UTF-8'); $s=preg_replace('/[^a-z0-9]+/u','',$s); return trim($s); }
function buildTree($dir,$baseRel='',$maxDepth=10,$currDepth=0){
 $dir=np($dir); $items=[];$files=[];$dirs=[];
 if($currDepth>$maxDepth) return ['items'=>[],'count'=>0];
 $entries=@scandir($dir); if($entries===false) return ['items'=>[],'count'=>0];
 foreach($entries as $f){
  if($f=='.'||$f=='..') continue;
  if($f==='backups') continue;
  $full=$dir.'/'.$f;
  if(is_dir($full)){
   $sub=buildTree($full,$baseRel?$baseRel.'/'.$f:$f,$maxDepth,$currDepth+1);
   $dirs[]=['type'=>'dir','name'=>$f,'path'=>($baseRel?$baseRel.'/'.$f:$f),'full'=>$full,'children'=>$sub['items'],'count'=>$sub['count']];
  } else {
   $ext=strtolower(pathinfo($f,PATHINFO_EXTENSION));
   if($ext==='csv'){
    $sz=@filesize($full)?:0; $hr=$sz>1048576?round($sz/1048576,1).' MB':($sz>1024?round($sz/1024,1).' KB':$sz.' B');
    $files[]=['type'=>'file','name'=>$f,'path'=>$full,'rel'=>($baseRel?$baseRel.'/'.$f:$f),'size'=>$hr,'ext'=>$ext];
   }
  }
 }
 usort($dirs,fn($a,$b)=>strcasecmp($a['name'],$b['name']));
 usort($files,fn($a,$b)=>strcasecmp($a['name'],$b['name']));
 return ['items'=>array_merge($dirs,$files),'count'=>count($files)+array_sum(array_map(fn($d)=>$d['count'],$dirs))];
}
function detectDelimiter($filePath){
 $fh=@fopen($filePath,'r'); if(!$fh) return ',';
 $line1=fgets($fh); fclose($fh);
 if($line1===false) return ',';
 $tab1=substr_count($line1,"\t"); $comma1=substr_count($line1,",");
 if($tab1>$comma1 && $tab1>0) return "\t";
 return ",";
}
function listImages($dir, $recursive=true){
 $dir=np($dir); $images=[]; $folders=[];
 if(!is_dir($dir)) return ['images'=>[],'folders'=>[],'error'=>"Not a folder: $dir"];
 if($recursive){
   try{
     $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
     foreach($it as $file){
       if(count($images)>=300) break;
       if($file->isFile()){
         $ext=strtolower($file->getExtension());
         if(in_array($ext,['jpg','jpeg','png','gif','webp','bmp'])){
           $path=np($file->getPathname());
           $images[]=['path'=>$path,'name'=>$file->getFilename(),'folder'=>np($file->getPath()),'size'=>@filesize($path)];
         }
       }
     }
   }catch(Exception $e){ return ['images'=>[],'folders'=>[],'error'=>$e->getMessage()]; }
 } else {
   $entries=@scandir($dir);
   if($entries){
     foreach($entries as $f){
       if($f=='.'||$f=='..') continue;
       $full=$dir.'/'.$f;
       if(is_dir($full)){ $folders[]=np($full); }
       else{
         $ext=strtolower(pathinfo($f,PATHINFO_EXTENSION));
         if(in_array($ext,['jpg','jpeg','png','gif','webp','bmp'])){
           $images[]=['path'=>np($full),'name'=>$f,'folder'=>$dir,'size'=>@filesize($full)];
         }
       }
       if(count($images)>=300) break;
     }
   }
 }
 usort($folders, 'strcasecmp');
 usort($images, fn($a,$b)=>strcasecmp($a['name'],$b['name']));
 return ['images'=>$images,'folders'=>$folders];
}
$action=$_GET['action']??'';
if($action==='list'){
 $root=np($_GET['root']??''); if($root==='') $root=DEFAULT_CSV_ROOT;
 $root=np($root); if(!is_dir($root)) $root=DEFAULT_CSV_ROOT;
 $tree=buildTree($root);
 echo json_encode(['root'=>$root,'parent'=>np(dirname($root)),'total'=>$tree['count'],'tree'=>$tree['items']]);
 exit;
}
if($action==='read'){
 $file=np($_GET['file']??''); if(!file_exists($file)){echo json_encode(['error'=>"File not found: $file"]); exit;}
 $delim=detectDelimiter($file);
 $fh=fopen($file,'r'); $h=fgetcsv($fh,0,$delim); $rows=[]; while(($r=fgetcsv($fh,0,$delim))!==false) $rows[]=$r; fclose($fh);
 echo json_encode(['headers'=>$h?:[],'rows'=>$rows,'path'=>$file]);
 exit;
}
if($action==='write'){
 $in=json_decode(file_get_contents('php://input'),true);
 $file=np($in['file']??''); $fmt=$in['format']??'csv'; $isCopy=$in['isCopy']??false;
 $delim=$fmt==='tsv'?"\t":",";
 @mkdir(dirname($file),0777,true);
 if(file_exists($file)){
  $ts=time(); $baseName=pathinfo($file,PATHINFO_FILENAME);
  @mkdir(BACKUP_DIR,0777,true);
  $backupFile=np(BACKUP_DIR.'/'.$baseName.'-'.$ts.'.bak');
  @copy($file, $backupFile); $backupToReturn=$backupFile;
 } else { $backupToReturn=null; }
 $fh=fopen($file,'w'); fputcsv($fh,$in['headers'],$delim);
 foreach($in['rows'] as $r) fputcsv($fh,$r,$delim);
 fclose($fh);
 echo json_encode(['success'=>true,'path'=>np($file),'backup'=>$backupToReturn?np($backupToReturn):null,'isCopy'=>$isCopy]);
 exit;
}
if($action==='upload_image'){
 $root=np($_GET['root']??'');
 if($root===''||!is_dir($root)) $root=DEFAULT_CSV_ROOT;
 $root=np($root);
 if(!isset($_FILES['image'])){ echo json_encode(['error'=>'No file uploaded']); exit; }
 $f=$_FILES['image'];
 $ext=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
 if(!in_array($ext,['jpg','jpeg','png','gif','webp','bmp'])){ echo json_encode(['error'=>'Invalid image type']); exit; }
 $safeName=preg_replace('/[^A-Za-z0-9._-]/','_',$f['name']);
 // Avoid overwrite: add timestamp if exists
 $dest=np($root.'/'.$safeName);
 if(file_exists($dest)){
   $base=pathinfo($safeName,PATHINFO_FILENAME); $ext2=pathinfo($safeName,PATHINFO_EXTENSION);
   $dest=np($root.'/'.$base.'_'.time().'.'.$ext2);
 }
 if(!@move_uploaded_file($f['tmp_name'],$dest)){
   // fallback to images folder
   @mkdir(__DIR__.'/images',0777,true);
   $dest=np(__DIR__.'/images/'.$safeName);
   @move_uploaded_file($f['tmp_name'],$dest);
   if(!file_exists($dest)){ echo json_encode(['error'=>'Failed to save to '.$dest]); exit; }
 }
 echo json_encode(['success'=>true,'path'=>np($dest),'name'=>basename($dest)]);
 exit;
}
if($action==='image'){
 $file=np($_GET['file']??'');
 if(!file_exists($file) || !is_file($file)){
   header('HTTP/1.1 404 Not Found');
   header('Content-Type: application/json');
   echo json_encode(['error'=>'Image not found: '.$file]);
   exit;
 }
 $ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));
 $mime = match($ext){ 'jpg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','bmp'=>'image/bmp',default=>'image/jpeg' };
 header('Content-Type: '.$mime);
 header('Cache-Control: public, max-age=3600');
 readfile($file);
 exit;
}
if($action==='list_images'){
 $root=np($_GET['root']??'D:/web/csv/studio');
 $recursive = ($_GET['recursive']??'1')==='1';
 $data=listImages($root, $recursive);
 echo json_encode(['root'=>$root,'parent'=>np(dirname($root)),'images'=>$data['images'],'folders'=>$data['folders']??[],'count'=>count($data['images']),'error'=>$data['error']??null]);
 exit;
}
if($action==='global_search'){
 $q=$_GET['q']??''; $root=np($_GET['root']??DEFAULT_CSV_ROOT);
 $parts=preg_split('/[\n,;|]+/',$q); $ph=[]; $np_arr=[]; foreach($parts as $p){$p=trim($p); if($p==='')continue; $ph[]=$p; $np_arr[]=normalize_text($p);}
 $res=[]; $max=500;
 $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS));
 foreach($it as $file){
  if(count($res)>=$max) break; if(!$file->isFile()) continue;
  $ext=strtolower($file->getExtension()); if($ext!=='csv') continue;
  if(str_contains($file->getFilename(),'.bak')) continue;
  $path=np($file->getPathname()); $fh=@fopen($path,'r'); if(!$fh) continue;
  $delim=detectDelimiter($path); $rn=0;
  while(($row=fgetcsv($fh,0,$delim))!==false){ $rn++; $line=implode(' ',$row); $nl=normalize_text($line); $ok=true; foreach($np_arr as $need){ if(strpos($nl,$need)===false){$ok=false;break;}} if($ok){ $res[]=['file'=>$path,'rel'=>substr($path,strlen($root)+1),'row'=>$rn,'content'=>mb_substr($line,0,500)];}}
  fclose($fh);
 }
 echo json_encode(['phrases'=>$ph,'results'=>$res,'root'=>$root]);
 exit;
}
echo json_encode(['error'=>'unknown']);
