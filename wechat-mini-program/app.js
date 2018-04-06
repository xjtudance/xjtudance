//app.js
App({
  global_data: {
    //server_url: 'http://xjtudance.top.local/xjtudance/test/', // 服务器地址(test)
    server_url: 'https://xjtudance.top/xjtudance/', // 服务器地址
    userInfo: null, // 用户信息
    systemInfo: null, // 系统信息
    dancer_list: null, // 报到舞友列表
    huyou_list: null, // 忽悠列表
  },

  onLaunch: function () {
/*    wx.request({
      url: this.global_data.server_url + 'php/testMongo.php',
      method: "POST",
      success: function (res) {
        console.log(res.data);
        if (res.header.errMsg != 0) {
          console.log(res.header.errMsg);
        } else {
          console.log(res.data);
        }
      },
    });*/
    console.log('server_url: ' + this.global_data.server_url);

    // 监听网络状态变化
    wx.onNetworkStatusChange(function (res) {
      var net_type = res.networkType;
      if (!res.isConnected) {
        wx.showToast({ // 断网提示
          title: 'oops！貌似断网了...',
          duration: 1000,
        });
      };
    });
  }
})