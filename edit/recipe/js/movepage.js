

var unloaded = function (e) {
  // カスタムメッセージの設定（後述するが、EdgeとIEしか表示されない）
  var confirmMessage = '離脱するの？';
  e.returnValue = confirmMessage;
  return confirmMessage;
};

// beforeunloadイベントの登録
window.addEventListener('beforeunload', unloaded, false);

// 特定のボタンがクリックされたときはアラートを表示しないようにもできます。
document.getElementById('mySubmit').addEventListener('click', function(){
  // submit時はアラート表示させない
  window.removeEventListener('beforeunload', unloaded, false);
});
