var url = null;

function getapi(url,pdata)
{	
	
	$.post(url,pdata,function(data,status){
		switch (status){
			case "success":
			
				$("#jsonmsg").html(JSON.stringify(data));
				
				break;			
			default:
				alert(status);
				break;
		}
		
	})
}

function loadInfo(url,fname) {	
	caturl = url+"&fn="+fname;
	$("#jsonmsg").html("");//清空info内容\
    $.getJSON(caturl, function(data) {
        $("#jsonmsg").html("");//清空info内容\        
        $.each(data, function(key, value) {
        	$("#jsonmsg").append(key+"----"+value+"<br/><hr/>");   
        	
        	if( Object.prototype.toString.call( value ) === '[object Array]' ) {
    			//alert( 'Array!' );
    			$("#jsonmsg").append("<url>");
    			
    			for(var w=0; w<value.length; w++) {    			  
    				$("#jsonmsg").append('<li><a onclick="loadInfo(\''+ url +"\',"+ value[w] +')")>'+url +","+ value[w] +'</a></li>');
    			}
    			
    			$("#jsonmsg").append("</url>");
			}
        });
        });
}