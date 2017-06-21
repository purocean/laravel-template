<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Qywx;
use App\Http\Controllers\Controller;

class WechatController extends Controller
{
    public function code(Request $request)
    {
        if ($request->input('state') === 'WechatOAuth') { // 验证回调
            $next = $request->input('next');
            $code = $request->input('code');

            return redirect("/mobile.html#/login/?code={$code}&next=" . urlencode($next));
        } else {
            return redirect(Qywx::getJumpOAuthUrl(url()->full()));
        }
    }

    public function wxjs()
    {
        $jsApiPackage = Qywx::getJsApiPackage(url('mobile.html'));

        $content = <<< JS
wx.config({
    debug: false,
    appId: '{$jsApiPackage["corpid"]}',
    timestamp: {$jsApiPackage["timestamp"]},
    nonceStr: '{$jsApiPackage["nonceStr"]}',
    signature: '{$jsApiPackage["signature"]}',
    jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'onMenuShareQQ',
        'onMenuShareWeibo',
        'onMenuShareQZone',
        'startRecord',
        'stopRecord',
        'onVoiceRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'onVoicePlayEnd',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'translateVoice',
        'getNetworkType',
        'openLocation',
        'getLocation',
        'hideOptionMenu',
        'showOptionMenu',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'closeWindow',
        'scanQRCode',
        'chooseWXPay',
        'openProductSpecificView',
        'addCard',
        'chooseCard',
        'openCard',
    ]
});

wx.ready(function() {
    wx.hideOptionMenu();
});

JS;

        return response($content)->header('Content-Type', 'application/javascript');
    }
}
