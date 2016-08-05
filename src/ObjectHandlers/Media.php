<?php

namespace Scottlaurent\Voicebase\ObjectHandlers;

use Scottlaurent\Voicebase\Voicebase;

/**
 * Class Media
 * @package Scottlaurent\Voicebase\ObjectHandlers
 */
class Media extends BaseObjectManagerHandler
{

	/**
	 * @var null
	 */
	protected $media_id;

	/**
	 * BaseObjectManagerHandler constructor.
	 * @param Voicebase $voicebase
	 * @param null $media_id
	 */
	public function __construct(Voicebase $voicebase, $media_id=null)
    {
		parent::__construct($voicebase);

		$this->media_id = $media_id;
    }

	/**
	 * Upload a media file to VoiceBase for analysis. Media files reside under the /media/ collection.
	 *
	 * @param $stream
	 * @param array $parameters
	 * @return string
	 * @throws \Exception
	 */
	public function uploadUsingStream($stream,$extra_configuration=[])
    {

	    $configuration = $extra_configuration + [
	        'transcripts'=>[
	            'engine'=>$this->voicebase->accuracy_engine
	        ]
	    ];

        $parameters = [
            'multipart' => [
	            [
	                'name'=>'media',
	                'contents'=>$stream
	            ],
				[
					'name'=>'configuration',
					'contents'=>json_encode([
					    'configuration'=> $configuration
					])
				]
            ]
        ];

        try {

            $response = $this->voicebase->post('media',$parameters);
            return $this->decodeResponse($response);

        } catch (\Exception $e) {

	        throw new \Exception('There was a problem uploading your media file.  ' . $e->getMessage());

        }


    }

	/**
	 * Upload a media file to VoiceBase for analysis. Media files reside under the /media/ collection.
	 *
	 * @param $path
	 * @param array $parameters
	 * @return string
	 * @throws \Exception
	 */
	public function uploadFromPath($path,$parameters=[])
    {
        return $this->uploadUsingStream(fopen($path, 'r'),$parameters);
    }

	/**
	 * Determine if a given media object has finished processing
	 * @return bool
	 */
	public function isFinished()
	{
		$this->requiresMediaId();
		return $this->getStatus($this->media_id) == 'finished';
	}

	/**
	 * Determine if a given media object has failed processing
	 * @return bool
	 */
	public function hasFailed()
	{
		$this->requiresMediaId();
		return $this->getStatus($this->media_id) == 'failed';
	}

 	/**
	 * Check the status of processing for the media by GETting its corresponding item in the /media/ collection.
	 *
	 * @return mixed
	 */
	public function getStatus()
    {
        $this->requiresMediaId();
        $status = $this->checkProcessingStatus();
        return $status['media']['status'];
    }

	/**
	 * Get Full Results of item in the /media/ collection.
	 *
	 * @param array $parameters
	 * @param array $extra_headers
	 * @return mixed
	 * @throws \Exception
	 */
    public function getProcessedResults($parameters=[],$extra_headers=[])
    {
        $this->requiresMediaId();
        $results = $this->checkProcessingStatus($parameters,$extra_headers);
        $status = $results['media']['status'];

        if ($status == "failed")
        {
            return "Media Object ".$this->media_id." has failed to process.";
        }

        if ($status != "finished")
        {
            return "Media Object ".$this->media_id." is $status, but has not yet completed processing.";
        }
        return $results;
    }

	/**
	 * Check the status of processing for the media by GETting its corresponding item in the /media/ collection.
	 *
	 * @param array $parameters
	 * @param array $extra_headers
	 * @return mixed
	 * @throws \Exception
	 */
	public function checkProcessingStatus($parameters=[],$extra_headers=[])
    {
        $this->requiresMediaId();
        $response = $this->voicebase->get("media/".$this->media_id,$parameters,$extra_headers);
        return $this->decodeResponse($response);
    }

	/**
	 * Retrieve all plain text transcripts for the media (present if requested when media is uploaded)
	 * @return mixed
	 * @throws \Exception
	 */
	public function getTranscripts()
	{
		$results = $this->getProcessedResults();
		return is_array($results) ? $results['media']['transcripts'] : $results;

	}

	/**
	 * Retrieve the latest JSON transcript for the media (present if requested when media is uploaded)
	 * @return mixed
	 * @throws \Exception
	 */
	public function getLatestTranscript()
	{
		$results = $this->getProcessedResults();
		return is_array($results) ? $results['media']['transcripts']['latest']['words'] : $results;
	}

	/**
	 * Retrieve the latest PLAIN TEXT transcript for the media (present if requested when media is uploaded)
	 * @return mixed
	 * @throws \Exception
	 */
	public function getPlainTextTranscript()
	{
        $this->requiresMediaId();
        $response = $this->voicebase->get("media/".$this->media_id.'/transcripts/latest',[],['Accept' => 'text/plain']);
        return $this->returnRawResponse($response);
	}



	/**
	 * Retrieve the latest topics and keywords for the media (present if requested when media is uploaded)
	 * @return mixed
	 * @throws \Exception
	 */
	public function getLatestTopics()
	{
		$results = $this->getProcessedResults();
		return is_array($results) ? $results['media']['topics']['latest']['topics'] : $results;
	}

	/**
	 * Retrieve all topics and keywords for the media (present if requested when media is uploaded)
	 * @return mixed
	 * @throws \Exception
	 */
	public function getTopics()
	{
		$results = $this->getProcessedResults();
		return is_array($results) ? $results['media']['topics'] : $results;

	}

	/**
	 * Retrieve all revisions of keywords
	 * @return mixed
	 * @throws \Exception
	 */
	public function getKeywords()
	{
		$results = $this->getProcessedResults();
		return is_array($results) ? $results['media']['keywords'] : $results;
	}

	/**
	 * Retrieve a the most current revision of keywords
	 * @return mixed
	 * @throws \Exception
	 */
	public function getLatestKeywords()
	{
		$results = $this->getProcessedResults();
		return is_array($results) ? $results['media']['keywords']['latest']['words'] : $results;
	}

	/**
	 * Streaming formats for uploaded media.
	 * @return mixed
	 * @throws \Exception
	 */
	public function getStreams()
	{
        $this->requiresMediaId();
        $response = $this->voicebase->get("media/".$this->media_id."/streams");
        return $this->decodeResponse($response)['streams'];
	}

	/**
	 * Collection of processing phases.
	 * @return mixed
	 * @throws \Exception
	 */
	public function getProgress()
	{
        $this->requiresMediaId();
        $response = $this->voicebase->get("media/".$this->media_id."/progress");
        return $this->returnRawResponse($response);
	}

	/**
	 * Forces a method to be called only after we have provided a media ID
	 * @throws \Exception
	 */
	private function requiresMediaId()
    {
        if (!$this->media_id)
        {
            throw new \Exception('Media ID required');
        }
    }
}