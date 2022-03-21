$("#form").on("submit", function(e){
    e.preventDefault();
    $.ajax({
        url: 'index.php?c=order&act=OrderRegChangeData&ajax=1',
        method: 'post',
        data: $(this).serialize(),
        success: function(data){
            $('#message').html(data);
        }
    });
});
