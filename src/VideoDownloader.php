<?php


namespace TubeCatcher;


use YouTube\Exception\TooManyRequestsException;
use YouTube\Exception\VideoNotFoundException;
use YouTube\Exception\YouTubeException;
use YouTube\YouTubeDownloader;


/**
 *
 * @author Muhammad Saim
 *
 * A wrapper class to download YouTube videos and infos
 * 
 */

class VideoDownloader
{

	private $youtube_downloader;
	private $youtube_video_url = "https://www.youtube.com/watch?v=";



	/**
	 *
	 * initiate the YouTube downloader
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
     * @param $video_id
     * @return array       return the formatted array of links
     * @throws TooManyRequestsException
     * @throws VideoNotFoundException
     * @throws YouTubeException
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
	 * @param array $videos unformated array
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