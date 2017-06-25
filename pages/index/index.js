//首页
Page({
  data: {
    imgUrls: [
      'http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg',
      'http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg',
      'http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg'
    ],
    indicatorDots: false,
    autoplay: true,
    interval: 5000,
    duration: 1000,
    hidden: false,
    url:'../../pages/lists/lists'
  },
  onShow: function(){
      var that = this;
      setTimeout(function(){
        that.setData({
            hidden: true
        });
      }, 500);
  },
  onLoad: function(){
    var common = require('../../utils/common.js')
    common.getLastData(this)
  },
  onTouch: function(event){
     wx.navigateTo({
       url: '../lists/lists'
     })
  }
})