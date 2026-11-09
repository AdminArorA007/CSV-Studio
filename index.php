<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>CSV Studio Sturdy Ver 1.1.2</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
.cell{cursor:cell}.cell-editing{outline:2px solid #6366f1;background:#eef2ff!important;box-shadow:0 0 0 2px #c7d2fe}
mark{background:#fef08a;padding:0 2px;border-radius:2px;font-weight:600}
::-webkit-scrollbar{width:6px;height:6px}::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:6px}
.resizer{width:5px;cursor:col-resize;position:absolute;right:0;top:0;bottom:0}.resizer:hover{background:#6366f1}
#saveToast{transition:opacity .3s, transform .3s} .tool{padding:5px 10px;background:#fff;border:1px solid #e2e8f0;border-radius:6px;font-size:12px}
th.sortable{cursor:pointer;user-select:none} th.sortable:hover{background:#f1f5f9}
.copy-btn{padding:2px 6px;font-size:11px;border:1px solid #e2e8f0;border-radius:4px;background:#f8fafc;cursor:pointer}
.save-dropdown{position:relative;display:inline-block}
.save-dropdown-menu{position:absolute;right:0;top:100%;margin-top:4px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:50;min-width:160px;display:none}
.save-dropdown-menu.show{display:block}
.save-dropdown-menu button{display:block;width:100%;text-align:left;padding:8px 12px;font-size:12px;border:none;background:none;cursor:pointer}
.save-dropdown-menu button:hover{background:#f1f5f9}
.img-thumb{width:36px;height:36px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0;cursor:zoom-in;background:#f8fafc;display:block}
.img-cell{display:flex;align-items:center;gap:6px;min-width:240px}
.browse-btn{padding:2px 6px;font-size:11px;border:1px solid #cbd5e1;border-radius:4px;background:#fff;cursor:pointer;color:#475569}
.folder-chip{padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;background:#fffbeb;font-size:11px;cursor:pointer;display:flex;align-items:center;gap:4px}
.manage-row{display:flex;align-items:center;gap:8px;padding:6px 0}
.manage-input{flex:1;padding:8px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px}

/* v1.1.2 TRUE MAXIMIZE: the IMAGE grows, not just the box. Bottom strip replaces side panel when maximized. */
#previewBox[data-max="1"]{width:100vw!important;height:100vh!important;max-width:none!important;max-height:none!important;border-radius:0!important;padding:0!important;background:#000!important}
#previewBox[data-max="1"] #imgPreview{width:100vw!important;height:calc(100vh - 52px)!important;max-width:none!important;max-height:none!important;object-fit:contain;background:#000;border-radius:0}
#previewBox[data-max="1"] #imgPreviewPath{color:#cbd5e1;background:#000;margin:0;border-radius:0}
#previewBox[data-max="1"] .preview-footnote{display:none}
#slideshowBox[data-max="1"]{width:100vw!important;height:100vh!important;max-width:none!important;max-height:none!important;border-radius:0!important}
#slideshowBox[data-max="1"] #slideSidePanel{display:none!important}
#slideshowBox[data-max="1"] #slideBottomBar{display:flex!important}
#slideshowBox[data-max="1"] #slideImage{width:100%!important;height:calc(100vh - 108px)!important;max-width:none!important;max-height:none!important;object-fit:contain}
#slideshowBox[data-max="1"] #slideStage{padding:0!important}
</style></head>
<body class="bg-slate-50 h-screen flex flex-col text-[13px]">
<div id="saveToast" class="fixed top-3 left-1/2 -translate-x-1/2 z-[100] hidden items-center gap-2 bg-emerald-600 text-white text-[13px] font-medium px-4 py-2.5 rounded-full shadow-2xl"><i class="fa-solid fa-circle-check"></i><span id="saveToastText"></span></div>
<header class="bg-white border-b px-3 py-2 flex items-center gap-2">
<button id="toggleSidebar" class="p-2 border rounded"><i class="fa-solid fa-bars"></i></button>
<i class="fa-solid fa-table-columns text-indigo-600"></i><b>CSV Studio</b><span class="text-slate-400">Sturdy Ver 1.1.2</span>
<div class="ml-auto flex items-center gap-1">
<div class="flex items-center border rounded overflow-hidden mr-2">
<button id="fontDown" class="px-2 py-1.5 bg-white hover:bg-slate-50 text-xs">A-</button>
<button id="fontReset" class="px-2 py-1.5 bg-white border-l border-r hover:bg-slate-50 text-xs"><i class="fa-solid fa-font"></i></button>
<button id="fontUp" class="px-2 py-1.5 bg-white hover:bg-slate-50 text-xs">A+</button>
</div>
<button id="saveCsvBtn" class="px-3 py-1.5 bg-indigo-600 text-white rounded">Save</button>
<div class="save-dropdown">
<button id="saveAsBtn" class="px-3 py-1.5 bg-white border rounded flex items-center gap-1">Save As <i class="fa-solid fa-chevron-down text-[10px]"></i></button>
<div id="saveAsMenu" class="save-dropdown-menu">
<button data-fmt="csv">Save As .csv (Copy)</button>
<button data-fmt="tsv">Save As .tsv (Copy)</button>
</div>
</div>
</div></header>
<div class="flex flex-1 overflow-hidden">
<aside id="sidebar" class="w-[360px] min-w-[200px] max-w-[600px] bg-white border-r flex flex-col relative">
<div id="resizer" class="resizer"></div>
<div class="p-2 border-b space-y-2">
<div class="flex gap-1"><input id="rootInput" class="flex-1 px-2 py-1.5 border rounded bg-slate-50 text-xs"><button id="goRoot" class="px-2 border rounded text-xs">Go</button><button id="upBtn" class="px-2 border rounded"><i class="fa-solid fa-arrow-up"></i></button></div>
<div class="flex gap-1"><input id="fileSearch" placeholder="Filter filenames..." class="flex-1 px-2 py-1 border rounded text-xs"><button id="refreshBtn" class="px-2 border rounded"><i class="fa-solid fa-rotate text-xs"></i></button></div>
<div class="bg-indigo-50 p-2 rounded border border-indigo-100">
<label class="text-[11px] font-bold text-indigo-700 uppercase">Smart Search Inside Files</label>
<div class="flex gap-1 mt-1"><input id="globalSearchInput" placeholder="e.g. MrJohn or John Sr" class="flex-1 px-2 py-1.5 border rounded text-xs"><button id="globalSearchBtn" class="px-3 bg-indigo-600 text-white rounded text-xs">Search</button></div>
</div></div>
<div class="px-2 py-1 text-[11px] text-slate-500 flex justify-between border-b bg-slate-50"><span id="rootPath" class="truncate"></span><span id="fileCount"></span></div>
<div id="tree" class="flex-1 overflow-auto p-1"></div>
<div id="globalResults" class="border-t max-h-[45%] overflow-auto hidden bg-white">
<div class="p-2 text-xs font-bold flex justify-between sticky top-0 bg-white border-b">Results <button id="closeResults" class="text-slate-400"><i class="fa-solid fa-xmark"></i></button></div>
<div id="resultsList" class="p-1 space-y-1"></div>
</div></aside>
<main class="flex-1 flex flex-col overflow-hidden">
<div id="breadcrumb" class="bg-slate-100 px-3 py-1.5 border-b text-xs truncate flex justify-between"><span id="bcText">No file open</span><span id="dirtyFlag" class="hidden text-amber-600 font-bold">● Unsaved changes</span></div>
<div id="toolbar" class="bg-white border-b px-2 py-1.5 flex gap-1.5 items-center flex-wrap">
<button class="tool" data-act="addRow">+ Row</button>
<button class="tool" id="manageColsBtn"><i class="fa-solid fa-table-columns mr-1"></i>Manage columns</button>
<button class="tool" data-act="delRow">Delete Selected</button>
<button class="tool" data-act="dupRow">Duplicate</button>
<div class="flex items-center gap-1 ml-2 border rounded bg-slate-50 px-2 py-1">
<select id="filterColSelect" class="text-xs bg-transparent border-none outline-none max-w-[140px]"><option value="-1">All columns</option></select>
<input id="rowFilter" placeholder="Filter current rows..." class="px-2 py-1 bg-transparent w-44 text-xs outline-none">
<button id="clearFilter" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
</div>
<button id="slideshowBtn" class="ml-auto hidden px-3 py-1.5 bg-fuchsia-600 text-white rounded text-xs font-bold"><i class="fa-solid fa-images mr-1"></i>Slideshow</button>
<span id="stats" class="text-[11px] text-slate-500"></span>
</div>
<div class="flex-1 overflow-auto bg-white" id="tableContainer"><table id="csvTable" class="w-full border-collapse"></table></div>
</main></div>

<!-- Add/Edit Row Modal -->
<div id="addRowModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
<div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-auto">
<div class="p-5 border-b flex justify-between items-center"><h3 id="modalTitle" class="font-bold text-base">Add row</h3><button id="closeAddRow" class="w-8 h-8 rounded-full hover:bg-slate-100"><i class="fa-solid fa-xmark"></i></button></div>
<div id="addRowForm" class="p-5 grid grid-cols-2 gap-4"></div>
<div class="p-4 border-t flex justify-end gap-2 bg-slate-50 rounded-b-xl"><button id="cancelAddRow" class="px-4 py-2 border rounded-lg bg-white">Cancel</button><button id="applyAddRow" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-medium">Apply</button></div>
</div>
</div>

<!-- Manage Columns Modal -->
<div id="manageColsModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[52] p-4">
<div class="bg-white rounded-xl shadow-2xl w-full max-w-xl max-h-[85vh] overflow-auto flex flex-col">
<div class="p-5 border-b"><h3 class="font-bold text-base">Manage columns</h3><p class="text-xs text-slate-500 mt-1">Rename, reorder or delete columns.</p></div>
<div id="manageColsList" class="p-4 flex-1 overflow-auto space-y-2"></div>
<div class="p-4 border-t flex justify-between bg-slate-50 rounded-b-xl">
<button id="addColInManage" class="px-3 py-2 border rounded-lg bg-white text-xs"><i class="fa-solid fa-plus mr-1"></i>Add column</button>
<div class="flex gap-2"><button id="cancelManageCols" class="px-4 py-2 border rounded-lg bg-white">Cancel</button><button id="applyManageCols" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-medium">Apply</button></div>
</div>
</div>
</div>

<!-- Image Preview Modal -->
<div id="imgPreviewModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[60] p-4">
<div id="previewBox" class="bg-white rounded-lg p-3 max-w-4xl max-h-[90vh] overflow-auto relative" onclick="event.stopPropagation()">
<button id="maxPreviewBtn" title="Maximize (fullscreen)" class="absolute top-2 right-12 w-8 h-8 rounded-full bg-black/60 text-white hover:bg-black/80"><i class="fa-solid fa-expand"></i></button>
<button id="closePreviewBtn" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/60 text-white hover:bg-black/80"><i class="fa-solid fa-xmark"></i></button>
<img id="imgPreview" src="" class="max-w-full max-h-[80vh] mx-auto block rounded">
<div id="imgPreviewPath" class="text-xs text-slate-600 mt-3 p-2 bg-slate-50 rounded truncate select-all"></div>
<div class="preview-footnote mt-2 text-[11px] text-slate-400 text-center">Press Esc to close &nbsp;•&nbsp; Maximize for fullscreen</div>
</div>
</div>

<!-- Slideshow Modal -->
<div id="slideshowModal" class="fixed inset-0 bg-black/85 hidden items-center justify-center z-[62] p-2">
<div id="slideshowBox" class="bg-white rounded-xl w-full max-w-6xl max-h-[92vh] flex flex-col overflow-hidden relative">
<div class="p-3 border-b flex justify-between items-center bg-slate-900 text-white">
<div><span class="font-bold" id="slideTitle">Slideshow</span> <span id="slideCounter" class="text-slate-300 text-xs ml-3"></span></div>
<div class="flex gap-2 items-center"><span class="text-[11px] text-slate-400">Esc to close, ← → to navigate</span><button id="maxSlideshowBtn" title="Maximize (fullscreen)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20"><i class="fa-solid fa-expand"></i></button><button id="closeSlideshow" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20"><i class="fa-solid fa-xmark"></i></button></div>
</div>
<div class="flex flex-1 overflow-hidden min-h-0">
<div id="slideStage" class="flex-1 bg-black flex items-center justify-center relative p-2 min-w-0">
<button id="slidePrev" class="absolute left-2 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/40 text-white text-xl z-10"><i class="fa-solid fa-chevron-left"></i></button>
<img id="slideImage" src="" class="max-w-full max-h-[68vh] object-contain">
<button id="slideNext" class="absolute right-2 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white/40 text-white text-xl z-10"><i class="fa-solid fa-chevron-right"></i></button>
</div>
<div id="slideSidePanel" class="w-[340px] border-l bg-slate-50 overflow-auto p-3">
<div id="slideInfo" class="space-y-2 text-xs"></div>
<div id="slidePath" class="mt-3 p-2 bg-white border rounded text-[11px] break-all"></div>
</div>
</div>
<div id="slideBottomBar" class="hidden items-center gap-4 px-4 py-2 bg-slate-900 text-white text-[13px] overflow-x-auto whitespace-nowrap"><span id="slideInfoBottom" class="flex gap-5 items-center"></span><span id="slidePathBottom" class="ml-auto text-[11px] text-slate-400 truncate"></span></div>
</div>
</div>

<input type="file" id="nativeFileInput" accept=".jpg,.jpeg,.png,.gif,.webp,.bmp" style="display:none">

<script>
window.addEventListener('error', function(ev){ var el=document.getElementById('tree'); if(el) el.innerHTML='<div class="p-3 text-xs text-red-600"><b>Script error — sidebar stopped:</b><br>'+String(ev.message||ev.error||'unknown')+'<br><br>Press Ctrl+R. If it repeats, tell me this exact message.</div>'; });
</script>
<script>
let currentRoot='',currentFile=null,headers=[],rows=[],selected=new Set(),isEditing=false,sortState={col:-1,dir:1},fontSize=13,isDirty=false,editingRowIndex=null;
let lastFocusedCell=null, currentBrowseTarget=null, imgBrowserRoot='D:/web/csv/studio', imgBrowserRecursive=true, allImagesCache=[];
let filterColIndex=-1;
let slideshowIndex=0, slideshowImages=[];
let pendingNav=null; // for Tab/Enter navigation

function clearTree(){document.getElementById('tree').innerHTML='';}
function setDirty(d){isDirty=d; document.getElementById('dirtyFlag').classList.toggle('hidden',!d);}
function esc(s){return (s||'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function truncatePath(p,len=20){ if(!p) return ''; if(p.length<=len) return p; return p.substring(0,len)+'...'; }
function isImageColumn(h){ if(!h) return false; const s=h.toLowerCase().trim(); return ['picture','chart','image','photo','img','thumbnail','pic','album'].some(k=> s===k || s.includes(k)); }

let toastTimer=null;
function showToast(msg, ms=3500){ const el=document.getElementById('saveToast'); document.getElementById('saveToastText').textContent=msg; el.classList.remove('hidden'); el.classList.add('flex'); el.style.opacity='1'; clearTimeout(toastTimer); toastTimer=setTimeout(()=>{ el.style.opacity='0'; setTimeout(()=>{ el.classList.add('hidden'); el.classList.remove('flex'); el.style.opacity='1'; },300); }, ms); }
// FIX v1.1: only close on a REAL click on the backdrop (mousedown AND mouseup both on backdrop).
// Drag-selecting text inside a modal and releasing outside used to close it. Now it never does.
function backdropClickClose(modalId, closeFn){ const m=document.getElementById(modalId); if(!m) return; let downOnBackdrop=false; m.addEventListener('mousedown', e=>{ downOnBackdrop=(e.target.id===modalId); }); m.addEventListener('mouseup', e=>{ if(downOnBackdrop && e.target.id===modalId) closeFn(); downOnBackdrop=false; }); }
function toggleMax(boxId, btn){ const box=document.getElementById(boxId); const isMax=box.dataset.max==='1'; box.dataset.max=isMax?'':'1'; btn.innerHTML=isMax?'<i class="fa-solid fa-expand"></i>':'<i class="fa-solid fa-compress"></i>'; }
function isImagePath(v){ if(!v) return false; const s=v.trim().toLowerCase(); return /\.(jpg|jpeg|png|gif|webp|bmp)$/i.test(s.split('?')[0]) || s.includes(':/'); }

async function loadTree(root){
 if(!document.getElementById('tree')) return;
 if(root) currentRoot=root; clearTree();
 try{
  const r=await fetch('api.php?action=list&root='+encodeURIComponent(currentRoot||''));
  const d=await r.json();
  if(d.error){alert(d.error);return;}
  currentRoot=d.root;
  document.getElementById('rootPath').textContent=d.root;
  document.getElementById('rootInput').value=d.root;
  document.getElementById('fileCount').textContent=d.total+' files';
  renderTree(d.tree, document.getElementById('tree'), 0);
  if(currentFile) expandToFile(currentFile);
 }catch(e){ document.getElementById('tree').innerHTML='<div class="p-3 text-xs text-red-500">Failed: '+e.message+'</div>'; }
}
function renderTree(nodes,cont,depth){
 nodes.forEach(n=>{
  const div=document.createElement('div');
  div.className='flex items-center gap-1 px-2 py-1 rounded hover:bg-slate-100 cursor-pointer text-[12px]';
  div.style.paddingLeft=(6+depth*14)+'px';
  div.dataset.full=n.full||n.path;
  if(n.type==='dir'){
   div.innerHTML='<i class="fa-solid fa-folder text-amber-500"></i> '+esc(n.name)+' <span class="ml-auto text-[10px] text-slate-400">'+n.count+'</span>';
   cont.appendChild(div);
   const child=document.createElement('div'); child.className='hidden'; child.dataset.dirPath=n.full;
   renderTree(n.children,child,depth+1); cont.appendChild(child);
   div.onclick=()=>child.classList.toggle('hidden');
  }else{
   const active=currentFile===n.path;
   div.className+=active?' bg-indigo-50 text-indigo-700 font-medium':'';
   div.innerHTML='<i class="fa-solid fa-file-csv text-emerald-600"></i> <span class="truncate">'+esc(n.name)+'</span> <span class="ml-auto text-[10px] text-slate-400">'+n.size+'</span>';
   div.title=n.path; div.onclick=()=>handleFileClick(n.path); cont.appendChild(div);
  }
 });
}
function expandToFile(filePath){
 if(!filePath) return;
 const treeEl=document.getElementById('tree');
 const fp = filePath.replace(/\\/g,'/').toLowerCase();
 const allDirDivs = treeEl.querySelectorAll('[data-dir-path]');
 allDirDivs.forEach(childDiv=>{
   let dirPath = (childDiv.dataset.dirPath||'').replace(/\\/g,'/').toLowerCase();
   if(!dirPath) return;
   if(!dirPath.endsWith('/')) dirPath+='/';
   if(fp.startsWith(dirPath)) childDiv.classList.remove('hidden');
 });
}
function handleFileClick(path){
 if(!isDirty || path===currentFile){ openFile(path); return; }
 if(confirm('You have unsaved changes\nOK to discard?')){ setDirty(false); openFile(path); }
}
async function openFile(path){
 currentFile=path;
 const r=await fetch('api.php?action=read&file='+encodeURIComponent(path));
 const d=await r.json(); if(d.error){alert(d.error);return;}
 headers=d.headers; rows=d.rows; selected.clear(); sortState={col:-1,dir:1}; setDirty(false);
 document.getElementById('bcText').textContent=path+' — '+rows.length+' rows';
 populateFilterColSelect();
 checkSlideshowVisibility();
 renderTable(); loadTree(currentRoot);
}
function populateFilterColSelect(){
 const sel=document.getElementById('filterColSelect');
 sel.innerHTML='<option value="-1">All columns</option>';
 headers.forEach((h,i)=>{ const opt=document.createElement('option'); opt.value=i; opt.textContent=h; sel.appendChild(opt); });
 sel.value=filterColIndex;
}
function checkSlideshowVisibility(){
 const btn=document.getElementById('slideshowBtn');
 const hasImageCol = headers.some(h=> isImageColumn(h));
 btn.classList.toggle('hidden', !hasImageCol);
}
function parseSortValue(v){
 if(!v) return ''; const m=v.match(/^(\d{1,2})[-\\/\.](\d{1,2})[-\\/\.](\d{2,4})$/);
 if(m){ let d=parseInt(m[1]),mo=parseInt(m[2]),y=parseInt(m[3]); if(y<100) y+=2000; return new Date(y,mo-1,d).getTime(); }
 const num=parseFloat(v.replace(/[^0-9.\-]/g,'')); if(!isNaN(num)&&isFinite(num)&&v.match(/^-?\d/)) return num; return v.toString().toLowerCase();
}
function sortByColumn(col){
 if(sortState.col===col){ sortState.dir*=-1; } else { sortState.col=col; sortState.dir=1; }
 rows.sort((a,b)=>{ const av=parseSortValue(a[col]||''), bv=parseSortValue(b[col]||''); if(av<bv) return -1*sortState.dir; if(av>bv) return 1*sortState.dir; return 0; });
 renderTable(); setDirty(true);
}
function getFilteredIndices(){
 const filterLower=document.getElementById('rowFilter').value.toLowerCase();
 if(!filterLower) return rows.map((_,i)=>i);
 if(filterColIndex>=0){
   return rows.map((r,i)=> ({r,i})).filter(o=> (o.r[filterColIndex]||'').toString().toLowerCase().includes(filterLower)).map(o=>o.i);
 } else {
   return rows.map((r,i)=> ({r,i})).filter(o=> o.r.join(' ').toLowerCase().includes(filterLower)).map(o=>o.i);
 }
}
function renderTable(){
 if(isEditing && !pendingNav) { /* allow render if pending nav */ }
 const filterLower=document.getElementById('rowFilter').value.toLowerCase();
 const visibleSet = new Set(getFilteredIndices());
 const table=document.getElementById('csvTable'); table.style.fontSize=fontSize+'px';
 const allVisibleSelected = visibleSet.size>0 && Array.from(visibleSet).every(i=>selected.has(i));
 const someVisibleSelected = Array.from(visibleSet).some(i=>selected.has(i));
 let html='<thead class="sticky top-0 bg-slate-50 z-10"><tr><th class="w-8 border p-1"><input type="checkbox" id="selAll" '+(allVisibleSelected?'checked':'')+'></th>';
 headers.forEach((h,i)=>{
  const active=sortState.col===i; const icon=active?(sortState.dir===1?'<i class="fa-solid fa-sort-up text-indigo-600"></i>':'<i class="fa-solid fa-sort-down text-indigo-600"></i>'):'<i class="fa-solid fa-sort text-slate-300"></i>';
  html+='<th class="sortable border px-2 py-1.5 text-left font-semibold bg-slate-50 whitespace-nowrap" onclick="sortByColumn('+i+')">'+esc(h)+icon+'</th>';
  if(i===0) html+='<th class="w-[70px] border px-2 py-1.5 text-center font-semibold bg-indigo-50/50 text-[11px]">Copy</th>';
 });
 html+='<th class="w-[70px] border px-2 py-1.5 text-center font-semibold bg-slate-50 text-[11px] text-indigo-600">Edit</th><th class="w-8 border bg-slate-50"></th></tr></thead><tbody>';
 rows.forEach((r,ri)=>{
  if(!visibleSet.has(ri)) return;
  html+='<tr class="hover:bg-slate-50 '+(selected.has(ri)?'bg-indigo-50':'')+'"><td class="border p-1 text-center"><input type="checkbox" '+(selected.has(ri)?'checked':'')+' onchange="toggleSel('+ri+')"></td>';
  r.forEach((c,ci)=>{
   const hdr=headers[ci]||'';
   if(isImageColumn(hdr)){
     const encoded = c?encodeURIComponent(c.trim()):'';
     const shortPath = c?truncatePath(c, 18):'<empty>';
     const thumb = c?'<img src="api.php?action=image&file='+encoded+'" class="img-thumb" loading="lazy" data-full-path="'+esc(c)+'" onclick="previewImageFromThumb(this)" onerror="this.style.display=\'none\'">' : '<div class="img-thumb bg-slate-100 flex items-center justify-center text-slate-300" style="width:36px;height:36px"><i class="fa-solid fa-image"></i></div>';
     html+='<td class="border px-2 py-1" data-ri="'+ri+'" data-ci="'+ci+'" ondblclick="startEdit(this,'+ri+','+ci+')"><div class="img-cell">'+thumb+'<span class="truncate text-[11px] text-slate-600" title="'+esc(c)+'">'+esc(shortPath)+'</span><button class="browse-btn ml-auto" onclick="openWindowsPicker('+ri+','+ci+')"><i class="fa-solid fa-folder-open"></i> Browse</button></div></td>';
   } else {
     html+='<td class="cell border px-2 py-1 min-w-[120px]" data-ri="'+ri+'" data-ci="'+ci+'" ondblclick="startEdit(this,'+ri+','+ci+')">'+esc(c)+'</td>';
   }
   if(ci===0) html+='<td class="border px-1 py-1 text-center bg-slate-50/30"><button class="copy-btn" onclick="copyDate(\''+esc(c).replace(/'/g,"\\'")+'\', this)"><i class="fa-solid fa-copy"></i> Copy</button></td>';
  });
  html+='<td class="border px-1 py-1 text-center"><button class="px-2 py-1 text-[11px] text-indigo-600 hover:bg-indigo-50 rounded" onclick="openEditRow('+ri+')"><i class="fa-solid fa-pen-to-square mr-1"></i>Edit</button></td>';
  html+='<td class="border text-center"><button onclick="delSingle('+ri+')" class="text-red-300 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button></td></tr>';
 });
 html+='</tbody>'; table.innerHTML=html;
 document.getElementById('stats').textContent=rows.length+' rows x '+headers.length+' cols - '+visibleSet.size+' visible';
 const sa=document.getElementById('selAll');
 if(sa){ sa.indeterminate = someVisibleSelected && !allVisibleSelected; sa.onclick=()=>{ if(allVisibleSelected){ Array.from(visibleSet).forEach(i=>selected.delete(i)); } else { Array.from(visibleSet).forEach(i=>selected.add(i)); } renderTable(); }; }
 // handle pending navigation after render
 if(pendingNav){ const nav=pendingNav; pendingNav=null; setTimeout(()=>{ startEditByIndex(nav.ri, nav.ci); }, 30); }
}
function previewImageFromThumb(imgEl){ const path = imgEl.dataset.fullPath || imgEl.getAttribute('data-full-path'); if(path) previewImage(path); }
function previewImage(path){
 const modal=document.getElementById('imgPreviewModal');
 const img=document.getElementById('imgPreview');
 const p=document.getElementById('imgPreviewPath');
 img.src='api.php?action=image&file='+encodeURIComponent(path);
 p.textContent=path;
 modal.classList.remove('hidden'); modal.classList.add('flex');
 modal.dataset.open='1';
}
function copyDate(text, btn){ navigator.clipboard.writeText(text).then(()=>{ const o=btn.innerHTML; btn.innerHTML='<i class="fa-solid fa-check"></i> Copied'; setTimeout(()=>{ btn.innerHTML=o; },1000); }); }
function toggleSel(i){ selected.has(i)?selected.delete(i):selected.add(i); renderTable(); }
function delSingle(i){ if(confirm('Delete row?')){ rows.splice(i,1); setDirty(true); renderTable(); } }

function startEditByIndex(ri,ci){
 const td=document.querySelector('td[data-ri="'+ri+'"][data-ci="'+ci+'"]');
 if(td) startEdit(td,ri,ci);
}

function startEdit(td,ri,ci){
 if(isEditing) return;
 if(td.tagName!=='TD') td=td.closest('td');
 if(!td) return;
 // don't edit image cells with browse button? allow text edit
 isEditing=true; lastFocusedCell={ri,ci, val: rows[ri][ci]};
 const originalInputVal = rows[ri][ci]||'';
 td.classList.add('cell-editing');
 td.innerHTML='<input class="w-full bg-transparent outline-none" data-orig="'+esc(originalInputVal).replace(/"/g,'&quot;')+'">';
 const inp=td.querySelector('input'); inp.value=originalInputVal; inp.focus(); inp.select();
 let done=false;
 const finish=(save, moveTo)=>{
  if(done) return; done=true;
  const newVal=save?inp.value:originalInputVal;
  if(save && newVal!==rows[ri][ci]){ rows[ri][ci]=newVal; setDirty(true); }
  isEditing=false; td.classList.remove('cell-editing');
  if(moveTo){
    pendingNav=moveTo;
    renderTable();
  } else {
    renderTable();
  }
 };
 inp.addEventListener('blur', (e)=>{
   if(document.hidden || !document.hasFocus()){ return; }
   if(pendingNav) return;
   setTimeout(()=>{
     if(isEditing && document.hasFocus() && !document.hidden && !pendingNav){
       // check if still same input
       if(document.activeElement!==inp){
         finish(true, null);
       }
     }
   }, 150);
 });
 inp.addEventListener('keydown',e=>{
  if(e.key==='Enter'){
    e.preventDefault();
    const visible = getFilteredIndices();
    let nextRi = ri;
    let idx = visible.indexOf(ri);
    if(idx>=0 && idx < visible.length-1) nextRi = visible[idx+1];
    else if(idx===visible.length-1) nextRi = visible[0]!==undefined? visible[0] : ri;
    // if Enter, go to next row same column
    if(e.shiftKey){
      // Shift+Enter goes up
      let prevIdx = idx-1; if(prevIdx<0) prevIdx=visible.length-1;
      nextRi = visible[prevIdx] ?? ri;
    }
    finish(true, {ri: nextRi, ci: ci});
  } else if(e.key==='Tab'){
    e.preventDefault();
    const visibleCols = headers.length;
    let nextRi=ri, nextCi=ci + (e.shiftKey ? -1 : 1);
    if(nextCi >= visibleCols){ nextCi=0; // next row
      const vis=getFilteredIndices(); let idx=vis.indexOf(ri); if(idx>=0 && idx<vis.length-1) nextRi=vis[idx+1]; else nextRi=vis[0]??ri;
    }
    if(nextCi < 0){ nextCi=visibleCols-1;
      const vis=getFilteredIndices(); let idx=vis.indexOf(ri); if(idx>0) nextRi=vis[idx-1]; else nextRi=vis[vis.length-1]??ri;
    }
    finish(true, {ri: nextRi, ci: nextCi});
  } else if(e.key==='Escape'){ e.preventDefault(); finish(false, null); }
 });
}

// Manage columns
function openManageColumns(){
 const list=document.getElementById('manageColsList'); list.innerHTML='';
 headers.forEach((h,i)=>{
   const row=document.createElement('div'); row.className='manage-row'; row.dataset.idx=i;
   row.innerHTML='<input class="manage-input" value="'+esc(h).replace(/"/g,'&quot;')+'" data-idx="'+i+'">'
     +'<button class="w-8 h-8 border rounded hover:bg-slate-50 upBtn"><i class="fa-solid fa-arrow-up text-xs"></i></button>'
     +'<button class="w-8 h-8 border rounded hover:bg-slate-50 downBtn"><i class="fa-solid fa-arrow-down text-xs"></i></button>'
     +'<button class="px-2 py-1 border border-red-200 text-red-600 rounded text-xs hover:bg-red-50 delColBtn">Delete</button>';
   list.appendChild(row);
 });
 refreshManageColButtons();
 document.getElementById('manageColsModal').classList.remove('hidden'); document.getElementById('manageColsModal').classList.add('flex');
}
function moveCol(idx, dir){
 const list=document.getElementById('manageColsList');
 const rowsEl=Array.from(list.children);
 const targetIdx=idx+dir;
 if(targetIdx<0 || targetIdx>=rowsEl.length) return;
 if(dir===-1){ list.insertBefore(rowsEl[idx], rowsEl[targetIdx]); }
 else { list.insertBefore(rowsEl[targetIdx], rowsEl[idx]); }
 refreshManageColButtons();
}
function refreshManageColButtons(){
 const list=document.getElementById('manageColsList');
 const rowsEl=Array.from(list.children);
 rowsEl.forEach((r,i)=>{
   r.querySelector('.upBtn').onclick=()=> moveCol(i,-1);
   r.querySelector('.downBtn').onclick=()=> moveCol(i,1);
   r.querySelector('.delColBtn').onclick=()=> deleteCol(i);
 });
}
function deleteCol(idx){
 if(!confirm('Delete column "'+headers[idx]+'"?')) return;
 const list=document.getElementById('manageColsList'); list.children[idx].remove(); refreshManageColButtons();
}
function closeManageCols(){ document.getElementById('manageColsModal').classList.add('hidden'); document.getElementById('manageColsModal').classList.remove('flex'); }
function applyManageCols(){
 const list=document.getElementById('manageColsList');
 const newHeaders=[]; const oldIndices=[];
 Array.from(list.children).forEach(row=>{
   const input=row.querySelector('input.manage-input');
   const newName=input.value.trim()||'Untitled';
   const origIdx=parseInt(input.dataset.idx);
   newHeaders.push(newName);
   if(!isNaN(origIdx)) oldIndices.push(origIdx); else oldIndices.push(-1);
 });
 const newRows = rows.map(oldRow=>{
   const newRow=[]; oldIndices.forEach(oi=>{ if(oi===-1) newRow.push(''); else newRow.push(oldRow[oi]||''); });
   return newRow;
 });
 headers=newHeaders; rows=newRows; setDirty(true); populateFilterColSelect(); checkSlideshowVisibility(); renderTable(); closeManageCols();
}
document.getElementById('manageColsBtn').onclick=openManageColumns;
document.getElementById('cancelManageCols').onclick=closeManageCols;
document.getElementById('applyManageCols').onclick=applyManageCols;
backdropClickClose('manageColsModal', closeManageCols);
document.getElementById('addColInManage').onclick=()=>{
 const list=document.getElementById('manageColsList');
 const row=document.createElement('div'); row.className='manage-row';
 row.innerHTML='<input class="manage-input" value="NewCol'+(list.children.length+1)+'" data-idx="-1">'
   +'<button class="w-8 h-8 border rounded hover:bg-slate-50 upBtn"><i class="fa-solid fa-arrow-up text-xs"></i></button>'
   +'<button class="w-8 h-8 border rounded hover:bg-slate-50 downBtn"><i class="fa-solid fa-arrow-down text-xs"></i></button>'
   +'<button class="px-2 py-1 border border-red-200 text-red-600 rounded text-xs hover:bg-red-50 delColBtn">Delete</button>';
 list.appendChild(row); refreshManageColButtons();
};

// Slideshow
function openSlideshow(){
 const imgColIdx = headers.findIndex(h=> isImageColumn(h));
 if(imgColIdx===-1){ alert('No image column'); return; }
 const visibleIndices = getFilteredIndices();
 slideshowImages = visibleIndices.map(ri=>{
   const path = rows[ri][imgColIdx]; if(!path || !isImagePath(path)) return null;
   return {path, rowIndex: ri, row: rows[ri]};
 }).filter(Boolean);
 if(!slideshowImages.length){ alert('No images in filtered rows'); return; }
 slideshowIndex=0;
 document.getElementById('slideshowModal').classList.remove('hidden'); document.getElementById('slideshowModal').classList.add('flex');
 document.getElementById('slideshowModal').dataset.open='1';
 showSlide(slideshowIndex);
}
function showSlide(idx){
 if(idx<0) idx=slideshowImages.length-1;
 if(idx>=slideshowImages.length) idx=0;
 slideshowIndex=idx;
 const item=slideshowImages[idx];
 document.getElementById('slideImage').src='api.php?action=image&file='+encodeURIComponent(item.path);
 document.getElementById('slideCounter').textContent=(idx+1)+' / '+slideshowImages.length;
 document.getElementById('slideTitle').textContent='Row '+(item.rowIndex+1)+' - '+ (item.row[0]||'');
 document.getElementById('slidePath').textContent=item.path;
 let infoHtml=''; headers.forEach((h,i)=>{ if(isImageColumn(h)) return; infoHtml+='<div class="flex gap-2"><span class="font-semibold min-w-[80px] text-slate-600">'+esc(h)+':</span><span class="text-slate-800 break-all">'+esc(item.row[i]||'')+'</span></div>'; });
 document.getElementById('slideInfo').innerHTML=infoHtml;
}
function closeSlideshow(){ document.getElementById('slideshowModal').classList.add('hidden'); document.getElementById('slideshowModal').classList.remove('flex'); document.getElementById('slideshowModal').dataset.open=''; }
document.getElementById('slideshowBtn').onclick=openSlideshow;
document.getElementById('closeSlideshow').onclick=closeSlideshow;
document.getElementById('slidePrev').onclick=()=> showSlide(slideshowIndex-1);
document.getElementById('slideNext').onclick=()=> showSlide(slideshowIndex+1);
backdropClickClose('slideshowModal', closeSlideshow);

// Add/Edit Row
function openAddRowModal(){
 editingRowIndex=null; document.getElementById('modalTitle').textContent='Add row';
 const form=document.getElementById('addRowForm'); form.innerHTML='';
 const today=new Date(); const dd=String(today.getDate()).padStart(2,'0'); const mm=String(today.getMonth()+1).padStart(2,'0'); const yyyy=today.getFullYear(); const todayStr=dd+'-'+mm+'-'+yyyy;
 headers.forEach((h,i)=>{
  const w=document.createElement('div'); const l=document.createElement('label'); l.className='block text-[12px] font-medium text-slate-600 mb-1'; l.textContent=h+(i===0?' (Entry)':'');
  const inp=document.createElement('input'); inp.className='w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px]'; inp.dataset.col=i; inp.placeholder=h; if(i===0) inp.value=todayStr;
  if(isImageColumn(h)){ const browse=document.createElement('button'); browse.type='button'; browse.className='mt-1 browse-btn'; browse.innerHTML='<i class="fa-solid fa-folder-open"></i> Windows Pick'; browse.onclick=()=>{ currentBrowseTarget={input: inp}; document.getElementById('nativeFileInput').click(); }; w.appendChild(l); w.appendChild(inp); w.appendChild(browse); } else { w.appendChild(l); w.appendChild(inp); }
  form.appendChild(w);
 });
 document.getElementById('addRowModal').classList.remove('hidden'); document.getElementById('addRowModal').classList.add('flex');
}
function openEditRow(ri){
 editingRowIndex=ri; document.getElementById('modalTitle').textContent='Edit row #'+(ri+1);
 const form=document.getElementById('addRowForm'); form.innerHTML=''; const row=rows[ri]||[];
 headers.forEach((h,i)=>{
  const w=document.createElement('div'); const l=document.createElement('label'); l.className='block text-[12px] font-medium text-slate-600 mb-1'; l.textContent=h;
  const inp=document.createElement('input'); inp.className='w-full px-3 py-2 border border-slate-200 rounded-lg text-[13px]'; inp.dataset.col=i; inp.value=row[i]||'';
  if(isImageColumn(h)){ const browse=document.createElement('button'); browse.type='button'; browse.className='mt-1 browse-btn'; browse.innerHTML='<i class="fa-solid fa-folder-open"></i> Windows Pick'; browse.onclick=()=>{ currentBrowseTarget={input: inp}; document.getElementById('nativeFileInput').click(); }; w.appendChild(l); w.appendChild(inp); w.appendChild(browse); } else { w.appendChild(l); w.appendChild(inp); }
  form.appendChild(w);
 });
 document.getElementById('addRowModal').classList.remove('hidden'); document.getElementById('addRowModal').classList.add('flex');
}
function closeAddRowModal(){ document.getElementById('addRowModal').classList.add('hidden'); document.getElementById('addRowModal').classList.remove('flex'); editingRowIndex=null; }

// v1.1: Custom Browse Images removed - Windows Picker only.
function openWindowsPicker(ri,ci){ currentBrowseTarget={ri,ci, isTable:true}; document.getElementById('nativeFileInput').click(); }
function selectImagePath(path){
  path = path.split(String.fromCharCode(92)).join('/');
  if(currentBrowseTarget && currentBrowseTarget.isTable){
    const {ri,ci}=currentBrowseTarget; if(ri>=0 && ci>=0){ rows[ri][ci]=path; setDirty(true); renderTable(); }
  } else if(currentBrowseTarget && currentBrowseTarget.input){ currentBrowseTarget.input.value=path; }
}
// Native Windows picker upload




document.getElementById('nativeFileInput').onchange=async (e)=>{
 const file=e.target.files[0]; if(!file) return;
 showToast('Uploading '+file.name+' ...', 2000);
 const form=new FormData(); form.append('image', file);
 try{
  const r=await fetch('api.php?action=upload_image&root='+encodeURIComponent(imgBrowserRoot), {method:'POST', body: form});
  const d=await r.json();
  if(d.success){ selectImagePath(d.path); showToast('Image picked: '+d.path); }
  else showToast('Error: '+(d.error||'unknown'), 5000);
 }catch(err){ showToast('Upload failed: '+err.message, 5000); }
 e.target.value='';
};

document.querySelectorAll('[data-act]').forEach(b=>{ b.onclick=()=>{
 if(isEditing) return; const a=b.dataset.act;
 if(a==='addRow'){ if(headers.length===0){ alert('Open a file first'); return; } openAddRowModal(); }
 if(a==='delRow'){if(!selected.size) return alert('Select rows'); rows=rows.filter((_,i)=>!selected.has(i)); selected.clear(); setDirty(true); renderTable();}
 if(a==='dupRow'){const nr=[]; rows.forEach((r,i)=>{nr.push(r); if(selected.has(i)) nr.push([...r]);}); rows=nr; setDirty(true); renderTable();}
};});
document.getElementById('rowFilter').oninput=()=>renderTable();
document.getElementById('filterColSelect').onchange=(e)=>{ filterColIndex=parseInt(e.target.value); renderTable(); };
document.getElementById('clearFilter').onclick=()=>{ document.getElementById('rowFilter').value=''; filterColIndex=-1; document.getElementById('filterColSelect').value=-1; renderTable(); };
async function saveFile(fmt, isSaveAs){
 if(!currentFile) return alert('Open file');
 let target=currentFile; let isCopy=false;
 if(isSaveAs){
   const defaultName = target.replace(/\.csv$/i,'').replace(/\.tsv$/i,'');
   const ext = fmt==='tsv' ? '.tsv' : '.csv';
   let newPath = prompt('Save As '+ext+' (Copy) - Enter full path or filename:', defaultName+'_copy'+ext);
   if(!newPath) return;
   if(!newPath.includes('/') && !newPath.includes('\\') && !newPath.includes(':')){
     const dir = target.substring(0, target.lastIndexOf('/')+1);
     newPath = dir + newPath;
   }
   if(fmt==='csv' && !newPath.toLowerCase().endsWith('.csv')) newPath+='.csv';
   if(fmt==='tsv' && !newPath.toLowerCase().endsWith('.tsv')) newPath+='.tsv';
   target=newPath; isCopy=true;
 } else {
   if(fmt==='tsv' && !target.toLowerCase().endsWith('.tsv')){ target=target.replace(/\.csv$/i,'.tsv'); }
 }
 const r=await fetch('api.php?action=write',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({file:target,headers,rows,format:fmt,isCopy})});
 const d=await r.json();
 if(d.success){ if(isCopy){ showToast('Copy saved: '+d.path+' — original still open'); loadTree(currentRoot); } else { showToast('Saved '+d.path+'  •  Backup: '+(d.backup||'none')); setDirty(false); if(d.path!==currentFile){ currentFile=d.path; loadTree(currentRoot); } } } else showToast('Save failed: '+d.error, 5000);
}
document.getElementById('saveCsvBtn').onclick=()=>saveFile('csv', false);
const saveAsBtn=document.getElementById('saveAsBtn'), saveAsMenu=document.getElementById('saveAsMenu');
saveAsBtn.onclick=(e)=>{ e.stopPropagation(); saveAsMenu.classList.toggle('show'); };
saveAsMenu.querySelectorAll('button').forEach(b=>{ b.onclick=()=>{ saveAsMenu.classList.remove('show'); saveFile(b.dataset.fmt, true); }; });
window.addEventListener('click',()=>saveAsMenu.classList.remove('show'));
document.getElementById('toggleSidebar').onclick=()=>document.getElementById('sidebar').classList.toggle('hidden');
const resizer=document.getElementById('resizer'),sidebar=document.getElementById('sidebar'); let resizing=false;
resizer.addEventListener('mousedown',()=>{resizing=true; document.body.style.cursor='col-resize';});
window.addEventListener('mousemove',e=>{if(!resizing) return; sidebar.style.width=e.clientX+'px';});
window.addEventListener('mouseup',()=>{resizing=false; document.body.style.cursor='';});
document.getElementById('goRoot').onclick=()=>loadTree(document.getElementById('rootInput').value);
document.getElementById('upBtn').onclick=()=>{const p=currentRoot.split('/').filter(Boolean); p.pop(); const up=p.join('/'); loadTree(currentRoot.startsWith('D:')||currentRoot.startsWith('C:')?up:'/'+up||currentRoot);};
document.getElementById('refreshBtn').onclick=()=>loadTree();
document.getElementById('fileSearch').oninput=e=>{const q=e.target.value.toLowerCase(); document.querySelectorAll('#tree div').forEach(el=>{el.style.display=!q||el.textContent.toLowerCase().includes(q)?'':'none';});};
function hl(text,phrases){ let out=esc(text); const sorted=[...phrases].sort((a,b)=>b.length-a.length); sorted.forEach(p=>{ const escp=p.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); try{const re=new RegExp('('+escp+')','gi'); out=out.replace(re,'<mark>$1</mark>');}catch(e){} }); return out; }
document.getElementById('globalSearchBtn').onclick=doSearch;
document.getElementById('globalSearchInput').onkeydown=e=>{if(e.key==='Enter') doSearch();};
async function doSearch(){ const q=document.getElementById('globalSearchInput').value.trim(); if(!q) return; const box=document.getElementById('globalResults'),list=document.getElementById('resultsList'); box.classList.remove('hidden'); list.innerHTML='<div class="p-2 text-xs">Searching...</div>'; const r=await fetch('api.php?action=global_search&root='+encodeURIComponent(currentRoot)+'&q='+encodeURIComponent(q)); const d=await r.json(); if(!d.results.length){list.innerHTML='<div class="p-2 text-slate-500">No matches</div>'; return;} let html=''; d.results.forEach(res=>{ html+='<div class="p-2 border rounded hover:bg-slate-50 cursor-pointer text-xs" onclick="openFile(\''+res.file.replace(/'/g,"\\'")+'\')"><div class="font-mono text-[11px]">'+hl(res.content,d.phrases)+'</div><div class="text-[10px] text-indigo-600 truncate mt-1">'+esc(res.rel||res.file)+' — row '+res.row+'</div></div>'; }); list.innerHTML=html; }
document.getElementById('closeResults').onclick=()=>document.getElementById('globalResults').classList.add('hidden');
document.getElementById('fontUp').onclick=()=>{ fontSize=Math.min(22,fontSize+1); document.getElementById('csvTable').style.fontSize=fontSize+'px'; };
document.getElementById('fontDown').onclick=()=>{ fontSize=Math.max(10,fontSize-1); document.getElementById('csvTable').style.fontSize=fontSize+'px'; };
document.getElementById('fontReset').onclick=()=>{ fontSize=13; document.getElementById('csvTable').style.fontSize=fontSize+'px'; };
document.getElementById('closeAddRow').onclick=closeAddRowModal;
document.getElementById('cancelAddRow').onclick=closeAddRowModal;
backdropClickClose('addRowModal', closeAddRowModal);
document.getElementById('applyAddRow').onclick=()=>{
 const inputs=document.querySelectorAll('#addRowForm input');
 const newRow=new Array(headers.length).fill('');
 inputs.forEach(inp=>{ const col=parseInt(inp.dataset.col); newRow[col]=inp.value; });
 if(newRow.some(v=>v.trim()!=='')){
  if(editingRowIndex!==null){ rows[editingRowIndex]=newRow; } else { rows.push(newRow); }
  setDirty(true); renderTable();
 }
 closeAddRowModal();
};

backdropClickClose('imgPreviewModal', ()=>{ document.getElementById('imgPreviewModal').classList.add('hidden'); document.getElementById('imgPreviewModal').dataset.open=''; });
document.getElementById('maxPreviewBtn').onclick=(e)=>{ e.stopPropagation(); toggleMax('previewBox', document.getElementById('maxPreviewBtn')); };
document.getElementById('maxSlideshowBtn').onclick=(e)=>{ e.stopPropagation(); toggleMax('slideshowBox', document.getElementById('maxSlideshowBtn')); };
document.getElementById('closePreviewBtn').onclick=()=>{ document.getElementById('imgPreviewModal').classList.add('hidden'); document.getElementById('imgPreviewModal').dataset.open=''; };

// Global Esc + arrows
document.addEventListener('keydown', (e)=>{
  if((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='s'){ e.preventDefault(); if(currentFile) saveFile('csv', false); else showToast('Open a file first', 2500); return; }
 if(e.key==='Escape'){
   if(document.getElementById('slideshowModal').dataset.open){ closeSlideshow(); e.preventDefault(); return; }
   if(document.getElementById('imgPreviewModal').dataset.open){ document.getElementById('imgPreviewModal').classList.add('hidden'); document.getElementById('imgPreviewModal').dataset.open=''; e.preventDefault(); return; }
   if(!document.getElementById('manageColsModal').classList.contains('hidden')){ closeManageCols(); e.preventDefault(); return; }
   if(!document.getElementById('addRowModal').classList.contains('hidden')){ closeAddRowModal(); e.preventDefault(); return; }
 }
 if(document.getElementById('slideshowModal').dataset.open){
   if(e.key==='ArrowLeft'){ showSlide(slideshowIndex-1); e.preventDefault(); }
   if(e.key==='ArrowRight'){ showSlide(slideshowIndex+1); e.preventDefault(); }
 }
});
document.addEventListener('visibilitychange', ()=>{
 if(document.hidden) return;
 if(isEditing && lastFocusedCell){
   setTimeout(()=>{
     const td=document.querySelector('td[data-ri="'+lastFocusedCell.ri+'"][data-ci="'+lastFocusedCell.ci+'"] input');
     if(td) td.focus();
   }, 80);
 }
});
window.addEventListener('focus', ()=>{
 if(isEditing && lastFocusedCell){
   setTimeout(()=>{ const inp=document.querySelector('.cell-editing input'); if(inp) inp.focus(); }, 40);
 }
});
loadTree();
</script></body></html>
