//首页
const uploadFileUrl = require('../../config').uploadFileUrl

function getRandomColor () {
  const rgb = []
  for (let i = 0 ; i < 3; ++i){
    let color = Math.floor(Math.random() * 256).toString(16)
    color = color.length == 1 ? '0' + color : color
    rgb.push(color)
  }
  return '#' + rgb.join('')
}
var app = getApp()
Page({
  onReady: function (res) {
    this.videoContext = wx.createVideoContext('myVideo')
  },
  inputValue: '',
  data: {
    imageSrc:'',
    userInfo:'',
    networkType:'',
    src: '',
    danmuList:
      [{
        text: '好好看看哦',
        color: '#ff0000',
        time: 1
      },
      {
        text: '水军',
        color: '#ff00ff',
        time: 3
      }]
  },
 onLoad: function(){
    var that = this
    if(wx.getStorageSync('openid') == ''){
      app.getUserInfo(function (userInfo) {
        var nickName = userInfo.nickName
        that.setData({ username: nickName })
      })
    }
    var openid = wx.getStorageSync('openid');
    var common = require('../../utils/common.js')
    common.getUserInfoInfoing(this,openid)
    /* 获取地址*/
    wx.getLocation({
      type: 'wgs84',
      success: function(res) {
        var latitude = res.latitude
        var longitude = res.longitude
        var speed = res.speed
        var accuracy = res.accuracy
        console.log(res)
      }
    })
    /* 手机信息 */
    wx.getSystemInfo({
      success: function(res) {
          console.log(res)
      }
    })
    /* */
    wx.getNetworkType({
      success: function(res) {
        // 返回网络类型, 有效值：
        // wifi/2g/3g/4g/unknown(Android下不常见的网络类型)/none(无网络)
        var networkType = res.networkType
        that.setData({networkType:networkType})
      }
    })
 },
  bindInputBlur: function(e) {
    this.inputValue = e.detail.value
  },
  bindButtonTap: function() {
    var that = this
    wx.chooseVideo({
        sourceType: ['album', 'camera'],
        maxDuration: 60,
        camera: ['front','back'],
        success: function(res) {
            that.setData({
                src: res.tempFilePath
            })
        }
    })
  },
  bindSendDanmu: function () {
    this.videoContext.sendDanmu({
      text: this.inputValue,
      color: getRandomColor()
    })
  },
  videoErrorCallback: function(e) {
    console.log('视频错误信息:')
    console.log(e.detail.errMsg)
  },
  chooseImage: function() {
    console.log('aaaa')
    var self = this
    wx.chooseImage({
      count: 1,
      sizeType: ['compressed'],
      sourceType: ['album'],
      success: function(res) {
        console.log('chooseImage success, temp path is', res.tempFilePaths[0])

        var imageSrc = res.tempFilePaths[0]

        wx.uploadFile({
          url: uploadFileUrl,
          filePath: imageSrc,
          name: 'data',
          success: function(res) {
            console.log('uploadImage success, res is:', res)

            wx.showToast({
              title: '上传成功',
              icon: 'success',
              duration: 1000
            })

            self.setData({
              imageSrc
            })
          },
          fail: function({errMsg}) {
            console.log('uploadImage fail, errMsg is', errMsg)
          }
        })

      },
      fail: function({errMsg}) {
        console.log('chooseImage fail, err is', errMsg)
      }
    })
  }
 
})