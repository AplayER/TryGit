<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\ArticleModel;

class ArticleController extends Controller{
	
	public function addTest(){
		$articleInfo = array(
				array('articleTitle' => 'Test the database',
				'articleContent' =>'sbsfbsbsfb',
						'zanCounter'=> '1')
		);
		$addData= new ArticleModel();
        dump($addData->addArticle($articleInfo));
	}
	public function selectTest(){
		$articleID='1';
		$selectData = new ArticleModel();
		dump($selectData->selectArticle($articleID));
	}
}