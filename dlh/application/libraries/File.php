<?php

/*
 * class for handling file operation
 *
 * @category	Libraries
 * @author		hygsan
 * @copyright	2015
 * @version		1.0 (08.05.2015)
 * 				1.1 (29.05.2015)
 *
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once 'Simpleimage.php';

class File {

	/*
	 * accomodate file's array properties from $_FILES
	 *
	 * @var array
	 */
	protected $file;

	/*
	 * accomodate file upload path value
	 *
	 * @var string
	 */
	protected $uploadPath;

	/*
	 * class constructor
	 *
	 * @param array	 $file		 accomodate $_FILES array properties
	 * @param string $uploadPath accomodate upload path value
	 */
	public function __construct(array $params) {
		$this->file = $params['file'];
		$this->uploadPath = $params['upload_path'];
	}

	/*
	 * check if file is an image type
	 *
	 * @return boolean
	 */
	public function isImage() {
		try {
			if (array_key_exists('width', $this->getFileInfo()) &&
				array_key_exists('height', $this->getFileInfo())) {
				return true;
			}

			return false;
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	/*
	 * get file extension only
	 *
	 * @return string
	 */
	public function getFileExt() {
		try {
			return pathinfo($this->file['name'], PATHINFO_EXTENSION);
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	/*
	 * get file mime type only
	 *
	 * @return string
	 */
	public function getFileMime() {
		try {
			$fileInfo = $this->getFileInfo();

			if ($this->isImage()) {
				return $fileInfo['exif']['MimeType'];
			} else {
				if (function_exists('finfo_open')) {
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$fileMime = finfo_file($finfo, $this->file['tmp_name']);

					return $fileMime;
				} else {
                    // if PHP's Fileinfo extension turned off
					return $this->file['type'];
				}
			}
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	/*
	 * get file information (meta)
	 *
	 * @return array
	 */
	public function getFileInfo() {
		try {
			if (exif_imagetype($this->file['tmp_name'])) {
				$image = new SimpleImage($this->file['tmp_name']);

				return $image->get_original_info();
			} else {
				return array('type' => $this->file['type'],
							 'name' => $this->file['name'],
							 'size' => $this->file['size']);
			}
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	/*
	 * get file name
	 *
	 * @param string $prefix hold prefix value to be added to the new generated file name
	 *
	 * @return string
	 */
	public function getFileName($prefix = '') {
		try {
			//$encryptedName = md5($this->file['name']) . '.' . $this->getFileExt();
			$encryptedName = md5(uniqid($this->file['name'], true)) . '.' . $this->getFileExt();

			if ($prefix) {
				$fileName = $prefix . $encryptedName;
			} else {
				$fileName = $encryptedName;
			}

			return $fileName;
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	/*
	 * upload file to server
	 *
	 * @param string $file file name
	 *
	 * @return boolean
	 */
	public function upload($file) {
		try {
			if (! move_uploaded_file($this->file['tmp_name'],
									 $this->uploadPath . '/' . $file)) {
				return false;
			}

			return true;
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	/*
	 * delete file from server
	 *
	 * @param string $file file name
	 *
	 * @return void
	 */
	public function delete($file) {
		try {
			unlink($this->uploadPath . '/' . $file);
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	/*
	 * compare uploaded image resolution size (width, height) with the minimum width and height
	 *
	 * @param int width  hold minimum image width value
	 * @param int height hold minimum image height value
	 *
	 * @return boolean
	 */
	public function isImageResolutionValid($width, $height) {
		try {
			$fileInfo = $this->getFileInfo();

			if ($fileInfo['width'] < $width || $fileInfo['height'] < $height) {
				return false;
			}

			return true;
		} catch (Exception $e) {
			return $this->printErrorLog($e);
		}
	}

	private function printErrorLog($e) {
		return 'Message: ' . $e->getMessage() . '<br />Code: ' . $e->getCode();
	}

}
