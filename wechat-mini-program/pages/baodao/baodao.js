// pages/baodao/baodao.js
/*******************************************************************************
舞友报到页
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-11-08
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/
var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    gender_items: [ // 性别
      { name: 'gentleman', value: 'Boy' },
      { name: 'lady', value: 'Girl' },
      { name: 'else', value: 'Gender Questionning' },
    ],
    knowfrom_items: [ // 从哪里知道dance
      { name: '同学、朋友、教研室等', value: '同学、朋友、教研室等' },
      { name: '宣传单、海报等', value: '宣传单、海报等' },
      { name: '微信朋友圈、公众号、小程序等线上宣传', value: '微信朋友圈、公众号、小程序等线上宣传' },
      { name: '兵马俑BBS', value: '兵马俑BBS' },
      { name: '看到了舞会（思源、宪梓堂、四大发明广场）', value: '看到了舞会（思源、宪梓堂、四大发明广场）' },
      { name: '元旦游园会', value: '元旦游园会' },
      { name: '其他', value: '其他' },
    ],
    imgUrl_dancers: '../../images/dance1-200.png',
    isSubmitted: false, // 表单是否已提交
    btn_submit: { // 提交按钮设置
      disabled: false, // 是否禁用
      plain: false, // 按钮是否镂空，背景色透明
      loading: false, // 名称前是否带 loading 图标
    }
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    console.log("onload baodao");

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
    console.log("onshow baodao");

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
  onShareAppMessage: function (res) {
    return {
      title: '加入dance',
      path: '/pages/baodao/baodao',
      success: function (res) {
        // 转发成功
      },
      fail: function (res) {
        // 转发失败
      }
    }
  },

  /**
   * 用户选择性别
   */
  genderChange: function (e) {
    this.setData({
      gender: e.detail.value,
    });
    // console.log('radio发生change事件，携带value值为：', e.detail.value)
  },

  eggdayChange: function (e) {
    // console.log('picker发送选择改变，携带值为', e.detail.value)
    this.setData({
      eggday: e.detail.value,
    })
  },

  knowfromChange: function (e) {
    this.setData({
      knowdancefrom: e.detail.value,
    })
    // console.log(this.data.knowdancefrom);
  },

  /**
   * 选择照片
   */
  choosePhotos: function () {
    var that = this;
    wx.chooseImage({
      count: 1,
      success: function (res) {
        //console.log(res);
        //var tempFilePaths = res.tempFilePaths;
        that.setData({
          photo_items: res.tempFilePaths,
        });
      }
    });
  },

  /**
  * 转换提交状态
  */
  changeSubmitState: function (that) {
    that.setData({
      isSubmitted: !that.data.isSubmitted,
      btn_submit: {
        disabled: !that.data.btn_submit.disabled,
        plain: !that.data.btn_submit.plain,
        loading: !that.btn_submit.data.loading,
      }
    });
  },

  /**
  * submit后上传报到信息
  */
  baodao: function (e) {
    if (this.data.isSubmitted == false) {
      this.changeSubmitState(this); // 防止用户重复点击提交按钮

      wx.showLoading({
        title: '处理中...',
        mask: false,
      });
      // console.log(e.detail.value);
      var isSend = true;
      if (e.detail.value.nickname == '') {
        this.setData({
          showmiss_nickname: true,
        });
        isSend = false;
      }
      if (this.data.gender == null) {
        this.setData({
          showmiss_gender: true,
        });
        isSend = false;
      }
      if (this.data.eggday == null) {
        this.setData({
          showmiss_eggday: true,
        });
        isSend = false;
      }
      if (e.detail.value.grade == '') {
        this.setData({
          showmiss_grade: true,
        });
        isSend = false;
      }
      if (e.detail.value.major == '') {
        this.setData({
          showmiss_major: true,
        });
        isSend = false;
      }
      if (e.detail.value.height == '') {
        this.setData({
          showmiss_height: true,
        });
        isSend = false;
      }
      if (e.detail.value.hometown == '') {
        this.setData({
          showmiss_hometown: true,
        });
        isSend = false;
      }
      if (e.detail.value.wechat_id == '') {
        this.setData({
          showmiss_wechat_id: true,
        });
        isSend = false;
      }
      if (e.detail.value.QQ == '') {
        this.setData({
          showmiss_QQ: true,
        });
        isSend = false;
      }
      if (e.detail.value.danceLevel == '') {
        this.setData({
          showmiss_danceLevel: true,
        });
        isSend = false;
      }
      if (this.data.knowdancefrom == null && e.detail.value.knowfromElse == '') {
        this.setData({
          showmiss_knowdancefrom: true,
        });
        isSend = false;
      }
      if (e.detail.value.selfIntro == '') {
        this.setData({
          showmiss_selfIntro: true,
        });
        isSend = false;
      }
      if (this.data.photo_items == null) {
        this.setData({
          showmiss_photo: true,
        });
        isSend = false;
      }

      if (!isSend) {
        wx.hideLoading();
        wx.showToast({
          title: '请完善表格再提交~',
          image: '../../images/smiley-6_64.png',
          duration: 1500,
          mask: false,
        });
      } else {
        var formId = e.detail.formId;
        var that = this;
        var knowdancefrom = this.data.knowdancefrom;
        if (knowdancefrom != null) {
          knowdancefrom.push(e.detail.value.knowfromElse);
          knowdancefrom = knowdancefrom.toString();
        } else {
          knowdancefrom = e.detail.value.knowfromElse;
        }

        wx.login({
          success: function (res) {
            console.log("login success");
            wx.uploadFile({
              url: app.global_data.server_url + 'php/wx_baodao.php',
              filePath: that.data.photo_items[0],
              name: 'photo',
              formData: {
                'formId': formId,
                'code': res.code,
                'nickname': e.detail.value.nickname,
                'gender': that.data.gender,
                'eggday': that.data.eggday,
                'grade': e.detail.value.grade,
                'major': e.detail.value.major,
                'height': e.detail.value.height,
                'hometown': e.detail.value.hometown,
                'wechat_id': e.detail.value.wechat_id,
                'QQ': e.detail.value.QQ,
                'contact': e.detail.value.contact,
                'danceLevel': e.detail.value.danceLevel,
                'knowdancefrom': knowdancefrom,
                'selfIntro': e.detail.value.selfIntro,
              },
              success: function (res) {
                // 上传成功，返回数据res
                wx.hideLoading();
                if (res.header) { // ????????????????此处原为res.header.errMsg != 0，好像是uploadfile的bug
                  console.log(res.header.errMsg);
                  wx.showToast({
                    title: '报到失败！请点击重试！',
                    image: '../../images/more.png',
                    duration: 1500,
                    mask: false,
                  });
                } else { // 报到成功
                  console.log("upload success");
                  app.global_data.userInfo = res.data;
                  wx.showToast({
                    title: '报到成功！欢迎加入dance大家庭！',
                    image: '../../images/dance1-200.png',
                    duration: 1500,
                    mask: false,
                  });
                  that.changeSubmitState(that); // 可以再次提交了
                  setTimeout(function () {
                    wx.navigateTo({
                      url: '../dancers/dancers',
                    });
                  }, 1500);
                }
              },
              fail: function () {
                wx.hideLoading(); // 网络错误
                wx.showToast({
                  title: '报到失败！请点击重试！',
                  image: '../../images/more.png',
                  duration: 1500,
                  mask: false,
                });
                that.changeSubmitState(that); // 可以再次提交了
              }
            });
          },
          fail: function () { // 获取微信code失败
            wx.hideLoading();
            wx.showToast({
              title: '报到失败！请点击重试！',
              image: '../../images/more.png',
              duration: 1500,
              mask: false,
            });
            that.changeSubmitState(that); // 可以再次提交了
          }
        });
      }
    } else { // 用户重复点击提交按钮
      wx.showToast({
        title: '我在努力提交啦，请不要着急~',
        image: '../../images/smiley-6_64.png',
        duration: 1500,
        mask: false,
      });
    }
  },

  /**
  * 跳转到dancer列表页
  */
  toDancersPage: function () {
    wx.navigateTo({
      url: '../dancers/dancers',
    });
  },
})