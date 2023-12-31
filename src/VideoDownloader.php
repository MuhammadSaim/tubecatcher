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
	private $youtube_video_url = "https://www.youtube.com/watch?v=";



	/**
	 *
	 * initiate the youtube downloader
	 * 
	 */
	public function __construct()
	{
		$this->youtube_downloader = new YouTubeDownloader();
	}


	/**
	 *
	 * fetch the download links from the page link
	 * 
	 * @param  string $link Youtube video url
	 * @return array       return the formated array of links
	 */
	public function fetchDownloadLinks($video_id)
	{
		$links = $this->youtube_downloader->getDownloadLinks($this->youtube_video_url.$video_id);
		return  $this->formatUrls($links->getCombinedFormats());
	}


	/**
	 *
	 * format the urls
	 *
	 * @param array unformated array
	 * 
	 * 
	 */

	public function formatUrls($videos)
	{

		$urls = [];

		foreach($videos as $video){

			$urls[] = [
				'quality' => $video->qualityLabel,
				'url'     => $video->url
			];

		}

		return $urls;
	}


}