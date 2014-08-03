// JavaScript Document
<script language="javascript">
     var bg="#000";
    function mysize(){
	
		 var w=window.innerWidth;
		 var b=document.getElementById("box"); 
		
		 if(w<="1200"){
			  b.style.width="1200px";
			
			 }else{
				 b.style.width="100%";
				 }
	}
    window.onresize=mysize;
	
    function moveStyle(t,i){
	    var b=document.getElementById("xx1"); 
		  if(i==1){
			  t.style.borderStyle="outset";
		      t.style.borderWidth="1px";
		  }else {
			  t.style.border="none";
		  }
	}
	 function moveStyle2(t,i,n){
		 var str="gn"+""+n+""+""+n+"";
		 var s="gn"+""+n+"";
		 var c=document.getElementById(s); 
	     var b=document.getElementById(str); 
		  if(i==1){
			  b.style.color="#F06";
			  c.style.borderStyle="outset";
			  c.style.borderBottomWidth="1";
			  c.style.backgroundColor="#DDD";
		  }else {
			  b.style.color="#CCC";
			  c.style.border="none";
			  c.style.backgroundColor="";
		  }
	}
	function moveStyle3(t,i,n){
		 var s="zx"+""+n+"";
		 var ss="xz"+""+n+"";
	     var b=document.getElementById(s); 
		 var c=document.getElementById(ss); 
		  if(i==1){
			  b.style.color="#F06";
			  c.style.borderStyle="outset";
			  c.style.borderBottomWidth="1";
			  c.style.backgroundColor="#DDD";
		  }else {
			  b.style.color="#CCC";
			  c.style.border="none";
			  c.style.backgroundColor="";
		  }
	}
</script>