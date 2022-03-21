function deleteFromOrder(goodId, user, orderId){
    location="index.php?c=order&act=DeleteGoodFromOrder&goodId="+goodId+"&user="+user+"&orderId="+orderId;
}

function changeCountFromOrder(goodId, user, orderId){
    let count = document.querySelector("#count_"+goodId).value;
    location="index.php?c=order&act=ChangeCountFromOrder&goodId="+goodId+"&count="+count+"&user="+user+"&orderId="+orderId;
}