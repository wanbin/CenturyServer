// JavaScript Document
var bg="#000";
    
    function moveStyle(t,i,n){     //n=1,2
	      if(i==1){
			  if(t.className=="xxbtn1"){ 
			       t.className="xxbtn1_1";
		      }else if(t.className=="xxbtn2"){
				  t.className="xxbtn2_1";  
			  }
		  }else if(i==0){
			  if(t.className=="xxbtn1_1"){ 
			       t.className="xxbtn1";
		      }else if(t.className=="xxbtn2_1"){
				  t.className="xxbtn2";  
			  }
		  }
	}	 
	function imgTr(t){
	      var b1=document.getElementById("cxx1");
		  var b2=document.getElementById("cxx2");
		  var b3=document.getElementById("cxx3");
		  b1.className="xxbtn2";
		  b2.className="xxbtn2";
		  b3.className="xxbtn2";
		  t.className="xxbtn1";
	}
	function imgxz(t,i){
		  var s="html/imges/touxiang.png";
		  var ss="html/imges/touxiang_1.png";
		  var x=document.getElementById("xzfont");
		  var n=document.getElementById("namefont");
		  if(i==1){
			   t.src=ss; 
			   x.style.fontSize="18px";
			   n.style.fontSize="18px";
		  }else {
			   t.src=s;  
			   x.style.fontSize="12px";
			   n.style.fontSize="12px";
		  }
	}	 	
	function aImg(t,i,n){
		  var s="html/imges/xingzuo/"+""+n+""+".png";
		  var ss="html/imges/xingzuo/"+""+n+""+"_"+""+n+""+".png";
		  if(i==1){ t.src=ss;
		  }else {t.src=s;  }
	}
	function aTd(t,i,n){
		 if(n==1){  if(i==1){ t.className="tuodong1_1"; }else { t.className="tuodong1"; }
		 }else{     if(i==1){ t.className="tuodong_1";  }else { t.className="tuodong";  }
		 }
	}	
	function aczxxTd(t,i,n){
		 if(n==1){  if(i==1){ t.className="czxx_tuodong1_1"; }else { t.className="czxx_tuodong1"; }
		 }else{     if(i==1){ t.className="czxx_tuodong_1";  }else { t.className="czxx_tuodong";  }
		 }
	}	
	function toRL(t,i,n){
		var s= "html/imges/shujikuang/toleft.png";
		var ss="html/imges/shujikuang/toleft_1.png";
		var s1= "html/imges/shujikuang/toright.png";
		var ss1="html/imges/shujikuang/toright_1.png";
		if(n==0){
		      if(i==1){	 t.src=ss; }else {	 t.src=s;  }
		}else{if(i==1){	 t.src=ss1; }else {	 t.src=s1;  }}
    }
	function toRL1(t,i){
		var s= "html/imges/shujikuang/toleft.png";
		var ss="html/imges/shujikuang/toleft_1.png";
		var s1= "html/imges/shujikuang/toright.png";
		var ss1="html/imges/shujikuang/toright_1.png";
		var l=document.getElementById("toleft");
		var r=document.getElementById("toright");
		if(i==1){	  l.src=ss; 	r.src=ss1; 
		}else {	      l.src=s;  	r.src=s1; 	}
    }
	function toczxxRL(t,i,n){
		var s= "html/imges/chuangzaoXX/czxxTL/toleft.png";
		var ss="html/imges/chuangzaoXX/czxxTL/toleft_1.png";
		var s1= "html/imges/chuangzaoXX/czxxTL/toright.png";
		var ss1="html/imges/chuangzaoXX/czxxTL/toright_1.png";
		if(n==0){
		      if(i==1){	 t.src=ss; }else {	 t.src=s;  }
		}else{if(i==1){	 t.src=ss1; }else {	 t.src=s1;  }}
    }
	function toczxxRL1(t,i){
		var s= "html/imges/chuangzaoXX/czxxTL/toleft.png";
		var ss="html/imges/chuangzaoXX/czxxTL/toleft_1.png";
		var s1= "html/imges/chuangzaoXX/czxxTL/toright.png";
		var ss1="html/imges/chuangzaoXX/czxxTL/toright_1.png";
		var l=document.getElementById("toczxx_left");
		var r=document.getElementById("toczxx_right");
		if(i==1){	  l.src=ss; 	r.src=ss1; 
		}else {	      l.src=s;  	r.src=s1; 	}
    }
	function ssfocus(t){	t.value="";  	}
	function ssblur(t){
		if(t.value==""){	t.value="搜索你喜欢的";	}
	}
	function mysize(){	
		 var w=window.innerWidth;
		 var b=document.getElementById("box"); 	
		 var nw=Number(window.innerWidth); 

		 var l=document.getElementById("top_up_left"); 	
		 var c=document.getElementById("top_up_center"); 	
		 var r=document.getElementById("top_up_right"); 
		 var dc=document.getElementById("top_down_center"); 
		 var dr=document.getElementById("top_down_right"); 
		 var xxbox=document.getElementById("xxbox");
		 //var main=document.getElementById("main");
		 //var ftcon=document.getElementById("ftcon");
		 var czxx=document.getElementById("czxx");
		 var pl=document.getElementById("pinglun");
		 //var home_edit_xq_k=document.getElementById("home_edit_xq_k");
		 
		  
	     var n=(nw-356)/2;	
		 var n1=nw-n;
		 var winW=1200;
		 var n3=(winW-356)/2;
		 var n4=winW-n3;
		 
		 var n5=(nw-510)/2;	
		 var n6=nw-n5;
		 var n7=(winW-510)/2;
		 var n8=winW-n7;
		 var n10 = (nw-600)/2;
		 var n9 = (winW-600)/2;
		 if(w<=winW){
			  b.style.width="1200px";	
			  c.style.left=""+n3+""+"px";
			  l.style.right=""+n4+""+"px";
			  r.style.left=""+n4+""+"px";
			  
			  dc.style.left=""+n7+""+"px";
			  dr.style.left=""+n8+""+"px";	
			  
			  //home_edit_xq_k.style.left = ""+n9+""+"px";	
			  xxbox.style.left="0px";
			  
			 // main.style.left="0px";
			  
			  //ftcon.style.left="0px";
			  czxx.style.left=""+(50)/2+""+"px";
			  pl.style.left=""+(winW-1000)/2+""+"px";
		 }else if(w >winW){
			  b.style.width="100%";
			  c.style.left=""+n-4+""+"px";
			  l.style.right=""+n1+""+"px";
			  r.style.left=""+n1+""+"px";
			  
			  dc.style.left=""+n5+""+"px";
				//home_edit_xq_k.style.left =""+n10+""+"px";

			  dr.style.left=""+n6+""+"px";	
			  
			  xxbox.style.left=""+(nw-winW)/2+""+"px";
			  
			  //main.style.left=""+(nw-winW)/2+""+"px";
			  
			  //ftcon.style.left=""+(nw-winW)/2+""+"px";
			  czxx.style.left=""+(nw-winW+50)/2+""+"px";
     		  pl.style.left=""+(nw-1000)/2+""+"px";
		 }
	}
	function xx_msg(t,i){
		 var n=document.getElementById(t);
		 var pNode=n.parentNode;
		 /* var a=document.getElementById("xx_appbig");
		 var nodediv=this.lastChild;*/
	     if(i==1){	
		     n.style.display="block"; 
			 pNode.style.width="320px"
          }else if(i==0){	
		      n.style.display="none"; 
			 pNode.style.width="auto"
	     }
	}
	function showczxx(){
	    var cz=document.getElementById("czxx");
		var bc=document.getElementById("boxcover");
		cz.style.display="block";			
		bc.style.display="block";
	}
	function czxxExit(){
	    var cz=document.getElementById("czxx");	
		var bc=document.getElementById("boxcover");
		 cz.style.display="none";	
		 bc.style.display="none";
	}
	function czxxExitStyle(t,i){
		 var s="html/imges/chuangzaoXX/czxx_exit_1.png";
		 var s1="html/imges/chuangzaoXX/czxx_exit.png";
	     if(i==1){         t.src=s;}
		 else if(i==0){    t.src=s1;}
	}
	function czxxStyle(t,i,s){
		 var overS=s+".png";
		 var outS=s+"_1.png";
		 if(i==1){         t.src=outS;}
		 else if(i==0){    t.src=overS;}
    }
	/////////////////////////////////////////////////
	function showPL(){
	    var cz=document.getElementById("pinglun");
		var bc=document.getElementById("boxcover");
		cz.style.display="block";			
		bc.style.display="block";
	}
	function exitPL(){
	    var cz=document.getElementById("pinglun");	
		var bc=document.getElementById("boxcover");
		 cz.style.display="none";	
		 bc.style.display="none";
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	