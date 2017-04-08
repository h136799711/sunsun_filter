function info(msg) {
    antd.Modal.info({
        title: '提示',
        content: msg,
        onOk:function(){}
    });
}
$('.btn-code').click(function(){
    var $el = $(this);
    var url = $el.attr('data-url');
    //按钮禁止 改变文字
    $el.addClass('disabled')
        .attr('disabled','disabled')
        .text('正在发送...');

    _post(url, jQuery.param({}), {
        res_back: function(res){
            //恢复按钮
            $el.removeClass('disabled')
                .removeAttr('disabled')
                .text('发送验证码');
        },
        //处理返回数据
        data_back: function(data){
            if(data.status){
                if(data.info.info){
                    info(data.info.info);
                }
            }else{
                info(data.info);
            }
        }
    });

});

$('.btn.save').click(function(){
    var account = $('#account');
    var code = $('#code');
    if($.trim(account.val())==''){
        antd.message.error('必须填写收款账户!');
        account.focus();
        return;
    }
    if($.trim(code.val())==''){
        antd.message.error('必须填写验证码!');
        code.focus();
        return;
    }
    bind.submit();
});