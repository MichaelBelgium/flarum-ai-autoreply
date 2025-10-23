<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;

class GoogleClient implements IPlatform
{
    private ?Client $client = null;

    public function __construct(protected SettingsRepositoryInterface $settings, protected LoggerInterface $logger)
    {
        $apiKey = $this->settings->get('michaelbelgium-ai-autoreply.api_key');

        if (empty($apiKey))
        {
            $this->logger->error('Gemini API key is not set.');
            return null;
        }

        $this->client = new Client([
            RequestOptions::HEADERS => [
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function completions(array $messages): ?string
    {
        if ($this->client === null)
            return null;

        $model = $this->settings->get('michaelbelgium-ai-autoreply.model');
        $tokens = $this->settings->get('michaelbelgium-ai-autoreply.max_tokens');
        $temperature = $this->settings->get('michaelbelgium-ai-autoreply.temperature');

        $options = [
            'messages' => $messages,
            'max_completion_tokens' => empty($tokens) ? null : (int)$tokens,
        ];

        if (empty($model))
            $options['model'] = 'gemini-2.5-flash-lite';
        else
            $options['model'] = $model;

        if (!empty($temperature))
        {
            $options['generationConfig'] = [
                'temperature' => (float)$temperature
            ];
        }

        try {
            $response = $this->client->post('https://generativelanguage.googleapis.com/v1beta/openai/chat/completions', [
                RequestOptions::JSON => $options
            ]);

            $json = json_decode($response->getBody()->getContents(), true);

            return $json['choices'][0]['message']['content'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}