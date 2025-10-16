<?php

namespace MichaelBelgium\FlarumAIAutoReply\Listeners;

use Carbon\Carbon;
use Flarum\Discussion\Event\Started;
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use MichaelBelgium\FlarumAIAutoReply\AnthropicClient;
use MichaelBelgium\FlarumAIAutoReply\GoogleClient;
use MichaelBelgium\FlarumAIAutoReply\IPlatform;
use MichaelBelgium\FlarumAIAutoReply\OpenAIClient;
use MichaelBelgium\FlarumAIAutoReply\OpenrouterClient;
use Psr\Log\LoggerInterface;

class ReplyOnDiscussionStart
{
    public function __construct(
        protected Dispatcher $events,
        protected SettingsRepositoryInterface $settings,
        protected LoggerInterface $logger,
        protected OpenAIClient $openAIClient,
        protected AnthropicClient $anthropicClient,
        protected OpenrouterClient $openrouterClient,
        protected GoogleClient $googleClient,
    ) {
    }

    public function handle(Started $event): void
    {
        if (!$this->settings->get('michaelbelgium-ai-autoreply.enable_on_discussion_started', true)) {
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

            if ($user === null) {
                $this->logger->warning('No assistant user found with ID ' . $userId);
                return;
            }
        }

        $actor->assertCan('useChatGPTAssistant', $discussion);

        $platform = $this->settings->get('michaelbelgium-ai-autoreply.platform');

        /** @var IPlatform $client */
        $client = match($platform) {
            'anthropic' => $this->anthropicClient,
            'openrouter' => $this->openrouterClient,
            'google' => $this->googleClient,
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
