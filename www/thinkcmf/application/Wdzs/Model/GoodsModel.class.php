<?php
namespace Home\Model;
use Think\Model;
class GoodsModel extends Model {
		
	protected $connection 	= 'mysql://root@localhost:3306/wd_cloth2';
	protected $dbName 		= 'wd_cloth2';
	protected $tablePrefix 	= 's_';
	protected $tableName 	= 'goodsinfo';   	
	
	
	public function __construct() {
        //$this->db(1,"mysql://root@localhost:3306/wd_cloth2");
    }

	
	protected $_auto = array (
		array ('post_date', 'mGetDate', 1, 'callback' ), 	// 增加的时候调用回调函数
		//array ('post_modified', 'mGetDate', 2, 'callback' ) 
	);
	// 获取当前时间
	function mGetDate() {
		return date ( 'Y-m-d H:i:s' );
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}
}