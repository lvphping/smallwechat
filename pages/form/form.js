var app = getApp()
Page({
  data: {
    array: ['美国', '中国', '巴西', '日本'],
    area: 0,
    score: 0,
    username: '',
    content:'',
    toastHidden:true,
    msg:'错误',
    loadHidden:true,
    loadmsg:'加载中'
  },
  bindPickerChange: function (e) {
    this.setData({ area: e.detail.value });
  },
  bindSliderChange: function (e) {
    this.setData({ score: e.detail.value });
  },
  formSubmit: function (e) {
    this.setData({loadHidden:false,loadmsg:'数据提交中'})
    var formData = e.detail.value
    formData.area = this.data.area
    formData.score = this.data.score
    formData.username = this.data.username
    var that = this
    if(formData.content.length < 4){
      that.setData({msg:'留言内容不得少于四个字',toastHidden:false})
      this.setData({loadHidden:true})
      return false;
    }
  
    wx.request({
      url: app.url + 'addon/Feedback/Feedback/addFeedback',
      data: formData,
      header: {
        'Content-Type': 'application/json'
      },
      success: function (res) {
            console.log(res)
            that.setData({msg:res.data.info,toastHidden:false})
            that.setData({loadHidden:true})
      },
      complete: function () {
            that.setData({loadHidden:true})
      }
    })
  },
  onLoad: function () {
    var that = this
    app.getUserInfo(function (userInfo) {
        var nickName = userInfo.nickName
        that.setData({ username: nickName })
    })
  },
  toastChange: function(){
    this.setData({toastHidden:true})
  },
  loadChange: function(){
    this.setData({loadHidden:true,loadmsg:'数据提交中'})
  }
})