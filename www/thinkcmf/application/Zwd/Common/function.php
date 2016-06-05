<?php
	
	function show404()
	{
		   	header('HTTP/1.1 404 Not Found');
    	    header('Status:404 Not Found');
    	    if(sp_template_file_exists(MODULE_NAME."/404")){
    	        return TRUE;
    	    }
			return false;
	}