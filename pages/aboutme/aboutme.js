//aboutme.js
//获取应用实例
var app = getApp()
Page({
  data: {
    img: '../../images/logo.png',
    title: "LvApp",
    intro: "",
    contab: "关于我们",
    address: "南京市栖霞区马群街道宁康苑",
    mobile: "18652337494",
    email: "Lv_Php@163.com"
  },
  //事件处理函数
  bindViewTap: function () {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  onLoad: function () {
    wx.request({
      url: app.url + 'addon/Cms/Cms/testLogin',
      data: { PHPSESSID: wx.getStorageSync('PHPSESSID') },
      success: function (res) {
        console.log(res);
      }
    })
  },
  callme: function () {
    wx.makePhoneCall({
      phoneNumber:  this.data.mobile
    })
  }
})