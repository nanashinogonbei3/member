
$(function(){

    // カルーセルパネル
    $("#carouselInner").css("width",200*$("#carouselInner ul.column").length+"px");
    // #carousel {width:200px}今回はforeachでliタグに全部おさまり、1回すので 200px*1の幅。
    $("#carouselInner ul.column:last").prependTo("#carouselInner");
    // 最後になったら、左へ-200pxずらす。
    $("#carouselInner").css("margin-left","-400px");

    // Prevボタン←
    $("#carouselPrev").click(function(){
        // Prevボタンが押された時のイベントです。
        // $("#carouselPrev,#carouselNext").hide();
        // 左に移動したときに、前ボタンを’隠す’

        $("#carouselInner").animate({
            "margin-left" : parseInt($("#carouselInner").css("margin-left"))+200+"px"
            // そして左に移動した画像を、最後尾へ移動する、-200+200=0 最後尾に足されて+200なので0。
        },"slow","swing",

        // スローで”ゆっくり”スリングする。
        function(){
            $("#carouselInner").css("margin-left","-200px");
            // 次の画像が左へ200px(-200px)移動する
            $("#carouselInner ul.column:last").prependTo("#carouselInner");
            // 左へずれた画像が、最後尾へ移動する。
            // $("#carouselPrev,#carouselNext").show();
            // ”次へ”ボタン隠れていたものが表示
        });
    });

    // Nextボタン
    $("#carouselNext").click(function(){
        // Nextが押された時のイベントです。考え方はPrevボタンの時と反対方向ですが、
        // 考え方は、全く同じです。講師は”猪俣さん”動画解説が分かり易い。
        $("#carouselPrev,#carouselNext").show();

        $("#carouselInner").animate({
            "margin-left" : parseInt($("#carouselInner").css("margin-left"))-200+"px"
        },"slow","swing",

        function(){
            $("#carouselInner").css("margin-left","-200px");
            $("#carouselInner ul.column:first").appendTo("#carouselInner");
            $("#carouselPrev,#carouselNext").show();
        });
    });

    // 自動スライド
    var timerID = setInterval(function(){
        $("#carouselNext").click();
    },5000);

    // ナビボタンをhoverしている時は、インターバルクリア
    $("#carouselPrev,#carouselNext").hover(function(){
        clearInterval(timerID);
    },function(){
        timerID = setInterval(function(){
            $("#carouselNext").click();
        },5000);
    });

});

