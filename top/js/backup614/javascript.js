function changeimg(url,e) {
    document.getElementById("img").src = url;
    let nodes = document.getElementById("thumb_img");
    let img_child = nodes.children; //id名「thumb_img」配下の子要素を取得
    for (i = 0; i < img_child.length; i++) { //要素の数ループさせる
     img_child[i].classList.remove('active') //要素に付与されているすべてのclass名「active」を削除する
    }
    e.classList.add('active'); //クリック（タップ）した要素にclass名「active」を付与する
   }
  