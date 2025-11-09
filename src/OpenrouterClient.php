<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;

class OpenrouterClient implements IPlatform
{
    private ?Client $client = null;

    public function __construct(protected SettingsRepositoryInterface $settings, protected LoggerInterface $logger)
    {
        $apiKey = $this->settings->get('michaelbelgium-ai-autoreply.api_key');

        if (empty($apiKey))
        {
            $this->logger->error('OpenRouter API key is not set.');
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
            'messages' => $messages
        ];

        if (empty($model))
            $options['model'] = 'openrouter/auto';
        elseif (str_contains(',', $model))
            $options['models'] = explode(',', $model);
        else
            $options['model'] = $model;

        if (!empty($tokens))
            $options['max_tokens'] = (int)$tokens;

        if (!empty($temperature))
            $options['temperature'] = $temperature;

        try {
            $response = $this->client->post('https://openrouter.ai/api/v1/chat/completions', [
                RequestOptions::JSON => $options
            ]);

            $json = json_decode((string)$response->getBody(), true);

            return $json['choices'][0]['message']['content'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}