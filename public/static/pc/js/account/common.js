$('#login_form').find("#account,#psw").keydown(function(e){
    if(e.which == 13) $("#login_btn").click();
});

function show_tip(show,text){
    if(show){
        var data_tip = $('<div class="oper-tip"><i class="iconfont icon-infocircle"></i><span></span></div>');
        data_tip.find('span').text(text);
        $('#tip-wrap').append(data_tip);
    }else{
        $(".oper-tip").remove();
    }
}

function _dispose($el,d){
    var url = d.url;
    var form = d.form;
    var tip = d.tip;
    var text = d.text;
    var success_url = d.success_url;
    var _vfunc = d._vfunc;

    //移除提示内容
    show_tip(false);

    if(tip=='') tip = text;

    form = $("#"+form);
    if(form.length!=1){
        return false;
    }
    var form_el = $(form[0]).find('input,select,textarea');

    //表单验证
    if(_vfunc in window){
        var r = window[_vfunc](form_el.serializeArray());
        if(r!=""){
            show_tip(true,r);
            return false;
        }
    }

    //进度条
    var spin = $('<div class="ant-spin ant-spin-lg ant-spin-spinning"><span class="ant-spin-dot ant-spin-dot-first"></span><span class="ant-spin-dot ant-spin-dot-second"></span><span class="ant-spin-dot ant-spin-dot-third"></span><div class="ant-spin-text">加载中...</div></div>');
    $('#tip-wrap').append(spin);
    //按钮禁止 改变文字
    $el.addClass('disabled')
        .attr('data-text',text)
        .attr('disabled','disabled')
        .text(tip);


    var data = form_el.serialize();
    _post(url, data, {
        res_back: function(res){
            //恢复按钮
            $el.removeClass('disabled')
                .removeAttr('disabled')
                .text($el.attr(('data-text')))
                .remove('data-text');
            //移除进度条
            spin.remove();
        },
        //处理返回数据
        data_back: function(data){
            if(data.status){
                if(data.info.info){
                    antd.message.success(data.info.info);
                }
                setTimeout(function(){
                    if(data.info.redirect_uri){
                        window.location.href = data.info.redirect_uri;
                    }else if(success_url!="") {
                        window.location.href = success_url;
                    }
                },500);

            }else{
                show_tip(true,data.info);
            }
        }
    });
}
