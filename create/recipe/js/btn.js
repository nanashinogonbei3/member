

    $('input').on('change', function () {
        var file = $(this).prop('files')[0];
        $('p').text(file.name);
    });

