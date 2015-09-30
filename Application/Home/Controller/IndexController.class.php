<?php
namespace Home\Controller;
use Home\Model\ArticleModel;

use Think\Controller;
use Common\Common\Constant;
use Think\Model;
class IndexController extends Controller {
    public function index(){
    	$this->display('Index:index');
    }
    public function testForm(){
        dump($_POST);
    }
    public function testRedis(){
    	error_reporting(0);
    	//require 'ThinkPHP\Library\Think\Cache\Driver\Redis.class.php';
    	$test = new ArticleModel();
    	$test->insert2Redis();  	
    }
    public function testExcel() {
        error_reporting(0);
        $filePath = Constant::WEBSERVER_EXCEL_SAVEPATH.'test.xls';
//         $dataList = array(array('school'=>'计算机学院','sno'=>'1',),
//         		array('school'=>'计算机学院','sno'=>'2',));
        $dataList[]=$_POST;
//         $headerList = array('emailAddress','website');
        foreach ($_POST as $val){
        	$headerList[]=key($_POST);
        	next($_POST);
        }
        $OutputFileName ='test1';
        $this->exportToExcelWithHeader($filePath,$dataList,$headerList,$OutputFileName);
    }
/**
	 * 
	*函数名：		exportToExcelWithHeader
	*输入：		模板excel的路径 $filePath，例如 'C:\\123.xls'
	*输入：		二维数组  要导出的内容 $dataList
	* 			array(
	*				array(key1=>$value1,key2=>$value2,...),
	*				array(key1=>$value1,key2=>$value2,...),...
	*			)
	*输入：		一维数组  生成excel的头部信息 $headerList 
	*			array(数据库前台字段key1，数据库前台字段key2,...)
	*输入：		string $OutputFileName 输出文件名(建议输出文件名带时间戳)
	*输入：		$outputType 默认0输出到浏览器，输入其他保存到文件服务器并成功返回相对地址，失败返回NULL
	 */
	public function exportToExcelWithHeader($filePath,$dataList,$headerList,$OutputFileName,$outputType=0){
		require './ThinkPHP/Library/Org/PHPExcel/PHPExcel.php';		
		//实例化Excel读取类
		$PHPReader = new \PHPExcel_Reader_Excel2007();
// 		$fileWebService=new FileWebService($this->userID);
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new \PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				return false;
			}
		}
		//读取Excel
		$PHPExcel = $PHPReader->load($filePath);
		/**读取excel文件中的第一个工作表*/
		$currentSheet = $PHPExcel->getSheet(0);
		/**取得一共有多少行*/
		$allRow = $currentSheet->getHighestRow();
// 		$cell = $currentSheet->getCell('A'.$allRow)->getValue();
// 		while($cell==null&&$allRow>=0){
// 			$allRow--;
// 			$cell = $currentSheet->getCell('A'.$allRow)->getValue();
// 		}
		
		$allRow++;
		//实例化Excel写入类
		$PHPWriter = new \PHPExcel_Writer_Excel5($PHPExcel);
		
		//遍历数据列表中的内容,从第allRow行开始
		$i=$allRow;
		foreach ($dataList as  $key=>$value){
			$position='A';
			foreach ($headerList as $headerKey=>$headerValue){
				$currentSheet->setCellValue("$position".$i, $dataList[$key][$headerValue]);  //headerKey为数据库前台字段
				$position++;
			}
			$i++;
		}
		ob_end_clean();//清除缓冲区,避免乱码
		//决定是导出到浏览器还是文件服务器
		if($outputType!==0){
// 			$path=Constant::WEBSERVER_TMPFILE_SAVEPATH."$OutputFileName.xls";
// 			$PHPWriter->save($path);
// 			// 					dump($path);
// 			// 					dump(file_exists($path));
// 			$fileURL=$fileWebService->uploadFile($path, "$OutputFileName.xls");
// 			if($fileURL==false){
// 				$fileURL=null;
// 			}
// 			return $fileURL;
             return false;
		}else{
			header('Content-Type: application/vnd.ms-excel');
			header("Content-Disposition: attachment;filename=$OutputFileName.xls");
			header('Cache-Control: max-age=0');
			$PHPWriter->save('php://output');
		}
	}
}