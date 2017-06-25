var app = getApp()
function loadInfo(id, obj){
    var key = 'info_' + id
    var info = wx.getStorageSync(key)
    if(info){
        console.log('data from localCache')
        obj.setData({ info:info })
        return true
    }

    wx.request({
        url: app.url+'addon/Cms/Cms/getDetail',
        data: {id:id},
        header: {
            'Content-Type': 'application/json'
        },
        success: function(res) {
                obj.setData({ info:res.data })
                
               // console.log(key)
                //wx.setStorageSync(key, res.data)
                //console.log('data from server')
        },
        fail: function(res){
            console.log('server error')       
            obj.setData({ toastHidden:false, msg:'当前网格异常，请稍后再试' }) 
        },        
    })    
}
function addTimes(id){
     wx.request({
        url: app.url+'addon/Cms/Cms/addTimes',
        data: {id:id},
        header: {
            'Content-Type': 'application/json'
        },
        success: function(res) {
            if(res.status == 1){
                console.log('浏览次数+1') 
            }else{
                 console.log('失败') 
            }
        },
        fail: function(res){
            console.log('server error')       
            obj.setData({ toastHidden:false, msg:'当前网格异常，请稍后再试' }) 
        },        
    }) 
}
function getLastData(obj){
    wx.request({
        url: app.url+'addon/Cms/Cms/getLastData',
        data: '',
        header: {
            'Content-Type': 'application/json'
        },
        success: function(res) {
             obj.setData({ info:res.data })
        },
        fail: function(res){
            console.log('server error')       
            obj.setData({ toastHidden:false, msg:'当前网格异常，请稍后再试' }) 
        },        
    }) 
}
function getUserInfoInfoing(obj,openid){
    wx.request({
        url: app.url+'addon/Cms/Cms/getUserInfoing',
        data: {data:openid},
        header: {
            'Content-Type': 'application/json'
        },
        success: function(res) {
             console.log('会员数据'+res.data)
             obj.setData({ userInfo:res.data })
        },
        fail: function(res){      
            obj.setData({ toastHidden:false, msg:'当前网格异常，请稍后再试' }) 
        },        
    })
  }
module.exports = {
  loadInfo: loadInfo,
  addTimes: addTimes,
  getLastData:getLastData,
  getUserInfoInfoing:getUserInfoInfoing
 
}