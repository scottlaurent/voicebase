<?php

namespace Scottlaurent\Voicebase\ObjectHandlers;

use Scottlaurent\Voicebase\Voicebase;

/**
 * Class Definitions
 * @package Scottlaurent\Voicebase\ObjectHandlers
 */
class Definitions extends BaseObjectManagerHandler
{

	/**
	 * @var null
	 */
	protected $group_id;

	/**
	 * BaseObjectManagerHandler constructor.
	 * @param Voicebase $voicebase
	 * @param null $group_id
	 */
	public function __construct(Voicebase $voicebase, $group_id=null)
    {
		parent::__construct($voicebase);

		$this->group_id = $group_id;
    }

	/**
	 * Definitions of complex behaviors or reusable data sets.
	 * @return mixed
	 * @throws \Exception
	 */
	public function keywordGroups()
	{
        $response = $this->voicebase->get("definitions/keywords/groups");
        return $this->decodeResponse($response)['groups'];
	}

	/**
	 * @param $groupname
	 * @param $keyword_array
	 * @return mixed
	 */
	public function createKeywordGroup($groupname, $keyword_array)
	{
		$groupname = self::slugify($groupname);
		$parameters = [
			'json' => [
				'name' => $groupname,
				'keywords' => $keyword_array
			]
		];
		$response = $this->voicebase->put("definitions/keywords/groups/$groupname",$parameters);
		return $this->returnRawResponse($response);
	}

	/**
	 * Get the raw keyword group with revision history
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function get()
	{
		$this->requiresGroupId();
		$response = $this->voicebase->get("definitions/keywords/groups/".$this->group_id);
		return $this->decodeResponse($response);
	}

	/**
	 * Get keywords from a keyword group
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function getKeywords()
	{
		$this->requiresGroupId();
		$response = $this->voicebase->get("definitions/keywords/groups/".$this->group_id);
		return $this->decodeResponse($response)['keywords'];
	}

	/**
	 * Remove keywords from a keyword group
	 *
	 * @param $keywords
	 * @return mixed
	 * @throws \Exception
	 */
	public function removeKeywords($keywords)
	{
		$current_keywords = $this->getKeywords();
		foreach ($keywords as $keyword)
		{
			if (in_array($keyword,$current_keywords[]))
			{
				unset($current_keywords[$keyword]);
			}
		}
		if (!$current_keywords)
		{
			throw new \Exception('Your group must contain at least one keyword');
		}
		return $this->createKeywordGroup($this->group_id,$current_keywords);
	}

	/**
	 * Add keywords to a keyword group
	 *
	 * @param $keywords
	 * @return mixed
	 */
	public function addKeywords($keywords)
	{
		$current_keywords = $this->getKeywords();
		$current_keywords = array_merge($current_keywords,$keywords);
		return $this->createKeywordGroup($this->group_id,$current_keywords);
	}

	/**
	 * Forces a method to be called only after we have provided a group ID
	 * @throws \Exception
	 */
	private function requiresGroupId()
    {
        if (!$this->group_id)
        {
            throw new \Exception('Group ID required');
        }
    }

	/**
	 * Slugify our group names
	 *
	 * @param $text
	 * @return string
	 */
	static public function slugify($text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text))
		{
		return 'n-a';
		}

		return $text;
	}
}