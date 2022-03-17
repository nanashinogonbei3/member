
function dispDelete(){
   if(!window.confirm('本当に削除してよろしいですか？')){
     window.alert('キャンセルされました'); // 警告ダイアログを表示
     return false;

   } 
   return true;
 }

