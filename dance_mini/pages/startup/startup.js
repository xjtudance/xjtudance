// pages/startup/startup.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
  
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setStartupImg();
    setTimeout(function () {
      wx.navigateTo({
        url: '../index/index',
      });
    }, 2000);
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  },

  /**
 * 根据当前节日设置启动图片
 */
  setStartupImg: function () {
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var str_date = [year, month, day].map(this.formatNumber).join('');
    if (str_date == '20171004') { // 2017年中秋
      this.setData({
        startup_img: '../../images/mid-autumn.jpg', // 启动图片
      });
    } else if ('20171001' <= str_date && str_date <= '20171007') { // 国庆节
      this.setData({
        startup_img: '../../images/national-day.jpg', // 启动图片
      });
    } else {
      wx.navigateTo({
        url: '../index/index',
      });
    }
  },

  /**
   * 格式化日期字符串
   */
  formatNumber: function (n) {
    n = n.toString()
    return n[1] ? n : '0' + n
  },
})