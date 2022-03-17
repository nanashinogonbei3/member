'use.strict';
{
   const dts = document.querySelectorAll('dt');
//    複数・・ALL取得（タグ、classは.クラス）
   dts.forEach(dt => {
// 繰り返し処理.質問ですに対して実装
      dt.addEventListener('click',() => {
        dt.parentNode.classList.toggle('appear');
       
          dts.forEach(el => {
            if(dt!==el){
              el.parentNode.classList.remove('appear');
            }
         });
      });
   });
}