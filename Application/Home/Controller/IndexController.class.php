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
    public function testRedis(){
    	error_reporting(0);
    	//require 'ThinkPHP\Library\Think\Cache\Driver\Redis.class.php';
    	$test = new ArticleModel();
    	$test->insert2Redis();  	
    }
    /**
     *
     *函数名：	testExcel
     *输入：		无
     *输出：		$outputType 默认0输出到浏览器，输入其他保存到文件服务器并成功返回相对地址，失败返回NULL
     *创建人：          石昌民
     *创建时间：       2015-9-30
     */
    public function testExcel() {
        error_reporting(0);
        $filePath = Constant::WEBSERVER_EXCEL_SAVEPATH.'test.xls';
//         $dataList = array(array('school'=>'计算机学院','sno'=>'1',),
//         		array('school'=>'计算机学院','sno'=>'2',));
        $dataList[]=$_POST;
//         $headerList = array('emailAddress','website');
       /*获取post数据的key名 */
        foreach ($_POST as $val){
        	$headerList[]=key($_POST);
        	next($_POST);
        }
        $OutputFileName ='test';
        $outputType=1;
        $res=$this->exportToExcelWithHeader($filePath,$dataList,$headerList,$OutputFileName,$outputType);
        if($res==true){
        	$this->display('Index:submit');
        }
        else{
        	$this->display('Index:fail');
        }
        
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
	*创建人：          石昌民
	*创建时间：       2015-9-30
	 */
	public function exportToExcelWithHeader($filePath,$dataList,$headerList,$OutputFileName,$outputType=0){
		require './ThinkPHP/Library/Org/PHPExcel/PHPExcel.php';		
		//实例化Excel读取类
		$PHPReader = new \PHPExcel_Reader_Excel2007();
		$fileWebService=new FileWebController($this->userID);
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
		$allRow++;
		//实例化Excel写入类
		$PHPWriter = new \PHPExcel_Writer_Excel5($PHPExcel);
		
		//遍历数据列表中的内容,从第allRow行开始
		$i=$allRow;
		foreach ($dataList as  $key=>$value){
			$position='A';
			foreach ($headerList as $headerKey=>$headerValue){
				$currentSheet->setCellValueExplicit("$position".$i, $dataList[$key][$headerValue],\PHPExcel_Cell_DataType::TYPE_STRING);  //headerKey为数据库前台字段
				$position++;
			}
			$i++;
		}
		ob_end_clean();//清除缓冲区,避免乱码
		//决定是导出到浏览器还是文件服务器
		if($outputType!==0){
			$path=Constant::WEBSERVER_EXCEL_SAVEPATH."$OutputFileName.xls";
			$PHPWriter->save($path);
			// 					dump($path);
			// 					dump(file_exists($path));
			$fileURL=$fileWebService->uploadFile($path, "$OutputFileName.xls");
			if($fileURL==false){
				$fileURL=null;
			}
			return $fileURL;
             return false;
		}else{
			header('Content-Type: application/vnd.ms-excel');
			header("Content-Disposition: attachment;filename=$OutputFileName.xls");
			header('Cache-Control: max-age=0');
			$PHPWriter->save('php://output');
		}
	}
	/**
	 *
	 *函数名：		exportToExcel
	 *输入：		二维数组  要导出的内容 $dataList
	 * 			array(
	 *				array(key1=>$value1,key2=>$value2,...),
	 *				array(key1=>$value1,key2=>$value2,...),...
	 *			)
	 *输入：		一维数组  生成excel的头部信息 $headerList
	 *			array(数据库前台字段key1=>excel表头名称1，数据库前台字段key2=>excel表头名称2,...)
	 *输入：		string $fileName 输出文件名(建议输出文件名带时间戳)
	 *输入：		$outputType 默认0输出到浏览器，输入其他保存到文件服务器并成功返回相对地址，失败返回NULL
	 *返回值：		无
	 *功能：		导出数据到excel中
	 *创建人：          石昌民
	 *创建时间：       2015-9-30
	 */
	public function exportToExcel($dataList,$headerList,$fileName,$outputType=0){
		require './ThinkPHP/Library/Org/PHPExcel/PHPExcel.php';
		//记录模块调用信息
		$this->setFuncInfo(__FUNCTION__);
		$excel_obj = new \PHPExcel();
		$objWriter = new \PHPExcel_Writer_Excel5($excel_obj);
		$fileWebService=new FileWebController($this->userID);
		$excel_obj->setActiveSheetIndex(0);
		$act_sheet_obj=$excel_obj->getActiveSheet();
		$act_sheet_obj->setTitle('sheet');
	
		$position='A';
		foreach ($headerList as $key=>$value){
			$act_sheet_obj->setCellValue("$position".'1', $value);
			$act_sheet_obj->getColumnDimension($position)->setWidth(15);
			$act_sheet_obj->getStyle("$position".'1')->getAlignment()->setHorizontal("center");
			$act_sheet_obj->getStyle($position)->getAlignment()->setHorizontal("left");
			$act_sheet_obj->getStyle($position.'1')->getFont()->setBold(true);
			$position++;
		}
	
	
		//遍历数据列表中的内容
		$i=2;
		foreach ($dataList as  $key=>$value){
			$position='A';
			$act_sheet_obj->setCellValue("$position".$i, $i-1);  //序号添加
			foreach ($headerList as $headerKey=>$headerValue){
				$act_sheet_obj->setCellValue("$position".$i, $dataList[$key][$headerKey]);  //headerKey为数据库前台字段
				$position++;
			}
			$i++;
		}
	
		ob_end_clean();//清除缓冲区,避免乱码
		if($outputType!==0){//返回地址，文件保存在文件服务器
			$path=Constant::WEBSERVER_TMPFILE_SAVEPATH."$fileName.xls";
			$objWriter->save($path);
			// 					dump($path);
			// 					dump(file_exists($path));
			$fileURL=$fileWebService->uploadFile($path, "$fileName.xls");
			if($fileURL==false){
				$fileURL=null;
			}
			// 					dump($fileURL);
				
			// 					$fileAbsoluteURL=$fileWebService->getUploadURL($fileURL);
			// 					dump($fileAbsoluteURL);
			return $fileURL;
		}else{//直接下载
			header('Content-Type: application/vnd.ms-excel');
			header("Content-Disposition: attachment;filename=$fileName.xls");
			header('Cache-Control: max-age=0');
			$objWriter->save('php://output');
		}
	
	}
}