<?php

/*
 * class for handling image manipulation
 *
 * @category	Libraries
 * @author		hygsan
 * @copyright	2015
 * @version		1.0 (08.05.2015)
 * @uses		SimpleImage.php
 * 
 */

require_once 'Simpleimage.php';
 
class Image {
	
	public function resizeImage($originalFile, $fileName=null, $uploadPath, array $dimension) {		
		$image = new SimpleImage($uploadPath . '/' . $originalFile);
		$image->resize($dimension['width'], $dimension['height']);
		$image->save($uploadPath . '/' . ($fileName == null ? $originalFile : $fileName));
	}
	
	public function resizeImageFitToHeight($fileName, $uploadPath, $height) {		
		$image = new SimpleImage($uploadPath . '/' . $fileName);
		$image->fit_to_height($height);
		$image->save();
	}
	
	public function resizeImageFitToWidth($fileName, $uploadPath, $width) {		
		$image = new SimpleImage($uploadPath . '/' . $fileName);
		$image->fit_to_width($width);
		$image->save();
	}
	
	public function resizeImageThumbnail($originalFile, $fileName, $uploadPath, array $dimension) {
		$image = new SimpleImage($uploadPath . '/' . $originalFile);
		$image->thumbnail($dimension['width'], $dimension['height']);
		$image->save($uploadPath . '/' . $fileName);
	}
	
	public function resizeImageBestFit($originalFile, $fileName=null, $uploadPath, array $dimension) {
		$image = new SimpleImage($uploadPath . '/' . $originalFile);
		$image->best_fit($dimension['width'], $dimension['height']);
		$image->save($uploadPath . '/' . $originalFile);
	}
	
	public function cropImage($originalFile, $fileName=null, $uploadPath, array $coords) {
		$image = new SimpleImage($uploadPath . '/' . $originalFile);
		$image->crop($coords['x1'], $coords['y1'], $coords['x2'], $coords['y2']);
		$image->save($uploadPath . '/' . $originalFile);
	}
	
}