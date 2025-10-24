<?php

namespace MichaelBelgium\FlarumAIAutoReply\Job;

use Flarum\Post\CommentPost;
use Flarum\Queue\AbstractJob;
use MichaelBelgium\FlarumAIAutoReply\AnthropicClient;
use MichaelBelgium\FlarumAIAutoReply\GoogleClient;
use MichaelBelgium\FlarumAIAutoReply\IPlatform;
use MichaelBelgium\FlarumAIAutoReply\OpenAIClient;
use MichaelBelgium\FlarumAIAutoReply\OpenrouterClient;

class Reply extends AbstractJob
{
    public function __construct(
        private readonly string $platform,
        private readonly int $discussionId,
        private readonly int $assistantId,
        private readonly array $conversation,
    ) {
    }

    public function handle(
        OpenAIClient $openAIClient,
        AnthropicClient $anthropicClient,
        OpenrouterClient $openrouterClient,
        GoogleClient $googleClient,
    ) {
        /** @var IPlatform $client */
        $client = match($this->platform) {
            'anthropic' => $anthropicClient,
            'openrouter' => $openrouterClient,
            'google' => $googleClient,
            default => $openAIClient,
        };

        $content = $client->completions($this->conversation);

        $post = CommentPost::reply(
            $this->discussionId,
            $content,
            $this->assistantId,
            null,
        );

        $post->save();
    }
}