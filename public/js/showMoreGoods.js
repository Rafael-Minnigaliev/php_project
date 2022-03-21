$('#showmore-button').click(function (){
    let item = $(this);
    let page = item.attr('page');
    let gcat = item.attr('gcat');
    let gen = item.attr('gen');
    let uid = item.attr('uid');
    let admin = item.attr('admin');
    page++;
    $.ajax({
        url: "index.php?c=catalog&act=ShowMoreGoods&ajax=1",
        type: "POST",
        data: { page, gcat, gen, uid, admin },
        success: function(data){
            $('#catalog__list').append(data);
        }
    });
    item.attr('page', page);
    if(page == item.attr('max_page')){
        item.hide();
    }
    return false;
});