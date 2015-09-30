<?php 
namespace Home\Model\FileSys;
use Home\Service\FileWebService\WebConstant;
/*
 * 文件上传处理类
 * 将图片从web服务器上传至图片服务器
 */

class UploadFileModel{
	private $userID;
	
	public function __construct($userID){
		$this->userID = $userID;
	}
	
	/**
	 * 函数名：	uploadImage
	 * 输入：		@param string $imgURL	本地（web服务器）图片URL
	 * 输入：		@param string $domain	图片服务器的域名
	 * 返回值：	成功返回文件存在数据库中的地址，形如"/1/1_12342342_origin.jpg"，如果上传失败或文件没有通过安全监测，则返回false
	 * 功能：		将图片上传至指定域名的服务器
	 * 创建人：	孙关军
	 * 创建时间：	2014-9-3
	 */
	public function uploadImage($imgURL, $domain){
		//判断图片是否存在
		if (!file_exists($imgURL)){
			return false;
		}
		//进行安全检测，若图片不安全则返回false
		if (!$this->detectImage($imgURL)){
			return false;
		}
		//按用户名和时间戳来给图片重命名
		$fileName = $this->renameImage();
		$dbURL = $this->userID . "/" . $fileName;
		//图片服务器上图片的存放地址（不含域名）
		$imgServerImgURL = WebConstant::IMAGE_STORAGE_UPLOAD_URL . $dbURL;
		//图片上传处理地址
// 		$uploadURL = '192.168.1.30:866/Application/Home/Service/FileAppService/ImageUploadAPI.php';
		$uploadURL = $domain . WebConstant::IMAGE_UPLOAD_API;
		$result = $this->uploadFile($imgURL, $uploadURL, $imgServerImgURL);
		if ($result){
// 			$del = $this->deleteLocalImage($imgURL);
			return $dbURL;
		}
		else{
			return false;
		}
	}
	
	/**
	 * 
	*函数名：		uploadFileToServer
	*输入：		string $fileURL (web服务器）文件存储位置
	*输入：		string $domain	文件服务器的域名
	*输入：		$fileName 文件名
	*返回值：		成功返回文件存在数据库中的地址，形如"/1/我的附件.doc"，如果上传失败或文件没有通过安全监测，则返回false
	*功能：		将文件上传至指定域名的服务器	
	*创建人：		夏权
	*创建时间：	2015-3-11
	 */
	public function uploadFileToServer($fileURL,$fileName,$domain){
		//判断文件是否存在
		if (!file_exists($fileURL)){
			return false;
		}
		$dbURL = $this->userID . "/" . $fileName;
		//文件服务器上文件的存放地址（不含域名）
		$fileServerURL = WebConstant::FILE_STORAGE_UPLOAD_URL . $dbURL;
		$uploadURL = $domain . WebConstant::FILE_STORAGR_UPLOAD_API;
		$result = $this->uploadFile($fileURL, $uploadURL, $fileServerURL);
		if ($result){
			// 			$del = $this->deleteLocalImage($imgURL);
			return $dbURL;
		}
		else{
			return false;
		}
	}
	/**
	 * 函数名：	renameImage
	 * 输入：		无
	 * 返回值：	文件系统中的统一文件名
	 * 功能：		将图片改名，并且将jpg、png、bmp、gif等格式的图片统一成jpg
	 * 创建人：	孙关军
	 * 创建时间：	2014-9-1
	 */
	private function renameImage(){
		$uuid = microtime(true) * 10000;
		return $this->userID . "_" . $uuid ."_origin.jpg"; 
	}
	
	/**
	 * 函数名：	detectImage
	 * 输入：		@param string $imgURL	web服务器上图片的地址
	 * 返回值：	 通过安全检验返回true，否则返回false
	 * 功能：		检验图片是否存在安全威胁
	 * 创建人：	孙关军
	 * 创建时间：	2014-9-1
	 */
	private function detectImage($imgURL){
		return true;
	}
	
	/**
	 * 函数名：	uploadFile
	 * 输入：		@param string $imgURL			web服务器上源图片URL
	 * 输入：		@param string $uploadURL		上传图片处理文件的url，为在图片服务器上的ImageUploadAPI.php
	 * 输入：		@param string $imgServerImgURL	上传到图片服务器上图片存储的url
	 * 返回值：	
	 * 功能：		将图片从web服务器上传到图片服务器
	 * 创建人：	孙关军
	 * 创建时间：	2014-9-1
	 */
	//TODO  重点检查
	private function uploadFile($imgURL, $uploadURL, $imgServerImgURL){
		//上传图片的相关数据
		//fileURL不能写临时地址，要写存在的绝对路径
		$data = array('userID' => $this->userID, 'web' => 0,
				'picture' => '@'.$imgURL, 'fileURL' => $imgServerImgURL);
		//上传图片
// 		dump('---curl----');
// 		dump($data);
// 		dump($uploadURL);
		$uploadResult = $this->uploadByCURL($data, $uploadURL);
		//上传成功后返回true
		return $uploadResult;
	}
	
	/**
	 * 函数名：	uploadByCURL
	 * 输入：		@param 一维数组	 $postData	上传文件的二进制数据
	 * 输入：		@param string	 $postUrl	文件处理url，该url是图片服务器上的
	 * 返回值：	成功：图片服务器处理后的含有图片URL的js脚本（用于前台处理图片）
	 * 			失败：false
	 * 功能：		使用curl模拟文件上传
	 * 创建人：	孙关军
	 * 创建时间：	2014-9-2
	 */
	private function uploadByCURL($postData, $postUrl){
		if($postData && $postUrl){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $postUrl);
			curl_setopt($curl, CURLOPT_POST, 1 );
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
// 			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($curl);
			$error 	= curl_error($curl);
			curl_close($curl);
			$lasrRe=$error ? $error : $result;
			return $lasrRe;
		}
		else
			return false;
	}
	
	/**
	 * 函数名：	deleteLocalImage
	 * 输入：		@param unknown_type $imgURL
	 * 返回值：	成功删除返回true，失败返回false。
	 * 功能：		私有方法，删除本地图片
	 * 创建人：	孙关军
	 * 创建时间：	2014-9-1
	 */
	private function deleteLocalImage($imgURL){
		if (file_exists($imgURL)){
			return unlink($imgURL);
		}
		else{
			return false;
		}
	}
}
?>