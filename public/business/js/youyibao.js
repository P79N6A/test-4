/**
 * Created by AIMPER on 2016/10/28.
 */

/*
 通用 http 请求函数，调用参数方法:
 1.get 请求：
 选定元素必须有 data-url 属性存放请求的目标 URL
 选定的元素必须有 data-id 属性存放跟请求一起发送到目标地址的参数
 2.post 请求
 选定元素必须是一个包含 form 元素的表单

 例子：
 1.get：
 $('a.delete').send('get');
 2.post：
 $('form#post').send('post');


 */
$.fn.send = function (type) {
    if (!type) {
        type = 'get';
    }
	
	
    $.ajax({
        type: type,
        url: type == 'get' ? $(this).attr('data-url') : $(this).attr('action'),
        data: type == 'get' ? ($(this).attr('data-type') + '=' + $(this).attr('data-id')) : $(this).serialize(),
        success: function (data) {
            if (data.code != undefined && data.code == 200) {
                if (type == 'get') {
                    window.location.reload();
                } else {
                    window.location.href = data.url;
                }
            } else if (data.code != 200) {
                alert(data.msg);
            }
        }
    });
}


var youyibao = {

    /**
     * http 请求方法
     * @param obj   Object  jQuery对象
     * @param method    String  请求方法，get 或 post
     * @param whetherJump   Bool    是否跳转
     * @param callback   Function    回调函数
     * */
    httpSend: function (obj, method, whetherJump, callback) {
        if (!method) {
            method = 'get';
        }
		
		console.log(12345);
		var index = layer.load(0, {shade: false}); 
		
        $.ajax({
            type: method,
            url: method == 'get' ? obj.attr('data-url') : obj.attr('action'),
            data: method == 'get' ? (obj.attr('data-type') + '=' + obj.attr('data-id')) : obj.serialize(),
            success: function (data) {
					layer.close(index);
                if(callback != undefined && callback.length > 0){
                    callback(data);
                }
                if (data.code != undefined && data.code == 200) {
                    layer.msg(data.msg, {icon: 6}, function () {
                        if (method == 'get') {
                            if (whetherJump) {
                                if (data.url != undefined) {
                                    window.location.href = data.url;
                                } else {
                                    window.location.reload();
                                }
                            }
                        } else {
                            if (whetherJump && data.url != undefined) {
                                window.location.href = data.url;
                            }
                        }
                    });
                } else if (data.code != 200) {
                    layer.msg(data.msg, {icon: 5});
                }
            }
        });
    },

    /**
     * 根据提供的省份id获取对应城市列表
     * */
    getCities: function (pid, callback) {
        $.ajax({
            url: 'get-cities',
            type: 'get',
            data: {
                pid: pid
            },
            success: function (data) {
                callback(data);
            }
        });
    },

    /**
     * 根据城市id获取对应的区/县
     * */
    getBlocks: function (cid, callback) {
        $.ajax({
            type: 'get',
            url: 'get-blocks',
            data: {
                cid: cid
            },
            success: function (data) {
                callback(data);
            }
        });
    },

    /**
     * 根据门店ID获取对应的套餐列表
     * */
    getPackages: function (id, callback) {
        $.ajax({
            type: 'get',
            url: 'get-packages',
            data: {
                id: id
            },
            success: function (data) {
                callback(data);
            }
        });
    },

    /**
     * 基于百度 WebUploader 实现文件上传
     * 本插件必须后于 webuploader.min.js 引入
     * @param picker       jQuery对象，指定替换成文件上传表单的元素
     * @param from         jQuery对象，指定上传成功后存放服务器返回数据的隐藏域
     * @param preview      jQuery对象，指定上传成功后存放预览（图片）的容器
     * @return void
     */
    fileUpload: function (picker, form, preview) {
        // 实例化上传对象
        var uploader = WebUploader.create({
            swf: '/Uploader.swf',    // 上传flash组件
            server: '/upload',       // 上传地址
            pick: picker,
            auto: true,
            resize: false
        });

        // 监听加入队列事件
        uploader.on('fileQueued', function (file) {
            var $img = $('<img>');
            alert(uploader);

            // 创建缩略图
            // 如果为非图片文件，可以不用调用此方法。
            // thumbnailWidth x thumbnailHeight 为 100 x 100
            uploader.makeThumb(file, function (error, src) {
                if (error) {
                    $img.replaceWith('<span>不能预览</span>');
                    return;
                }
                $img.attr('src', src);
                preview.append($img);
            }, 160, 90);
        });

        // 监听上传成功事件
        var str = '';
        uploader.on('uploadSuccess', function (file, response) {
            str += String(response.data[0].id) + ',';
            form.val(str);
        });
    },

    jumpPage:function(page){
        if(!page){
            alert('请输入页码');
            return false;
        }
        var pattern = /\?/gi;

        if(pattern.exec(location.href)){
            location.href = location.href.replace(/&+page=\d+$/,'') + '&page=' + page;
        }else{
            location.href = location.href.replace(/page=\d+$/,'') + '?page=' + page;
        }
    },

}