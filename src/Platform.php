<?php

namespace MichaelBelgium\FlarumAIAutoReply;

use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;

abstract class Platform implements IPlatform
{
    protected ?Client $client = null;
    protected ?string $apiKey = null;
    protected ?int $maxTokens = null;
    private ?string $model = null;
    protected ?float $temperature = null;
    protected ?string $systemPrompt = null;

    public function __construct(
        protected SettingsRepositoryInterface $settings,
        protected LoggerInterface $logger
    ) {
        if($this->loadConfiguration())
        {
            $this->client = new Client([
                RequestOptions::HEADERS => $this->getHeaders()
            ]);
        }
    }

    abstract protected function getDefaultModel(): string;
    abstract protected function getHeaders(): array;

    protected function resolveModel(): string
    {
        return $this->model ?? $this->getDefaultModel();
    }

    private function loadConfiguration(): bool
    {
        $apiKey = $this->settings->get('michaelbelgium-ai-autoreply.api_key');

        if (empty($apiKey))
        {
            $this->logger->error('[AI-AutoReply] API key is not set.');
            return false;
        }

        $model = $this->settings->get('michaelbelgium-ai-autoreply.model');
        $temperature = $this->settings->get('michaelbelgium-ai-autoreply.temperature');
        $systemPrompt = $this->settings->get('michaelbelgium-ai-autoreply.system_prompt');
        $tokens = $this->settings->get('michaelbelgium-ai-autoreply.max_tokens');

        $this->apiKey = $apiKey;

        if (!empty($tokens))
            $this->maxTokens = (int)$tokens;

        if (!empty($model))
            $this->model = $model;

        if (!empty($temperature))
            $this->temperature = (float)$temperature;

        if (!empty($systemPrompt))
            $this->systemPrompt = $systemPrompt;

        return true;
    }
}