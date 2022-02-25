<?php

/**
 * Amazon S3 Upload PHP class
 *
 * @version 0.1
 */

class S3_upload {
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('s3');

		$this->CI->config->load('s3', TRUE);
		$s3_config = $this->CI->config->item('s3');
		$this->bucket_name = $s3_config['bucket_name'];
		$this->folder_name = $s3_config['folder_name'];
		$this->s3_url = $s3_config['s3_url'];
		
	}

	function upload_file($file, $file_name)
	{
		//validate that filename contains only 1 period
		if(substr_count($file['name'],'.') != 1)
		{
			return "Invalid filename. Must contain exactly 1 period.";
		}
		else
		{
			//get extention name
			$exp = explode('.', $file['name']);
			$ext = $exp[1];
			$fname = $exp[0];

			// generate unique filename
			$file_path = $file['tmp_name'];
			//$file = pathinfo($file_path);
			//$s3_file = $file['filename'].'-'.rand(1000,1).'.png';//.$file['extension'];
			$s3_file = $file_name.'.'.$ext;
			$mime_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path);

			$saved = $this->CI->s3->putObjectFile(
				$file_path,
				$this->bucket_name,
				$this->folder_name.$s3_file,
				S3::ACL_PUBLIC_READ,
				array(),
				$mime_type
			);
			if ($saved) {
				return 'https://'.$this->bucket_name.'.s3-ap-northeast-1.amazonaws.com/'.$this->folder_name.$s3_file;
			}
		}

	}

	function uploadS3Images($file, $file_name, $s3_directory){
		//validate that filename contains only 1 period
		if(substr_count($file['name'],'.') != 1)
		{
			return "Invalid filename. Must contain exactly 1 period.";
		}
		else
		{
			//get extention name
			$exp = explode('.', $file['name']);
			$ext = $exp[1];
			$fname = $exp[0];

			// generate unique filename
			$file_path = $file['tmp_name'];
		
			$s3_file = $file_name.'.'.$ext;
			$mime_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path);

			$saved = $this->CI->s3->putObjectFile(
				$file_path,
				$this->bucket_name,
				$s3_directory.$s3_file,
				S3::ACL_PUBLIC_READ,
				array(),
				$mime_type
			);
			if ($saved) {
				return 1;
			}else{
				return 0;
			}
		}

	}

	function uploadS3ImagesOrig($fileTempName, $activityContent){
		$saved = $this->CI->s3->putObjectFile(
			$fileTempName,
			$this->bucket_name,
			gets3_bucketfolder().$activityContent,
			S3::ACL_PUBLIC_READ
		);

		if ($saved) {
			return 1;
		}
		else{
			return 0;
		}
	}

	function uploadS3ImagesOrig_link($fileTempName, $activityContent){
		$saved = $this->CI->s3->putObjectFile(
			$fileTempName,
			$this->bucket_name,
			gets3_bucketfolder().$activityContent,
			S3::ACL_PUBLIC_READ
		);

		if ($saved) {
			// return 1;
			return 'https://'.$this->bucket_name.'.s3-ap-northeast-1.amazonaws.com/'.$activityContent;
		}
		else{
			return 0;
		}
	}

	function uploadS3ImagesDiff($fileTempName, $activityContent){
		$saved = $this->CI->s3->putObjectFile(
			$fileTempName,
			$this->bucket_name,
			gets3_bucketfolder().$activityContent,
			S3::ACL_PUBLIC_READ
		);

		if ($saved) {
			return 1;
		}
		else{
			return 0;
		}
	}

	function renameS3images($s3_directory_old, $s3_directory_new){
		$sourceBucket  = $this->bucket_name;
		$sourceKeyname = $s3_directory_old;
		$targetBucket  =  $this->bucket_name;
		$targetKeyname = $s3_directory_new;

		$result = $this->CI->s3->copyObject(
			$sourceBucket,
			$sourceKeyname,
			$targetBucket,
			$targetKeyname
		);

		if($result){
			$this->CI->s3->deleteObject(
				$this->bucket_name,
				$s3_directory_old
			);
		}
		
	}

	function deleteS3Images($s3_directory){
		$result =$this->CI->s3->deleteObject(
			$this->bucket_name,
			$s3_directory
		);
	}
}