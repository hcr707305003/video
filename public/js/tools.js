$(document).ready(function(){


	//审核菜单下的查看（审核）页面，js-ajax-forms提交
	//--------------------------ajax提交表单-------------------
    $(".js-ajax-close-btn").on('click', function(e) {
        e.preventDefault();
        Wind.use("artDialog", function() {
            art.dialog({
                id : "question",
                icon : "question",
                fixed : true,
                lock : true,
                background : "#CCCCCC",
                opacity : 0,
                content : "您确定需要关闭当前页面嘛？",
                ok : function() {
                    setCookie("refersh_time", 1);
                    window.close();
                    return true;
                }
            });
        });
    });
    /////---------------------
    Wind.use('validate', 'ajaxForm', 'artDialog', function() {
        //javascript
        var form = $('form.js-ajax-forms');
        //ie处理placeholder提交问题
        if ($.browser && $.browser.msie) {
            form.find('[placeholder]').each(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            });
        }

        var formloading = false;
        //表单验证开始
        form.validate({
            //是否在获取焦点时验证
            onfocusout : false,
            //是否在敲击键盘时验证
            onkeyup : false,
            //当鼠标掉级时验证
            onclick : false,
            //验证错误
            showErrors : function(errorMap, errorArr) {
                //errorMap {'name':'错误信息'}
                //errorArr [{'message':'错误信息',element:({})}]
                try {
                    $(errorArr[0].element).focus();
                    art.dialog({
                        id : 'error',
                        icon : 'error',
                        lock : true,
                        fixed : true,
                        background : "#CCCCCC",
                        opacity : 0,
                        content : errorArr[0].message,
                        cancelVal : '确定',
                        cancel : function() {
                            $(errorArr[0].element).focus();
                        }
                    });
                } catch (err) {
                }
            },
            //验证规则
            rules : {
                'active' : {
                    required : 1
                }
            },
            //验证未通过提示消息
            messages : {
                'active' : {
                    required : '请审核'
                }
            },
            //给未通过验证的元素加效果,闪烁等
            highlight : false,
            //是否在获取焦点时验证
            onfocusout : false,
            //验证通过，提交表单
            submitHandler : function(forms) {
                if (formloading)
                    return;
                $(forms).ajaxSubmit({
                    url : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                    dataType : 'json',
                    beforeSubmit : function(arr, $form, options) {
                        formloading = true;
                    },
                    success : function(data, statusText, xhr, $form) {
                        formloading = false;
                        console.log(data);
                        if (data.status) {
                            //添加成功
                            setCookie("refersh_time", 1);
                            if (data.url){
                                Wind.use("artDialog", function() {
                                    art.dialog({
                                        id : "succeed",
                                        icon : "succeed",
                                        fixed : true,
                                        lock : true,
                                        background : "#CCCCCC",
                                        opacity : 0,
                                        content : data.info,
                                        button : [{
                                            name : '确定',
                                            callback : function() {
                                                window.parent.location.reload();
                                                window.parent.layer.closeAll();
                                            }
                                        },{
                                            name : '查看',
                                            callback : function() {
                                                window.parent.location.replace(data.url);
                                                window.parent.layer.closeAll();
                                            }
                                        }
                                        ]
                                    });
                                });
                            }else {
                                Wind.use("artDialog", function() {
                                    art.dialog({
                                        id : "succeed",
                                        icon : "succeed",
                                        fixed : true,
                                        lock : true,
                                        background : "#CCCCCC",
                                        opacity : 0,
                                        content : data.info,
                                        button : [{
                                            name : '确定',
                                            callback : function() {
                                                window.parent.location.reload();
                                                window.parent.layer.closeAll();
                                            }
                                        } ]
                                    });
                                });
                            }
                        } else {
                            artdialog_alert(data.info);
                        }
                    }
                });
            }
        });
    });
    //--------------------------ajax提交表单-------------------

    //弹框 弹出审核/查看页面
    $('.handle').click(function(){
        var url =$(this).attr('jump');
        layer_view(url);
    });

    //审核菜单下的查看（审核）页面
    function layer_view(url) {
        //iframe窗
        Wind.css('layer3');
        Wind.use('layer3', function () {
            layer.open({
                type: 2,
                title: false,
                closeBtn: 1, //关闭按钮
                shade: [0.5,'#000'],//遮幕层
                area: ['95%', '95%'],
                anim: 0,//弹出动画
                zIndex: 1000,//弹出动画
                content: [url, 'yes'], //iframe的url，no代表不显示滚动条
                end: function(){

                }
            });
        });
    }


//        点击返回按钮，关闭iframe
    $('.view_back').click(function () {
        window.parent.layer.closeAll();
    });

    $('.show_img').click(function () {
        var id = $(this).attr('id');
        show_img_list(id);
    });



});