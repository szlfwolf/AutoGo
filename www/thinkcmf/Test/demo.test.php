<?php

require_once('simpletest/autorun.php');


class demoTest extends UnitTestCase {
 function test_pass(){
 	

 	$boolean = false;
 	$this->assertFalse($boolean);
 }
}
?>