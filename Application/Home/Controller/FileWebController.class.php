<?php
namespace Home\Controller;
use Home\Model\UploadFileModel;
class FileWebController {
	private $userID;
	private $uploadModel;

	public function __construct($userID){
		$this->userID = $userID;
// 		$this->uploadModel = new UploadFileModel($userID);
        $this->uploadModel = new \Home\Model\UploadFileModel($userID);
	}
	/**
	 *
	 *函数名：		uploadFile
	 *输入：		string $fileURL web服务器中存的文件路径
	 *输入：		string $fileName 文件名
	 *返回值：		成功返回文件存在数据库中的地址，形如"1/我的附件.doc"，如果上传失败或文件没有通过安全监测，则返回false
	 *功能：
	 *创建人：		夏权
	 *创建时间：	2015-3-11
	 */
	public function uploadFile($fileURL,$fileName){
// 		$domain=$this->locateFileServer($this->userID,WebConstant::SERVER_TYPE_HANDLE);
		$domain='192.168.1.1';
		$result=$this->uploadModel->uploadFileToServer($fileURL, $fileName, $domain);
		return $result;
	}
	/**
	 *
	 *函数名：		locateFileServer
	 *输入：		int $userID
	 *输入：                int  businessType业务类型
	 *返回值：		成功返回文件服务器域名，比如thumbFile5.XXX.cn；失败返回false
	 *功能：		定位域名服务器
	 *创建人：		夏权
	 *创建时间：	2015-3-11
	 */
	private function locateFileServer($userID,$serverType,$businessType=0){
		if($serverType==WebConstant::SERVER_TYPE_HANDLE)
			return WebConstant::FILE_HANDLE_SERVER_1;
		if($serverType==WebConstant::SERVER_TYPE_STORAGE)
			return WebConstant::FILE_STORAGE_SERVER_1;
	}

	/**
	 *
	 *函数名：		getUploadURL
	 *输入：		string $fileURL
	 *输入：		int $businessType
	 *返回值：		返回文件存储的绝对路径
	 *功能：		把文件存储的相对路径 变换成绝对路径
	 *创建人：		夏权
	 *创建时间：	2015-3-11
	 */
	public function getUploadURL($fileURL,$businessType=0){
		//根据userID和businessType定位图片服务器
		$array = explode("/", $fileURL);
		$userID = $array[0];
		$domain = $this->locateFileServer($userID, WebConstant::SERVER_TYPE_STORAGE,$businessType);

		$absFileURL = 'http://' . $domain.WebConstant::FILE_STORAGE_UPLOAD_RELATIVE_URL.$fileURL;
		return $absFileURL;
	}
}
