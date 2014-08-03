// JavaScript Document
$(function (){
	//
	//top的背景更换效果
	//
	var topbgNum=1;  //默认显示第一张图片
	var topbgCount=3;//背景图片数量
	$(".ctrbg").click(function(){
		    ++topbgNum;
			if(topbgNum>topbgCount){ topbgNum=1;}
			var n=-761*(topbgNum-1);
		    $(".top2").css("background-position","0 "+""+n+""+"px");
			$(this).css("color","red");
		}
	);
	//
	//页面切换效果
	//
	$(".ctrTab1").mouseover(function(){
		     $(".main_center").children().hide();
			 $(".main_tab1").show();
		}
	);
	$(".ctrTab2").mouseover(function(){
		     $(".main_center").children().hide();
			 $(".main_tab2").show();
		}
	);
	$(".ctrTab3").mouseover(function(){
		     $(".main_center").children().hide();
			 $(".main_tab3").show();
		}
	);
	//
	//功能连接效果【诙谐，搞笑，高端，邪恶......】
	//
	$(".top_yuan_up4_gn").find("a").mouseover(function(){
		$(this).css("background-image","url(imges/grzx/huixie_1.png)");
	});
	$(".top_yuan_up4_gn").find("a").mouseout(function(){
		$(this).css("background-image","url(imges/grzx/huixie.png)");
	});
	$(".top_yuan_up4_gn").find("a").mousedown(function(){
		$(this).css("background-image","url(imges/grzx/huixie_2.png)");
	});
	$(".top_yuan_up4_gn").find("a").mouseup(function(){
		$(this).css("background-image","url(imges/grzx/huixie_2.png)");
	});
	//
	//功能连接效果【音乐，视频，W...】
	//
	$(".top_yuan_up5_gn").find("img").mouseover(function (){
		    var ss=$(this).attr("src");
			var newurl=ss.replace(".png","_1.png");
			$(this).attr("src",newurl);	
	    }
	);
	$(".top_yuan_up5_gn").find("img").mouseout(function (){
		    var ss=$(this).attr("src");
			var newurl=ss.replace("_1.png",".png");
			$(this).attr("src",newurl);	
	    }
	);	
	//
	//测试加分
	//
	$(".main_left_up2").find("img").mouseover(function (){
		   $(this).addClass("scale");
	    }
	);
	$(".main_left_up2").find("img").mouseout(function (){
		   $(this).removeClass("scale");
	    }
	);
	$(".main_left_up1").find("img").mouseover(function (){
		   $(this).addClass("scale");
	    }
	);
	$(".main_left_up1").find("img").mouseout(function (){
		   $(this).removeClass("scale");
	    }
	);	
	//
	//关注
	//
	$(".main_right_up2").find("div>a").mouseover(function (){
		   //$(this).fadeTo(100,0.6);
		   $(this).find("div").css("display","block")
	    }
	);
	$(".main_right_up2").find("div>a").mouseout(function (){		   
		   $(this).find("div").css("display","none");
	    }
	);
	//	
	//	
	//
	$(".main_tab1_up1>table").find("img").hover(function(){
			 $(this).fadeTo(100,0.7);
		}
	);
	$(".main_tab1_up1>table").find("img").mouseout(function(){
		      $(this).fadeTo(100,1);
		}
	);	
	$(".main_tab1_up2>table").find("img").hover(function(){
			 $(this).fadeTo(100,0.7);
		}
	);
	$(".main_tab1_up2>table").find("img").mouseout(function(){
		      $(this).fadeTo(100,1);
		}
	);	
	//
	//功能连接效果【音乐，视频，W...】
	//
	$(".btn_xq1").mouseover(function (){
		    var ss=$(this).css("background-image");
			var newurl=ss.replace(".png","_1.png");
			$(this).css("background-image",newurl);
	    }
	);
	$(".btn_xq1").mouseout(function (){
		    var ss=$(this).css("background-image");
			var newurl=ss.replace("_1.png",".png");
			$(this).css("background-image",newurl);
	    }
	);	
	$(".btn_xq2").mouseover(function (){
		    var ss=$(this).css("background-image");
			var newurl=ss.replace(".png","_1.png");
			$(this).css("background-image",newurl);
	    }
	);
	$(".btn_xq2").mouseout(function (){
		    var ss=$(this).css("background-image");
			var newurl=ss.replace("_1.png",".png");
			$(this).css("background-image",newurl);
	    }
	);
	//
	//
	//
	$(".ctrinfo").click(function(){
		   var txt=$(this).text();		
		   if(txt=="编辑"){
			   $(this).text("保存");
			   $(this).parent().siblings().eq(0).hide();
			   $(this).parent().siblings().eq(1).show();
		   }else if(txt=="保存"){
			   $(this).text("编辑");
			   $(this).parent().siblings().eq(1).hide();
			   $(this).parent().siblings().eq(0).show();
		   }
		   
		}
	);
	$(".ctrinfo").mouseover(function(){
		    var ss=$(this).css("background-image");
			var newurl=ss.replace(".png","_1.png");
			$(this).css("background-image",newurl);
			$(this).css("color","#FFF");
		}
	);
	$(".ctrinfo").mouseout(function(){
		    var ss=$(this).css("background-image");
			var newurl=ss.replace("_1.png",".png");
			$(this).css("background-image",newurl);
			$(this).css("color","#000");
		}
	);
	$(".infomstx").click(function(){
		   var p=$(this).parents(".base_view");
		   var sib1=p.siblings(".infotop");
		   var sib2=p.siblings(".base_info");	
		   var txt=sib1.find("a").text();	
		   if(txt=="编辑"){
			   sib1.find("a").text("保存");
			   p.hide();
			   sib2.show();
		   }	   
		}
	);
	$(".base_view").find("img").click(function(){
		   var p=$(this).parents(".base_view");
		   var sib1=p.siblings(".infotop");
		   var sib2=p.siblings(".base_info");	
		   var txt=sib1.find("a").text();	
		   if(txt=="编辑"){
			   sib1.find("a").text("保存");
			   p.hide();
			   sib2.show();
		   }	   
		}
	);
	$(".base_view").find("img").mouseover(function(){
		    var ss=$(this).attr("src");
			var newurl=ss.replace(".png","_1.png");
		    $(this).attr("src",newurl);
		}
	);
	$(".base_view").find("img").mouseout(function(){
		    var ss=$(this).attr("src");
			var newurl=ss.replace("_1.png",".png");
		    $(this).attr("src",newurl);
		}
	);
	//
	//
	//
	$(".ssSelect").mouseover(function(){
	   $(this).find(".ssOption").css("display","block");
	});
	$(".ssSelect").mouseout(function(){
	   $(this).find(".ssOption").css("display","none");
	});
	/*$(".ssOption").mouseover(function(){
	   
	});
	$(".ssOption").mouseout(function(){
	   $(this).css("display","none");
	});*/
	$(".opt").click(function(){
	  // $(this).parent().css("display","none");
	   $(this).parent().siblings().find(".showOpt").text(""+$(this).text()+"");
	});
    $(".opt").mouseover(function(){
	   $(this).css("background-color","#CCC");
	});
    $(".opt").mouseout(function(){
	   $(this).css("background-color","#FFF");
	});  
	
	
	
	
	
	
	
	
		
		
		
});