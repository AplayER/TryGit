<?php
namespace Home\Service\FileAppService;

	$file = $_FILES ["picture"];
	$allowFileType=array('jpeg','doc','docx','gif','bmp','png','rar','txt','pdf','zip','xlsx','xls');
	$fileInfo=explode('.', $_POST['fileURL']);
// 	$handle=fopen('/home/testFile','w+');
// 	foreach($_POST as $key=>$value){
// 		$string="$key=>$value;";
// 		fwrite($handle, $string);
// 	}
// 	fclose($handle);
	$fileType = checkFileType ( $file ["tmp_name"] );
	if(in_array($fileType,$allowFileType)||$fileInfo[count($fileInfo)-1]=='txt'){
		if ($file ["error"] > 0)
		{
			// 		记录日志
			Logger::log("Upload failed. Field \$file['error'] is true.");
		} else	{
			if (file_exists ( "upload/" . $file ["name"] ))
			{
				echo $file ["name"] . " already exists. ";
			} else
			{
				$targetURL = $_POST ["fileURL"];
				if (! file_exists ( dirname ( $targetURL ) ))
				{
					mkdir ( dirname ( $targetURL ), 0777, true );
				}
				if (file_exists ( dirname ( $targetURL ) ))
				{
					if( !file_exists($file ["tmp_name"]))
					{
						// 					记录日志
						Logger::log("Upload failed. File ".$file ["tmp_name"]." dosen't exist.");
						return;
					}
					move_uploaded_file ( $file ["tmp_name"], $targetURL );
					if(!file_exists($targetURL))
					{
						return 'jahahah';
						// 					记录日志
						Logger::log("Upload failed. Can't copy from ".$file ["tmp_name"]." to ".$targetURL);
					}
				}
				else
				{
					// 					记录日志
					Logger::log("Upload failed. Path ".dirname ( $targetURL )." dosen't exist.");
				}
			}
		}
	} else
	{	
	// 	记录日志
		Logger::log("Upload failed. Invalid file format. File is not gif or hpeg or png.");
	}
	
	
	
	
	
	function checkFileType($fileLocalURL)
	{
		$file = fopen ( $fileLocalURL, "rb" );
		$bin = fread ( $file, 2 ); // 只读2字节
		fclose ( $file );
		$strInfo = @unpack ( "C2chars", $bin );
		$typeCode = intval ( $strInfo ['chars1'] . $strInfo ['chars2'] );
		$fileType = '';
		switch ($typeCode)
		{
			case 255216 :
				$fileType = 'jpeg'; // jpg
				break;
			case 7173 :
				$fileType = 'gif'; // gif
				return false;
				break;
			case 6677 :
				$fileType = 'bmp'; // bmp
				break;
			case 13780 :
				$fileType = 'png'; // png
				break;
			case 8075:
				$fileType = 'docx'; //docx zip xlsx
				break;
			case 208207:
				$fileType = 'doc'; //doc
				break;
			case 8297:
				$fileType = 'rar'; //rar
				break;
			case 208207:
				$fileType= 'xls'; //xls
				break;
			case 3780:
				$fileType= 'pdf'; //pdf
				break;
			default :
				return false;
		}
		return $fileType;
	}
?>
