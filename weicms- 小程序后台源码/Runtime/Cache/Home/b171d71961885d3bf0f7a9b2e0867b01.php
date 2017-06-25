<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
<meta content="<?php echo C('WEB_SITE_KEYWORD');?>" name="keywords"/>
<meta content="<?php echo C('WEB_SITE_DESCRIPTION');?>" name="description"/>
<link rel="shortcut icon" href="<?php echo SITE_URL;?>/favicon.ico">
<title><?php echo empty($page_title) ? C('WEB_SITE_TITLE') : $page_title; ?></title>
<link href="/weicms/Public/static/font-awesome/css/font-awesome.min.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weicms/Public/Home/css/base.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weicms/Public/Home/css/module.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weicms/Public/Home/css/weiphp.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weicms/Public/static/emoji.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="/weicms/Public/static/bootstrap/js/html5shiv.js?v=<?php echo SITE_VERSION;?>"></script>
<![endif]-->

<!--[if lt IE 9]>
<script type="text/javascript" src="/weicms/Public/static/jquery-1.10.2.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
<script type="text/javascript" src="/weicms/Public/static/jquery-2.0.3.min.js"></script>
<!--<![endif]-->
<script type="text/javascript" src="/weicms/Public/static/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/weicms/Public/static/uploadify/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="/weicms/Public/static/zclip/ZeroClipboard.min.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weicms/Public/Home/js/dialog.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weicms/Public/Home/js/admin_common.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weicms/Public/Home/js/admin_image.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weicms/Public/static/masonry/masonry.pkgd.min.js"></script>
<script type="text/javascript" src="/weicms/Public/static/jquery.dragsort-0.5.2.min.js"></script> 
<script type="text/javascript">
var  IMG_PATH = "/weicms/Public/Home/images";
var  STATIC = "/weicms/Public/static";
var  ROOT = "/weicms";
var  UPLOAD_PICTURE = "<?php echo U('home/File/uploadPicture',array('session_id'=>session_id()));?>";
var  UPLOAD_FILE = "<?php echo U('File/upload',array('session_id'=>session_id()));?>";
var  UPLOAD_DIALOG_URL = "<?php echo U('home/File/uploadDialog',array('session_id'=>session_id()));?>";
</script>
<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
<?php echo hook('pageHeader');?>

</head>
<body id="login_body">
	
	<!-- 主体 -->
	
<script type="text/javascript" src="/weicms/Public/static/qrcode/qrcode.js"></script>
<script type="text/javascript" src="/weicms/Public/static/qrcode/jquery.qrcode.js"></script>
<!-- 头部 -->
<div class="login_header">
	
    <div class="log_wrap">
        <a href="/" title="WeiPHP"><img class="logo" src="/weicms/Public/Home/images/logo.png"/></a>
    </div>
    
