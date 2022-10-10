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
     * @param string $filename without extension
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

        // Text is over the limit; make multiple audio files and combine them
        if (mb_strlen($text) >= TTS::INPUT_LIMIT) {
            return $this->generateOversized($text, $filename);
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

    /**
     * Generates an oversized TTS file by creating multiple smaller files and combining them together
     *
     * @param string $text
     * @param string $filename without extension
     * @return string
     * @throws ApiException
     * @throws ValidationException
     */
    public function generateOversized(string $text, string $filename): string
    {
        $partFilePaths = [];
        $partNum = 1;
        $words = explode(' ', $text);
        do {
            $partText = '';
            while ($words && mb_strlen($partText . $words[0]) < TTS::INPUT_LIMIT) {
                $word = array_shift($words);
                $partText .= $word . ' ';
            }
            $partFilePaths[] = TTS::PATH . $this->generate($partText, "$filename.$partNum");
            $partNum++;
        } while ($words);

        // Combine files
        $filename = $filename . TTS::EXTENSION;
        $combinedFilepath = TTS::PATH . $filename;
        file_put_contents($combinedFilepath, '');
        (new PhpMp3())->multiJoin($combinedFilepath, $partFilePaths);

        // Cleanup
        foreach ($partFilePaths as $filePath) {
            unlink($filePath);
        }

        return $filename;
    }
}
