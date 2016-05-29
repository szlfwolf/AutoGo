<?php

namespace Zwd\Controller;
use Common\Controller\HomebaseController;
class IndexController extends HomebaseController{
    public function index(){
      echo "this is zwd index !";
	  $this->display(":index");
    }
}