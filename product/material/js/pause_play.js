$(function () {
    $('#homeCarousel').carousel({
      pause: false
    });
    $('#playButton').on('click',function () {
      $('#homeCarousel').carousel('cycle');
    });
    $('#pauseButton').on('click',function () {
      $('#homeCarousel').carousel('pause');
    });
  });