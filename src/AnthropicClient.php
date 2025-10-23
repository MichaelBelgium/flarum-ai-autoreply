<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Anthropic\Client;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Log\LoggerInterface;
use const Anthropic\Core\OMIT as omit;

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

         $this->client = new Client($apiKey);
    }

    public function completions(array $messages): ?string
    {
        if ($this->client === null)
            return null;

        $tokens = $this->settings->get('michaelbelgium-ai-autoreply.max_tokens');
        $model = $this->settings->get('michaelbelgium-ai-autoreply.model');
        $temperature = $this->settings->get('michaelbelgium-ai-autoreply.temperature');

        try {
            $message = $this->client->messages->create(
                empty($tokens) ? 1024 : (int)$tokens,
                $messages,
                empty($model) ? 'claude-haiku-4-5' : $model,
                temperature: empty($temperature) ? omit : $temperature
            );

            return $message->content[0]['text'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}