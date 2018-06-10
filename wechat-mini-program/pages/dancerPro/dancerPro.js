// pages/dancerPro/dancerPro.js
/*******************************************************************************
舞友列表页
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
				
Updated: 2017-11-09
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/
var base_fun = require('../../libs/base_fun.js');
var net_fun = require('../../libs/net_fun.js');
var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (e) {
    if (app != null) {
      this.setData({
        isBanban: app.global_data.userInfo ? app.global_data.userInfo.rights.banban.is : false,
      });
    } else {
      this.setData({
        isBanban: false,
      });
    }
    var _id = e._id;
    this.getDancerInfo(_id);
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
    return {
      title: '虫虫 ' + this.data.dancer_info.nickname + ' 报到',
      path: '/pages/dancerPro/dancerPro?_id=' + this.data._id,
      imageUrl: this.data.photo,
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 获取用户信息
   */
  getDancerInfo: function (_id) {
    // console.log(_id);
    this.setData({
      _id: _id,
    });
    var that = this;
    wx.showLoading({
      title: '正在跳转...',
      mask: true,
    });
																		  
    var getValues = '_id/nickname/gender/person_info.eggday/person_info.grade/person_info.major/person_info.height/person_info.hometown/dance.baodao_diaryId/dance.danceLevel/dance.selfIntro/dance.photos/dance.baodao';
    if (this.data.isBanban) { // 根据是否为管理员获得不同值
      getValues += '/wechat.id/person_info.QQ/person_info.contact/dance.knowdancefrom';
    }
    base_fun.listData({
      collection_name: 'users',
      query: {
				   
        '_id': that.data._id,
							   
      },
      getValues: getValues,
      extraData: [
        {
          collection_name: 'diaries',
          query: {
            '_id': 'dance.baodao_diaryId',
          },
          getValues: '_id/reply',
          extraData: [],
        },
      ],
      success: function (res) { // 成功获取信息
        // console.log(res.data);
        wx.hideLoading();
        if (res.data !== null) {
								   
          that.setData({
            dancer_info: res.data,
            photo: app.global_data.server_url + res.data.dance.photos[res.data.dance.photos.length - 1],
          });
		 that.setData({
            'dancer_info.person_info.eggday': res.data.person_info.eggday.slice(5),
          });
						
																				   
			 
          wx.setNavigationBarTitle({
            title: '虫虫' + res.data.nickname + '的信息',
          });
        } else { // 用户不在数据库中
          wx.showToast({
            title: 'ohoh，这位舞友的信息好像丢了~',
            image: '../../images/more.png',
            duration: 1500,
            mask: false,
          });
          setTimeout(function () {
            wx.navigateBack();
          }, 1500);
        }
      },
      fail: function () {
        wx.hideLoading();
					  
							 
										 
						 
					 
		   
        setTimeout(function () {
          wx.navigateBack();
        }, 1500);
      }
    });
  },

  /**
   * 预览照片
   */
  previewImage: function (e) {
    console.log(e);
    var current = e.target.dataset.src;
    wx.previewImage({
      current: current,
      urls: [current],
    });
  },

  /**
   * 点击回复打开回复窗口
   */
  openReplyArea: function () {
    var reply_atwho = '';
    //   if (e.currentTarget.dataset.father) {
    reply_atwho = '@' + this.data.dancer_info.nickname + ' ';
    //    }
    this.setData({
      reply_atwho: reply_atwho, // 给回复框占位符加上@被回复父帖的作者昵称
      showReplyArea: true,
    });
  },

  /**
   * 回复日记
   */
  replyDiary: function (e) {
    var that = this;
    var formId = e.detail.formId;
    var values = e.detail.value;
    var title = values.title;
    var content = values.content;
    if (title != "" || content != "") {
      net_fun.request({
        url: 'php/wx_replyDiary.php',
        data: {
          'title': title, // 标题
          "author": app.global_data.userInfo._id.$oid, // 作者
          'content': content, // 内容
          'mamaId': this.data.dancer_info.dance.baodao_diaryId.$oid, // 回复的主帖
          'fatherId': this.data.dancer_info.dance.baodao_diaryId.$oid, // 被回复帖子的id？？？？？？？？？
        },
        success: function (res) {
          console.log(res.data);
          /*var diary_list = that.data.diaries;
          var cur_mama = diary_list[that.data.diaryMama_replying];
          cur_mama.reply = res.data.concat(cur_mama.reply);
          var obj = new Object();
          obj[that.data.diaryMama_replying] = cur_mama;
          delete diary_list[that.data.diaryMama_replying];
          that.setData({
            diaryMama_replying: null,
            diaryFather_replying: null,
            titlePh_replying: null,
            showReplyArea: false,
            pen_y: app.global_data.systemInfo.windowHeight - 80,
            diaries: Object.assign(obj, diary_list), // 将数据传给全局变量diaries
          });*/
        }
      });
    } else {
      wx.showToast({
        title: '标题内容至少写一个吧？',
        image: '../../images/smiley-6_64.png',
        duration: 1500,
      });
    }
  },
})