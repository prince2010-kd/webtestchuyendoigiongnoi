<?php

namespace App\Services;

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

class SpeechToTextService
{
    public function transcribe($audioFilePath)
    {
        $client = new SpeechClient();

        $audioContent = file_get_contents($audioFilePath);

        $audio = (new RecognitionAudio())
            ->setContent($audioContent);

        $config = (new RecognitionConfig())
            ->setEncoding(AudioEncoding::LINEAR16) // LINEAR16 cho WAV. Nếu dùng MP3 => change to MP3
            ->setSampleRateHertz(16000) // Điều chỉnh đúng với file audio (có thể là 44100)
            ->setLanguageCode('vi-VN');

        $response = $client->recognize($config, $audio);

        $text = '';
        foreach ($response->getResults() as $result) {
            $text .= $result->getAlternatives()[0]->getTranscript() . ' ';
        }

        $client->close();

        return trim($text);
    }
}
