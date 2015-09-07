<?php
namespace Home\Controller;
use Home\Model\ArticleModel;

use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	echo 'nihao';
    }
    public function testRedis(){
    	error_reporting(0);
    	//require 'ThinkPHP\Library\Think\Cache\Driver\Redis.class.php';
    	$test = new ArticleModel();
    	$test->insert2Redis();  	
    }
}