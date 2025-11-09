<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Exception;
use Flarum\Settings\SettingsRepositoryInterface;
use OpenAI;
use OpenAI\Client;
use Psr\Log\LoggerInterface;

class OpenAIClient implements IPlatform
{
    private ?Client $client = null;

    public function __construct(protected SettingsRepositoryInterface $settings, protected LoggerInterface $logger)
    {
        $apiKey = $this->settings->get('michaelbelgium-ai-autoreply.api_key');

        if (empty($apiKey)) {
            $this->logger->error('OpenAI API key is not set.');
            return;
        }

        $this->client = OpenAI::client($apiKey);
    }

    public function completions(array $messages): ?string
    {
        if ($this->client === null)
            return null;

        $tokens = $this->settings->get('michaelbelgium-ai-autoreply.max_tokens');
        $model = $this->settings->get('michaelbelgium-ai-autoreply.model');
        $temperature = $this->settings->get('michaelbelgium-ai-autoreply.temperature');
        $systemPrompt = $this->settings->get('michaelbelgium-ai-autoreply.system_prompt');

        if (!empty($systemPrompt))
        {
            $messages = [
                ['role' => 'developer', 'content' => $systemPrompt],
                ...$messages,
            ];
        }

        try {
            $result = $this->client->chat()->create([
                'model' => empty($model) ? 'gpt-5-mini' : $model,
                'messages' => $messages,
                'max_completion_tokens' => empty($tokens) ? null : (int)$tokens,
                'temperature' => empty($temperature) ? 1 : (float)$temperature,
            ]);

            return $result->choices[0]->message->content;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}
