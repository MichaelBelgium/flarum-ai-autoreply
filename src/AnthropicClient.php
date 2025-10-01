<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Anthropic\Client;
use Anthropic\Messages\MessageParam;
use Flarum\Settings\SettingsRepositoryInterface;
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

         $this->client = new Client($apiKey);
    }

    public function completions(string $content): ?string
    {
        if ($this->client === null)
            return null;

        $tokens = $this->settings->get('michaelbelgium-ai-autoreply.max_tokens');

        try {
            $message = $this->client->messages->create(
                empty($tokens) ? 1024 : (int)$tokens,
                [MessageParam::with($content, 'user')],
                $this->settings->get('michaelbelgium-ai-autoreply.model')
            );

            return $message->content[0]['text'];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}