/** _post **/
$('._post').click(function(){
    var $el = $(this);
    var url = $el.attr('data-url');
    var form = $el.attr('data-form');
    var tip = $el.attr('data-tip');
    var text = $el.text();
    var success_url = $el.attr('data-success-url') || "";

    var _vfunc = $el.attr('data-verify');

    if("_dispose" in window){
        _dispose($el,{
            url: url,
            form: form,
            tip: tip,
            text: text,
            success_url: success_url,
            _vfunc: _vfunc
        });
    }

});

function _post(url, data, back){
    if(1!=1){
        fetch(url,{
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: data
        }).then(function(res){
            back.res_back(res);
            return res.json();

        }).then(function(data){
            back.data_back(data);

        }).catch(function(e){
            console.log('_post：返回数据异常');

        });

    }else{
        $.post(url, data).done(function(data){
            if(typeof back.res_back == "function") back.res_back();
            if(typeof back.data_back == "function") back.data_back(data);
        });

    }
}
/** _get **/
$('._get').click(function(){
    var $el = $(this);
    var url = $el.attr('data-url');
    var form = $el.attr('data-form');
    var tip = $el.attr('data-tip');
    var text = $el.text();
    var success_url = $el.attr('data-success-url');

    var _vfunc = $el.attr('data-verify');

    _public_get($el,{
        url: url,
        success_url: success_url
    });

    if("_dispose" in window){
        _dispose($el,{
            url: url,
            form: form,
            tip: tip,
            text: text,
            success_url: success_url,
            _vfunc: _vfunc
        });
    }

    return false;

});

function _get(url, back){
    $.get(url, function(data){
        if(typeof back.res_back == "function") back.res_back();
        if(typeof back.data_back == "function") back.data_back(data);
    })
}

function _public_get($el,d){
    var url = d.url;
    var success_url = d.success_url;

    _get(url, {
        //处理返回数据
        data_back: function(data){
            if(data.status){
                if(success_url!="")window.location.href = success_url.toUpperCase()=="SELF" ? window.location.href : success_url;
            }
        }
    });
}

var mwApi = {
    open_mw_spin: function () {
        $('body').append(
            '<div class="mw-modal-mask" data-></div>'+
            '<div class="mw-modal-wrap vertical-center-modal">'+
            '<div class="mw-modal-content">'+
            '<div class="ant-spin ant-spin-spinning">'+
            '<span class="ant-spin-dot ant-spin-dot-first"></span>'+
            '<span class="ant-spin-dot ant-spin-dot-second"></span>'+
            '<span class="ant-spin-dot ant-spin-dot-third"></span>'+
            '<div class="ant-spin-text">加载中...</div>'+
            '</div>'+
            '</div>'+
            '</div>'
        ).addClass('mw-modal-open');
    },
    close_mw_spin: function () {
        $('.mw-modal-mask,.mw-modal-wrap').remove();
        $('body').removeClass('mw-modal-open');
    }
};

var baseUrl = document.getElementsByTagName('base')[0].href;

function getBaseUrl(){
    return document.getElementsByTagName('base')[0].href;
}

//addons spcart
$('.spct-slide .del a').click(function(){
    $('.spct-slide .mask').css('display','flex');
    var _delli = $(this).parentsUntil('.spli');
    _delli = $(_delli[_delli.length-1]);

    var url = document.getElementsByTagName('base')[0].href + '/Api/spcart_delete';
    var data = {id: _delli.attr('data-id')};
    _post(url,jQuery.param(data),{data_back: function(data){
        message = antd.message;
        if(data.status){
            //如果成功，删除记录
            _delli.remove();
            if($('.spct-slide .spli li').length==0){
                $('.spct-slide .spli').remove();
                $('.spct-slide').prepend('<div class="empty">购物车里什么都没有哦~~</div>');
            }
            $('.spct-slide .mask').hide();
        }else{
            if(typeof data.info.redirect !="undefined"){
                message.error(data.info.info);
                setTimeout(function(){
                    window.location.href = data.info.redirect;
                },1000);
            }else{
                message.error('删除失败！');
            }
        }
    }});

});

//search
$('.search-bar .search-btn').click(function(){
    var s = $('#search').val();
    if($.trim(s)!='') window.location.href = getBaseUrl() + '/index/search/s/' + s;
});
$('#search').keydown(function(event){
    if(event.which==13){
        var s = $(this).val();
        if($.trim(s)!='') window.location.href = getBaseUrl() + '/index/search/s/' + s;
    }
});