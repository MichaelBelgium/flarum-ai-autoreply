<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Exception;
use Flarum\Settings\SettingsRepositoryInterface;
use OpenAI;
use OpenAI\Client;
use OpenAI\Resources\Models;
use Psr\Log\LoggerInterface;

class OpenAIClient
{
    public ?Client $client = null;

    public function __construct(protected SettingsRepositoryInterface $settings, protected LoggerInterface $logger)
    {
        $apiKey = $this->settings->get('michaelbelgium-ai-autoreply.api_key');

        if (empty($apiKey)) {
            $this->logger->error('OpenAI API key is not set.');
            return;
        }

        $this->client = OpenAI::client($apiKey);
    }

    public function completions(string $content): ?string
    {
        if ($this->client === null) {
            return null;
        }

        try {
            $result = $this->client->chat()->create([
                'model' => $this->settings->get('michaelbelgium-ai-autoreply.model'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $content,
                    ],
                ],
                'max_completion_tokens' => (int) $this->settings->get('michaelbelgium-ai-autoreply.max_tokens'),
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return null;
        }

        return $result->choices[0]->message->content;
    }
}
