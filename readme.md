#VoiceBase (beta 2) - HTTP Client

APIs for speech recognition and speech analytics, powering insights every business needs.

> For more information [See Voicebase's Developer Portal](https://www.voicebase.com/developers/).

## Installation

### Step 1: Composer

From the command line, run:

```
composer require scottlaurent/voicebase
```

##### OPTIONAL Laravel Service Provider for the Starbucks drinkers of the PHP community.

For your Laravel app, open `config/app.php` and, within the `providers` array, append:

```
Scottlaurent\Voicebase\ServiceProviders\LaravelServiceProvider::class
```

This will bootstrap the package into Laravel.

Create a `/config/voicebase.php` file and add this
```
<?php

	return [
		'api_key' => env('VOICEBASE_API_KEY'),
		'token' => env('VOICEBASE_BEARER_TOKEN'),
	];
```

Make sure to add the values to your .env file or environment settings (or just hardcode them above)

### Step 2: Instantiate a voicebase client

##### Non-laravel:
---
```
$voicebase = new \Scottlaurent\Voicebase\Voicebase(YOUR_BEARER_TOKEN)
```
##### Laravel (token not needed because you put in the config)
---
```
$voicebase = app('voicebase')
```

---
## Crazy awesome amazing stuff you can do:

#### MEDIA
```
// Upload a media file to VoiceBase for analysis. Media files reside under the /media/ collection.

// Non-Laravel
$result = $voicebase->media()->uploadStream(fopen('sample.mp3', 'r'))

// Laravel
$result = $voicebase->media()->uploadStream(Storage::readStream('sample.mp3’))

// Alternative Upload Method
$result = $voicebase->media()->uploadFromPath('sample.mp3'))

// Make sure you keep this
$media_id = $result['mediaId']
```
#### Once your media is uploaded
```
// Since I am impatient, I can run this over and over and over and over
$has_finished = $voicebase->media($media_id)->isFinished()

$full_informative_status = $voicebase->media($media_id)->checkProcessingStatus()

$results_if_available = $voicebase->media($media_id)->getProcessedResults()

// A good one word response.
// todo: teach girlfriend to response like this
$status = $voicebase->media($media_id)->getStatus()

// Every parent's fear
$has_failed = $voicebase->media($media_id)->hasFailed()

// The latest topics and keywords for the media
$transcripts = $voicebase->media($media_id)->getTranscripts()
$latest_transcripts = $voicebase->media($media_id)->getLatestTranscript()

// What did the people say in that mp3 file??
$keywords = $voicebase->media($media_id)->getKeywords()
$latest_keywords = $voicebase->media($media_id)->getLatestKeywords()

$topics = $voicebase->media($media_id)->getTopics()
$latest_topics = $voicebase->media($media_id)->getLatestTopics()
```

#### KEYWORDS (VIA DEFINITIONS)
```
// For the obsessive compuslive
$all_definitions = $voicebase->definitions()->all()

// Create a keyword group
$group_name = 'transportation'; // will be slugified
$keywords = ['planes','trains','automobiles'];
$voicebase->definitions()->createKeywordGroup($group_name,$keywords);

// Wait, I forgot to add my other vehicles
$voicebase->definitions('transportation')->addKeywords(['spaceships','riding lawn mowers'])

// Damn, the DMV downgraded John Deere
$voicebase->definitions('transportation')->removeKeywords(['riding lawn mowers'])

// Ok, so what the hell was in that definition again??
$voicebase->definitions('transportation')->get()
```
###### Contact: scott@baselineapplications.com