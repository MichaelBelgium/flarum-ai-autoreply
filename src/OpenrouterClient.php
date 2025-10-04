<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Http;
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

    public function completions(string $content): ?string
    {
        if ($this->client === null)
            return null;

        $model = $this->settings->get('michaelbelgium-ai-autoreply.model');

        if (empty($models))
            $model = 'openrouter/auto';
        else if (str_contains($model, ','))
            $model = explode(',', $models);

        try {
            $response = $this->client->post('https://openrouter.ai/api/v1/chat/completions', [
                RequestOptions::JSON => [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $content
                        ]
                    ]
                ]
            ]);

            $json = json_decode((string)$response->getBody(), true);

            return $json['choices'][0]['message']['content'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}