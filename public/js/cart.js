function addToCart(gId, id){
    $.ajax({
        url: "index.php?c=cart&act=AddToCart&ajax=1",
        type: "POST",
        data: { gId, id },
        success: function(data){
            $('#cart__id').empty();
            $('#cart__id').append(data);
        }
    });
}

function deleteFromCart(id){
    $.ajax({
        url: "index.php?c=cart&act=DeleteFromCart&ajax=1",
        type: "POST",
        data: { id },
        success: function(data){
            if(data != " "){
                let array = data.split(" ");
                $('#'+id).detach();
                $('#cart__id').empty();
                $('#cart__id').append(array[0]);
                $('#pay__price').empty();
                $('#pay__price').append(array[1]);
            }else{
                $('#cart__id').empty();
                $('#cart').empty();
                $('#cart').append("<h2 class=\"cart__title\">Корзина пуста!</h2>");
            }
        }
    });
}

function changeCountFromCart(id){
    let val = document.querySelector('#count_'+id).value;
    $.ajax({
        url: "index.php?c=cart&act=ChangeCountFromCart&ajax=1",
        type: "POST",
        data: { id, val },
        success: function(data){
            let array = data.split(" ");
            $('#cart__price_'+id).empty();
            $('#cart__price_'+id).append(array[0]);
            $('#pay__price').empty();
            $('#pay__price').append(array[1]);
            $('#cart__id').empty();
            $('#cart__id').append(array[2]);
        }
    });
}