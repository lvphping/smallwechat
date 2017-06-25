// map.js
Page({
  data: {
    latitude:0,
    longitude: 0,
  },
  onLoad:function(){
    var that = this;  
    wx.getLocation({
        type: 'gcj02',
        success: function(res) {
            var latitude = res.latitude
            var longitude = res.longitude
            that.setData({
                latitude:latitude,
                longitude:longitude,
                markers:[{
                    id: 0,
                    iconPath: "/images/position.png",
                    longitude: longitude,
                    latitude: latitude,
                    width: 30,
                    height: 30
                }]
             })
        }
    })
  }
})