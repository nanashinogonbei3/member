
function dispDelete(){
   if(!window.confirm('退会しますか？')){
     window.alert('キャンセルされました'); // 警告ダイアログを表示
     return false;

   } 
   return true;
 }

