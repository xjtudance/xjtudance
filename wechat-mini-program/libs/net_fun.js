/*******************************************************************************
网络处理函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-10-06
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

var app = getApp();

/**
 * request函数封装
 * @param options object 传入参数，json形式的数据，具体项包含：
 *  url string 请求url在服务器端的相对路径
 *  data object 传给服务器的数据
 *  header object 请求头部
 *  method string 请求方式，默认为POST
 *  success function request返回成功时执行的函数
 *  fail function request返回失败时执行的函数
 *  complete function request完成时执行的函数
 */
function request(options) {
  if (typeof options !== 'object') {
    var message = '请求传参应为 object 类型，但实际传了 ' + (typeof options) + ' 类型';
    console.log(message);
    // throw new RequestError(constants.ERR_INVALID_PARAMS, message);
  }

  var url = options.url || ''; // 默认为空
  var data = options.data || {}; // 默认为空
  var header = options.header || { 'content-type': 'application/json' };
  var method = options.method || "POST";
  var noop = function noop() { };
  var success = options.success || noop;
  var fail = options.fail || noop;
  var complete = options.complete || noop;

//  wx.showLoading({
//    title: '正在加载，稍等一下下...',
//    mask: true,
//  });
  wx.request({
    url: app.global_data.server_url + url,
    data: data,
    header: header,
    method: method,
    success: function (res) {
      // console.log(res.data);
      //wx.hideLoading();
/*      if (!res.header.errMsg || res.header.errMsg != 0) {
        console.log(res.header.errMsg || 'header中未定义errMsg！请检查服务器端调用的代码。');
        wx.showToast({
          title: res.header.errMsg || 'header中未定义errMsg！',
          image: '../../images/more.png',
          duration: 1500,
          mask: false,
        });
      } else {
        success(res);
      }*/
      success(res);
    },
    fail: function (res) {
      //wx.hideLoading();
      wx.showToast({
        title: 'oops，加载失败了...',
        image: '../../images/more.png',
        duration: 1500,
        mask: false,
      });
      fail(res);
    },
    complete: function () {
      //wx.hideLoading();
      complete();
    },
  });
}

module.exports = {
  request: request,
};