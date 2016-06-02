<?php

namespace Zwd\Controller;
use Common\Controller\HomebaseController;
class GoodsController extends HomebaseController{
    public function index(){    	
	  $this->display(":goods");
    }
}