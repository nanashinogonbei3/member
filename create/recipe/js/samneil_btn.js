$(function() {
    $('.imageList__thumbnail').on('click',function() {
  
      if ( $(this).hasClass("selected") ) {
          return;
      }
  
      var selectedImgSrc = $(this).children('img').attr('src');
  
      $('.selected').removeClass('selected');
      $(this).addClass('selected');
  
      $('.imageList__view').children('img').attr('src', selectedImgSrc);
    });
  })