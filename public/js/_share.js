/**
 * Created by WeiZeng on 2016/8/15.
 */
var des="精灵告诉你：你为什么一直单身？！";
var url=location.href;
var imgUrl="http://wx.ancai4399.com/_singledog/singledoge.jpg";

var test="";

// console.log( encodeURI(location.href.split('#')[0]) );
$.post(share,{url : encodeURI(location.href.split('#')[0])},function(data){
    data=JSON.parse(data);
    wx.config({
        debug: false,

        appId: data.appId,

        timestamp: data.timestamp,

        nonceStr: data.nonceStr,

        signature: data.signature,
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone',
        ]
    });

});

    wx.ready(function () {

        // 2. 分享接口
        // 2.1 监听“分享给朋友”，按钮点击、自定义分享内容及分享结果接口
        wx.onMenuShareAppMessage({
            desc: des,//
            link: url,//这里是当前页面的url
            imgUrl: imgUrl,//这里是图片的URL，有就取第一个，没有就用默认图片
            // trigger :function(){
            //     console.log("test")
            // }
        });

        // 2.2 监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
        wx.onMenuShareTimeline({
            // trigger :function(){
            //     test="test";
            //     console.log(test);
            // },
            desc: des,
            link: url,//这里是当前页面的url
            imgUrl: imgUrl,//这里是图片的URL，有就取第一个，没有就用默认图片
        });

        // 2.3 监听“分享到QQ”按钮点击、自定义分享内容及分享结果接口
        wx.onMenuShareQQ({
            title: title,
            desc: des,
            link: url,//这里是当前页面的url
            imgUrl: imgUrl,
            // trigger :function(){
            //     title=inputName.value + '单身的原因是: ' + reason.innerText;
            //     console.log(title);
            // }

        });

        // 2.4 监听“分享到微博”按钮点击、自定义分享内容及分享结果接口
        wx.onMenuShareWeibo({
            // title: title,
            desc: des,
            link: url,//这里是当前页面的url
            imgUrl: imgUrl,//这里是图片的URL，有就取第一个，没有就用默认图片
            // trigger :function(){
            //     console.log("test")
            // }

        });

        //2.5 监听“分享到QQ空间”按钮点击、自定义分享内容及分享结果接口
        wx.onMenuShareQZone({
            // title: title, // 分享标题
            desc: des, // 分享描述
            link: url, // 分享链接
            imgUrl: imgUrl, // 分享图标
            // trigger :function(){
            //     console.log("test")
            // }
        });
    });


