var pendingRequests = {};
$.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
    var key = options.url;
    if (!pendingRequests[key]) {
        pendingRequests[key] = jqXHR;
    }else{
        //jqXHR.abort();    //放弃后触发的提交
        pendingRequests[key].abort();   // 放弃先触发的提交
    }

    var complete = options.complete;
    options.complete = function(jqXHR, textStatus) {
        pendingRequests[key] = null;
        if ($.isFunction(complete)) {
            complete.apply(this, arguments);
        }
    };
});

function updateForm(dom_status_change,pjaxdom,btn){
    layer.open({
        'type':1,
        'content':dom_status_change,
        btn:btn,
        yes:function(layindex,laydom){
            var dom_form=laydom.find('form');
            $.post(dom_form.attr('action'),dom_form.serialize(),function(res){
                if(res.status){
                    layer.msg(res.msg,{time:1000},function(){
                        $.pjax.reload(pjaxdom);
                    });
                }else{
                    layer.msg(res.msg);
                }
                layer.close(layindex);
            },'json');
        }
    });
}

function batchUpdate(dom_status_change,pjaxDom,selectBtn,comfirMsg,confirmBtn){
    layer.open({
        'type':1,
        'content':dom_status_change,
        btn:selectBtn,
        yes:function(layindex,laydom){
            var dom_form=laydom.find('form');
            layer.confirm(comfirMsg,{
                btn:confirmBtn,
                yes:function(){
                    var load=layer.load();
                    $.post(dom_form.attr('action'),dom_form.serialize(),function(res){
                        layer.close(load);
                        if(res.status){
                            layer.msg(res.msg,{time:1300},function(){
                                $.pjax.reload(pjaxDom);
                            });
                        }else{
                            layer.alert(res.msg);
                        }
                        layer.close(layindex);
                    },'json');
                }
            });
        }
    });
}

var toastrOption={
    success:{
        "closeButton": true,
        "debug": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    },info:{
        "closeButton": true,
        "debug": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "onclick": null,
        "showDuration": "500",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    },warning:{
        "closeButton": true,
        "debug": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "onclick": null,
        "showDuration": "5000",
        "hideDuration": "1000",
        "timeOut": "10000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    },error:{
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "showDuration": "5000",
        "hideDuration": "2000",
        "timeOut": "5000",
        "extendedTimeOut": "10000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
};