
function movePage(){
   if(!window.confirm('このページから離れますか？')){
     window.alert('キャンセルされました'); // 警告ダイアログを表示
     return false;

   } 
   return true;
 }

