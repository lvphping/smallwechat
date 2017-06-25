//aboutme.js
//获取应用实例
var app = getApp()
Page({
  data: {
      toastHidden:true,
     info: {}
  },

  onLoad: function (options) {
    var common = require('../../utils/common.js')
    common.loadInfo(options.id, this)
    common.addTimes(options.id)

  },
  closepage: function(){
      wx.navigateBack()
  },
  toastChange: function(){
    this.setData({toastHidden:true})
    wx.navigateBack()
  },  
})