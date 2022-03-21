function changeForm(id){
    $.ajax({
        url: "index.php?c=catalog&act=GetChangeForm&ajax=1",
        type: "POST",
        data: { id },
        success: function(data){
            $('#'+id).empty();
            $('#'+id).append(data);
        }
    });
}

$('#good_form').on('submit', function(e){
    e.preventDefault();
    let id = $(this).attr('goodId');
    $.ajax({
            method: 'POST',
            url: 'index.php?c=catalog&act=ChangeGood&ajax=1',
            encType: 'multipart/form-data',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(data){
                $('#'+id).replaceWith(data);
            }
        }
    );
});