function getKeyInObject(object,exp) {
    if(typeof object == 'undefined') return undefined;
    var args = exp.split('.');
    var o = object;
    for(var val of args){
        if(!o.hasOwnProperty(val)) return undefined;
        o = o[val];
    }
    return o;
}

function ajax_post(url,body,callback){
    $.post(url,body,
        function(data){

            if(data.code == 1){

                if(typeof getKeyInObject(callback,'success') == 'function'){
                    getKeyInObject(callback,'success')(data);
                }else{
                    $.toptip('操作成功', 'success');
                }
            }else{
                if(typeof getKeyInObject(callback,'error') == 'function'){
                    getKeyInObject(callback,'error')(data);
                }else{
                    $.toptip(data.msg, 'error');
                }
            }

            setTimeout(function () {
                if(data.code == 1111){
                    location.href = getBaseUrl() + '/index/logout';
                }else{
                    if (data.url) {
                        location.href = data.url;
                    }
                }

            }, 1500);
        });
}


var baseUrl = document.getElementsByTagName('base')[0].href;

function getBaseUrl(){
    return document.getElementsByTagName('base')[0].href;
}