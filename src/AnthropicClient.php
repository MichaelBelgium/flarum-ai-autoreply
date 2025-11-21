<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use GuzzleHttp\RequestOptions;

class AnthropicClient extends Platform
{
    public function completions(array $messages): ?string
    {
        if ($this->client === null)
            return null;

        $response = $this->client->post('https://api.anthropic.com/v1/messages', [
            RequestOptions::JSON => [
                'model' => $this->resolveModel(),
                'messages' => $messages,
                'max_tokens' => $this->maxTokens ?? 1024,
                'temperature' => $this->temperature ?? 1,
                'system' => $this->systemPrompt ?? '',
            ]
        ]);

        $json = json_decode((string)$response->getBody(), true);

        return $json['content'][0]['text'];
    }

    protected function getDefaultModel(): string
    {
        return 'claude-haiku-4-5';
    }

    protected function getHeaders(): array
    {
        return [
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
        ];
    }
}