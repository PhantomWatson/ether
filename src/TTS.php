<?php

namespace App;

use Cake\Core\Configure;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

class TTS
{
    /**
     * @throws ApiException
     * @throws \Exception
     * @return string
     */
    public function generate($text, $filename): string
    {
        if (!$text) {
            throw new \Exception('No text supplied');
        }
        if (!$filename) {
            throw new \Exception('No filename given');
        }
        $extension = '.mp3';
        $filepath = WWW_ROOT . 'audio' . DS . $filename . $extension;
        if (file_exists($filepath)) {
            throw new \Exception('File already exists');
        }

        $input = new SynthesisInput();
        $input->setText($text);

        $resp = $this->getClient()->synthesizeSpeech(
            $input,
            $this->getVoice(),
            $this->getAudioConfig()
        );

        file_put_contents($filepath, $resp->getAudioContent());

        return $filename . $extension;
    }

    /**
     * @throws ValidationException
     */
    private function getClient()
    {
        return new TextToSpeechClient([
            'credentials' => json_decode(
                file_get_contents(Configure::read('googleTtsApiKey')),
                true
            ),
        ]);
    }

    private function getVoice()
    {
        $voice = new VoiceSelectionParams();
        $voice->setLanguageCode('en-US');
        $voice->setName('en-US-Wavenet-I');
        return $voice;
    }

    private function getAudioConfig()
    {
        $audioConfig = new AudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::MP3);
        return $audioConfig;
    }

}
