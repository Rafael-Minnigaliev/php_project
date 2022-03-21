function show() {
    let id = $('#order_list').attr('user');
    $.ajax({
        url: "index.php?c=order&act=СheckStatusUpdate&ajax=1",
        type: "POST",
        data: { id },
        success: function(answer){
            let data = JSON.parse(answer);
            for(let i of data){
                $("#statusUp_"+i.id).empty();
                $("#statusUp_"+i.id).append(i.status);
            }
        }
    });
}

$(document).ready(function(){
    show();
    setInterval('show()', 60000);
});