<?php


namespace TubeCatcher;


use YouTube\YouTubeDownloader;


/**
 *
 * @author Muhammad Saim
 *
 * A wrapper class to download youtube videos and infos
 * 
 */

class VideoDownloader
{

	private $youtube_downloader;



	/**
	 *
	 * initiate the youtube downloader
	 * 
	 */
	public function __construct()
	{
		$this->youtube_downloader = new YouTubeDownloader();
	}



	public function fetchDownloadLinks($link)
	{
		$links = $this->youtube_downloader->getDownloadLinks($link);
		return $links->getAllFormats();
	}

}