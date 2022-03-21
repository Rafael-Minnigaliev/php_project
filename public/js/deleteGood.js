function deleteGood(id){
    $.ajax({
        url: "index.php?c=catalog&act=DeleteGood&ajax=1",
        type: "POST",
        data: { id },
        success: function(){
            $('#'+id).detach();
        }
    });
}