</div>
<!-- 介绍 -->

    	<div class="top_content">
        	<div class="log_wrap">
            	<div class="top_content_r">
                	<img src="/weicms/Public/Home/images/about/img/banner_pic.png?20150723"/>
                </div>
            	<section class="login_box">
                  <form class="login-form" action="/weicms/index.php?s=/Home/User/login/from/1.html" method="post">
                          <div class="form_body" id="input_login" style="display:<?php if(C('SCAN_LOGIN')) echo 'none'; ?> ">
                                <h6>欢迎使用WeiPHP!</h6>
                                <div class="input_panel">
                                  <div class="control-group">
                                    <label class="control-label" for="inputEmail">用户名</label>
                                    <div class="controls">
                                      <span class="fa fa-user"></span>
                                      <input type="text" id="inputEmail" class="span3" placeholder="请输入用户名"  ajaxurl="/user/checkUserNameUnique.html" errormsg="请填写1-16位用户名" nullmsg="请填写用户名" datatype="*1-16" value="" name="username">
										
                                    </div>
                                  </div>
                                  <div class="control-group">
                                    <label class="control-label" for="inputPassword">密码</label>
                                    <div class="controls">
                                      <span class="fa fa-key"></span>
                                      <input type="password" id="inputPassword"  class="span3" placeholder="请输入密码"  errormsg="密码为6-20位" nullmsg="请填写密码" datatype="*6-20" name="password">
                                    </div>
                                  </div>
                                  <?php if(C('WEB_SITE_VERIFY')) { ?>
                                  <div class="control-group">
                                    <label class="control-label" for="inputPassword">验证码</label>
                                    <div class="controls">
                                       <span class="fa fa-keyboard-o"></span>
                                      <input type="text" id="verify" class="span3" placeholder="请输入验证码"  errormsg="请填写5位验证码" nullmsg="请填写验证码" datatype="*5-5" name="verify">
                                      <a href="javascript:;" class="reloadverify_a">换一张?</a>
                                    </div>
                                  </div>
                                  
                                  <?php } ?>
                               </div>
                              <?php if(C('WEB_SITE_VERIFY')) { ?>
                              
                              <div class="control-group">
                                <label class="control-label"></label>
                                <div class="controls" style="margin-top:10px">
                                    <img class="verifyimg reloadverify" alt="点击切换" src="<?php echo U('verify');?>" style="cursor:pointer;">
                                </div>
                              </div>
                              <?php } ?>
                              <div class="controls Validform_checktip text-warning"></div>
                              <div class="control-group">
                                <div class="controls">
                                 <input type="checkbox" id="checkbox"/><label for="checkbox">自动登录</label>
                                </div>
                                <div class="controls">
                                  <button type="submit" class="btn btn-large">登 录</button>
                                 </div>
                                 <?php if(C('USER_ALLOW_REGISTER')) { ?>
                                 <div class="controls">
                                 还没账号?
                                 </div>
                                 <div class="controls">
                                  <a style="width:280px;" class="btn border-btn-main btn-large" href="<?php echo U('User/register');?>">立 即 注 册</a>
                                 </div>
                                 <?php } ?>
                              </div>
                          </div>
                          <?php if(C('SCAN_LOGIN')) { ?>
                          <div class="form_body" id='scan_login'>
                                <h6>请用微信扫码登录</h6>
                                <div class="input_panel">
                      <div id="qrCode"><img src="<?php echo ($qrcode); ?>" /></div>
                      <p class="qr_time_tips">如未关注公众号，先关注即可登录</p>
                               </div>
                               <div class="o_p"><a href="javascript:void(0)" onclick="$('#input_login').show();$('#scan_login').hide();">原注册用户登录入口</a></div>
                              </div>
                          <?php } ?>
                       </form> 
                </section>
            </div>
        </div>
        
    	

	<!-- /主体 -->

	<!-- 底部 -->
	<div class="wrap bottom" style="background:none">
    <p class="copyright">本系统由<a href="http://weiphp.cn" target="_blank">WeiPHP</a>强力驱动</p>
</div>

	<!-- /底部 -->
    
	<script type="text/javascript">       
    	$(document)		   
	    	.ajaxStart(function(){
	    		$("button:submit").addClass("log-in").attr("disabled", true);
	    	})
	    	.ajaxStop(function(){
	    		$("button:submit").removeClass("log-in").attr("disabled", false);
	    	});


    	$("form").submit(function(){
    		var self = $(this);
    		$.post(self.attr("action"), self.serialize(), success, "json");
    		return false;

    		function success(data){
    			if(data.status){
    				window.location.href = data.url;
    			} else {
    				self.find(".Validform_checktip").text(data.info);
    				//刷新验证码
    				$(".reloadverify").click();
    			}
    		}
    	});

		$(function(){
			var verifyimg = $(".verifyimg").attr("src");
            $(".reloadverify,.reloadverify_a").click(function(){
                if( verifyimg.indexOf('?')>0){
                    $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
                }else{
                    $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
                }
            });
			$('input').focus(function(){
				$(this).parent().find('.fa').css('color','#44b549');
				})
			$('input').blur(function(){
				$(this).parent().find('.fa').css('color','#aaa');
				})
		});
		
<?php if(C('SCAN_LOGIN')) { ?>		
	setInterval(function(){
		$.post("<?php echo U('login');?>",{},function(res){
			if(res==1){
				window.location.href = "<?php echo U('home/index/main');?>";
			}
		});
	},3000);
<?php } ?>

	$('#login_body').height($(window).height());	
	</script>

</body>
</html>