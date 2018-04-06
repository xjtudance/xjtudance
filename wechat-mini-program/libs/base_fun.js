/*******************************************************************************
基本函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-08
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

var net_fun = require('./net_fun.js');
var app = getApp();

/**
 * 从数据库读取列表信息
 * @param options object 传入参数，json形式的数据，具体项包含：
 *  collection_name string 集合名称
 *  skip integer 跳过的数据数量
 *  limit integer 获取的数据数量
 *  list_order string 获取数据的顺序
 *  query object 查询条件
 *  getValues string 要获取的具体项
 *  extraData array 需要从其他集合获取相关的数据
 *  success function request返回成功时执行的函数
 *  fail function request返回失败时执行的函数
 *  complete function request完成时执行的函数
 */
function listData(options) {
  if (typeof options !== 'object') {
    var message = '请求传参应为 object 类型，但实际传了 ' + (typeof options) + ' 类型';
    console.log(message);
    // throw new RequestError(constants.ERR_INVALID_PARAMS, message);
  }

  var collection_name = options.collection_name;
  var skip = options.skip || 0; // 默认不跳过
  var limit = options.limit || 1; // 默认读取一条信息
  var list_order = options.list_order || '_id';
  var query = options.query || {};
  var getValues = options.getValues || '';
  var extraData = options.extraData || [];
  var noop = function noop() { };
  var success = options.success || noop;
  var fail = options.fail || noop;
  var complete = options.complete || noop;

//  wx.showLoading({
//    title: '正在加载，稍等一下下...',
//    mask: true,
//  });
  net_fun.request({
    url: 'php/wx_listData.php',
    data: {
      'collection_name': collection_name,
      'skip': skip,
      'limit': limit,
      'list_order': list_order,
      'query': query,
      'getValues': getValues, // 用/号分隔需要获取的value
      'extraData': extraData,
    },
    success: function (res) {
      console.log(res.data);
      //wx.hideLoading();
      if (res.data == []) {
        wx.showToast({
          title: '没有更多了...',
          duration: 1000
        });
      } else if (limit == 1) {
        res.data = res.data[0]; // 如果只获取一条信息，则吧这条信息的具体内容从列表中取出来
      }
      success(res);
    },
    fail: function (res) {
      //wx.hideLoading();
      fail(res);
    },
    complete: function () {
      //wx.hideLoading();
      complete();
    },
  });
}

module.exports = {
  listData: listData,
};