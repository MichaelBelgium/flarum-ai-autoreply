<?php

namespace MichaelBelgium\FlarumAIAutoReply\Listeners;

use Carbon\Carbon;
use MichaelBelgium\FlarumAIAutoReply\AnthropicClient;
use MichaelBelgium\FlarumAIAutoReply\IPlatform;
use MichaelBelgium\FlarumAIAutoReply\OpenAIClient;
use Flarum\Discussion\Event\Started;
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class PostAiAnswer
{
    public function __construct(
        protected Dispatcher $events,
        protected SettingsRepositoryInterface $settings,
        protected OpenAIClient $openAIClient,
        protected AnthropicClient $anthropicClient
    ) {
    }

    public function handle(Started $event): void
    {
        if (! $this->settings->get('michaelbelgium-ai-autoreply.enable_on_discussion_started', true)) {
            return;
        }

        $discussion = $event->discussion;
        $actor = $event->actor;
        $enabledTagIds = $this->settings->get('michaelbelgium-ai-autoreply.enabled-tags', '[]');

        if ($enabledTagIds = json_decode($enabledTagIds, true)) {
            $tagIds = Arr::pluck($discussion->tags, 'id');

            if (! array_intersect($enabledTagIds, $tagIds)) {
                return;
            }
        }

        if ($userId = $this->settings->get('michaelbelgium-ai-autoreply.user_prompt')) {
            $user = User::find($userId);
        }

        $actor->assertCan('useChatGPTAssistant', $discussion);

        $platform = $this->settings->get('michaelbelgium-ai-autoreply.platform', 'openai');

        /** @var IPlatform $client */
        $client = match($platform) {
            'anthropic' => $this->anthropicClient,
            default => $this->openAIClient,
        };

        $content = $client->completions($discussion->firstPost->content);

        if (empty($content)) {
            return;
        }

        $post = CommentPost::reply(
            $discussion->id,
            $content,
            $user?->id ?? $actor->id,
            null,
        );

        $post->created_at = Carbon::now();

        $post->save();
    }
}
