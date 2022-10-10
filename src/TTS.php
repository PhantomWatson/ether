<?php

namespace App;

use Cake\Core\Configure;
use Cake\Log\Log;
use Exception;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

class TTS
{
    const EXTENSION = '.mp3';
    const INPUT_LIMIT = 5000;
    const PATH = WWW_ROOT . 'audio' . DS;

    /**
     * @param string $text
     * @param string $filename
     * @return string
     * @throws ApiException
     * @throws ValidationException
     * @throws Exception
     */
    public function generate(string $text, string $filename): string
    {
        if (!$text) {
            throw new Exception('No text supplied');
        }
        if (!$filename) {
            throw new Exception('No filename given');
        }

        $filename .= self::EXTENSION;
        $filepath = self::PATH . $filename;

        if (file_exists($filepath)) {
            throw new Exception('File already exists');
        }

        $input = new SynthesisInput();
        $input->setText($text);

        try {
            $resp = $this->getClient()->synthesizeSpeech(
                $input,
                $this->getVoice(),
                $this->getAudioConfig()
            );
        } catch (ApiException $e) {
            Log::error("Error synthesizing TTS file $filename");
            Log::error($e->getMessage());
            throw $e;
        }

        file_put_contents($filepath, $resp->getAudioContent());

        return $filename;
    }

    /**
     * @throws ValidationException
     */
    private function getClient(): TextToSpeechClient
    {
        return new TextToSpeechClient([
            'credentials' => json_decode(
                file_get_contents(Configure::read('googleTtsApiKey')),
                true
            ),
        ]);
    }

    /**
     * @return VoiceSelectionParams
     */
    private function getVoice(): VoiceSelectionParams
    {
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode('en-US');
        $voice->setName('en-US-Wavenet-I');
        return $voice;
    }

    /**
     * @return AudioConfig
     */
    private function getAudioConfig(): AudioConfig
    {
        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);
        return $audioConfig;
    }
}
