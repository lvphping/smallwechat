//lists.js
//获取应用实例
var app = getApp()
Page({
  data: {
    newsList: [],
    lastID: 0
  },


  loadData(lastID) {
    var limit = 5
    var that = this

    wx.request({
      url: app.url + 'addon/Cms/Cms/getList',
      data: { limit: limit, lastID: lastID },
      header: {
        'Content-Type': 'application/json'
      },
      success: function (res) {
        if (!res.data) return false;
        var len = res.data.length;


        that.setData({
          lastID: res.data[len - 1].id
        })

        var dataArr = that.data.newsList
        var dataLen = dataArr.length
        if (dataLen > 0) {
          newData = dataArr.concat(res.data);
        } else {
          var newData = res.data
        }

        that.setData({
          newsList: newData
        })
      }
    })
  },

  loadMore: function (event) {
    var lastID = event.currentTarget.dataset.lastid
    this.loadData(lastID)
  },
  onLoad: function () {
    var that = this
    var lastID = 0

    this.loadData(0)
  }
})