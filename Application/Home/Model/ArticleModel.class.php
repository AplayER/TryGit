<?php
namespace Home\Model;
class ArticleModel extends BaseModel{
	/**
	 * 功能：覆盖父类对象，完成数据库字段和前台页面子弹的映射关系
	 */
	protected function initMap(){
		$this->fieldMapping = array(
			'WENZHANGID' => 'articleID',
			'WENZHANGBIAOTI' => 'articleTitle',
			'WENZHANGNEIRONG' => 'articleContent',
			'WENZHANGPINGLUN' => 'articleRemark',
			'BEILIULANCISHU' => 'browseCounter',
			'ZANSHU' => 'zanCounter',
			'CAISHU' => 'caiCounter'
				);
		}
	public function addArticle($artiInfo){
// 		$this->insertData = array(
// 				'WENZHANGBIAOTI' => 'articleTitle',
// 				'WENZHANGNEIRONG' => 'articleContent',
// 				//'WENZHANGPINGLUN' => 'articleRemark',
// 				//'BEILIULANCISHU' => 'browseCounter',
// 				//'ZANSHU' => 'zanCounter',
// 				//'CAISHU' => 'caiCounter'
// 				);
		$this->insertData = $artiInfo;
		return $this->insertRecord();
	}
	public function deleteArticle($articleID){
		$this->deleteCond = array(
				'WENZHANGID' => 'articleID');
		return $this->deleteRecord();
	}
	public function selectArticle($articleID){
		$this->selectCond = array(
				'WENZHANGID' => $articleID);
		return $this->selectRecord();
	} 
	public function updateArticle($articleInfo){
		$this->updateData = $articleInfo;
		$this->updateCond = array(
				'WENZHANGID' => $articleInfo['articleID']
				);
		return $this->updateRecord();
	}
	public function insert2Redis(){
		$redis = new \Redis();
		$redis->connect('127.0.0.1','6379');
		//     	$result=mysql_query($sql, $conn);
		//         $row=mysql_fetch_row($result);
		//         dump($row);
		      	$key = 'd1';
		    	$value = 'ddd';
		    	$redis->set($key,$value);
		    	dump($redis->get($key));
// 		$model = new ArticleModel();
// 		for($i=1;$i<=7;$i++){
// 			$data[$i-1]=$model->selectArticle($i);
// 			foreach ($data[$i-1] as $key=> $value){
// 				foreach ($value as $tkey => $tvalue){
// 					$redis->hset($i,$tkey,$tvalue);
// 					echo $redis->hGet($i,$tkey);
// 					echo '<p>'; }
// 			}
// 		}
		
	}
}
