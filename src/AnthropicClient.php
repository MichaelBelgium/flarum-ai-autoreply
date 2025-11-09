<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;

class AnthropicClient implements IPlatform
{
    private ?Client $client = null;

    public function __construct(protected SettingsRepositoryInterface $settings, protected LoggerInterface $logger)
    {
        $apiKey = $this->settings->get('michaelbelgium-ai-autoreply.api_key');

        if (empty($apiKey)) {
            $this->logger->error('Anthropic API key is not set.');
            return;
        }

         $this->client = new Client([
             RequestOptions::HEADERS => [
                 'x-api-key' => $apiKey,
                 'anthropic-version' => '2023-06-01',
             ]
         ]);
    }

    public function completions(array $messages): ?string
    {
        if ($this->client === null)
            return null;

        $tokens = $this->settings->get('michaelbelgium-ai-autoreply.max_tokens');
        $model = $this->settings->get('michaelbelgium-ai-autoreply.model');
        $temperature = $this->settings->get('michaelbelgium-ai-autoreply.temperature');
        $systemPrompt = $this->settings->get('michaelbelgium-ai-autoreply.system_prompt');

        try {
            $response = $this->client->post('https://api.anthropic.com/v1/messages', [
                RequestOptions::JSON => [
                    'model' => empty($model) ? 'claude-haiku-4-5' : $model,
                    'messages' => $messages,
                    'max_tokens' => empty($tokens) ? 1024 : (int)$tokens,
                    'temperature' => empty($temperature) ? 1 : $temperature,
                    'system' => empty($systemPrompt) ? '' : $systemPrompt,
                ]
            ]);

            $json = json_decode((string)$response->getBody(), true);

            return $json['content'][0]['text'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}