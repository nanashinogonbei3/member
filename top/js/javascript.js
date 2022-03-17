function changeimg(url,e) {
  // alert('アラート');
  document.getElementById("img").src = url;
  // const recipe_name = document.getElementById("recipe_name");
  // recipe_name.style.visibility = "hidden";
  let nodes = document.getElementById("thumb_img");
  let img_child = nodes.children; //id名「thumb_img」配下の子要素を取得
  for (i = 0; i < img_child.length; i++) { //要素の数ループさせる
   img_child[i].classList.remove('active') //要素に付与されているすべてのclass名「active」を削除する
  }
  e.classList.add('active'); //クリック（タップ）した要素にclass名「active」を付与する
 }



//  以下改訂版：サムネイル画像にリンクを貼る
 function changeimg(url,e) {
  // alert('アラート');
  document.getElementById("img").src = url;
  // const recipe_name = document.getElementById("recipe_name");
  // recipe_name.style.visibility = "hidden";
  let nodes = document.getElementById("thumb_img");
  let img_child = nodes.children; //id名「thumb_img」配下の子要素を取得
  for (i = 0; i < img_child.length; i++) { //要素の数ループさせる
   img_child[i].classList.remove('active') //要素に付与されているすべてのclass名「active」を削除する
  }
  e.classList.add('active'); //クリック（タップ）した要素にclass名「active」を付与する
 }

 this.href = img.parent('a').attr('href') || null;
 
 
//  以下、サムネイル画像（大きい画像）にリンクをつける方法。参照URL：https://wood-roots.com/web/javascript/236
 //160行目あたりのli>imgをli imgに変更
 //               this.sourceImgs = $('li>img',this.$el);
                this.sourceImgs = $('li img',this.$el);
  
  
 //509行目あたりに追加
                _img.fadeIn();
                //以下の行を足します。1枚目の画像を読み込んだ時にリンクを設置します。data-link属性を保持させるのは後ほどの処理で
                //data('link')のほうが今っぽいですがどこかでclone(false)が走っているのかdataが受け継がれなかった
                if(_img.attr('data-link')){
                     _img.wrap('<a href="' + _img.attr('data-link') + '"></a>');
                }
  
 //526行目あたりに追加
                               .attr('src',gvImage.src.panel)
                              
                               //以下の行を追加。画像自体にdata-link属性でリンク先を保持します。
                               .attr('data-link',gvImage.href);
  
  
 //600行目あたりに追加
 //今回はpanel_animationがfadeのものに適用させてますが、それ以外のcrossfadeやslideに適用させる場合はその部分（caseの中）に書いてください。
 //
                          if(panel.children().attr('data-link')){
                               panel.children().wrap('<a href="' + panel.children().attr('data-link') + '"></a>');
                          }
                          break;