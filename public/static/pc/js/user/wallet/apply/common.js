$('.btn.save').click(function(){
    var money = $('#money');
    var password = $('#password');
    if($.trim(money.val())==''){
        antd.message.error('必须填写金额!');
        money.focus();
        return;
    }else{
        if(parseFloat(money.val())<=0){
            antd.message.error('提现金额必须大于0!');
            money.focus();
            return;
        }
    }
    if(password.val()==''){
        antd.message.error('必须填写登录密码!');
        password.focus();
        return;
    }
    apply.submit();
});