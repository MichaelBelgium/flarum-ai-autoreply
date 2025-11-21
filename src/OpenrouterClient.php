<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use GuzzleHttp\RequestOptions;

class OpenrouterClient extends Platform
{
    public function completions(array $messages): ?string
    {
        if ($this->client === null)
            return null;

        $options = [
            'messages' => $messages
        ];

        if (str_contains(',', $this->resolveModel()))
            $options['models'] = explode(',', $this->resolveModel());
        else
            $options['model'] = $this->resolveModel();

        if ($this->maxTokens !== null)
            $options['max_tokens'] = $this->maxTokens;

        if ($this->temperature !== null)
            $options['temperature'] = $this->temperature;

        $response = $this->client->post('https://openrouter.ai/api/v1/chat/completions', [
            RequestOptions::JSON => $options
        ]);

        $json = json_decode((string)$response->getBody(), true);

        return $json['choices'][0]['message']['content'];
    }

    protected function getDefaultModel(): string
    {
        return 'openrouter/auto';
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ];
    }
}