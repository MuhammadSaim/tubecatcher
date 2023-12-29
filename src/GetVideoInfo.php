<?php

namespace TubeCatcher;

use Curl\Client;


/**
 *
 * @author Muhammad Saim
 * 
 */


class GetVideoInfo {


	private $youtube_api_key;
	private $youtube_service_url = "https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=snippet";
	private $client;

	/**
	 *
	 *	constructor to initiate the values
	 * 
	 * 
	 */

	public function __construct()
	{
		$this->youtube_api_key = TUBECATCHER_YOUTUBE_API;
		$this->client = new Client();
	}


	/**
	 *
	 * get the info for the video
	 * 
	 */

	public function getInfo($youtube_id)
	{
		$response = $this->client->get(sprintf($this->youtube_service_url, $youtube_id, $this->youtube_api_key));
		if($response->status === 200){
			return $this->fetchAndFormatDetails($response->body);
		}
		return [];
	}



	/**
	 *
	 * fetch the detail from response body and structure into an array
	 * 
	 * @param  $body 
	 * @return $array
	 */
	private function fetchAndFormatDetails($body)
	{
		if($body != null){

			$data = json_decode(json_encode(json_decode($body)), true);

			if(!isset($data['items']) && count($data['items']) < 0){
				return [];
			}
			
			return [
				'title' => $data['items'][0]['snippet']['title'],
            	'thumbnail' => $data['items'][0]['snippet']['thumbnails']['standard']['url'],
            	'channel_name' => $data['items'][0]['snippet']['channelTitle']
			];

		}
		return [];
	}


}