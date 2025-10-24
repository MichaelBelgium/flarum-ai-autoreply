<?php

namespace MichaelBelgium\FlarumAIAutoReply\Listeners;

use Flarum\Post\Event\Posted;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MichaelBelgium\FlarumAIAutoReply\AnthropicClient;
use MichaelBelgium\FlarumAIAutoReply\GoogleClient;
use MichaelBelgium\FlarumAIAutoReply\Job\Reply;
use MichaelBelgium\FlarumAIAutoReply\OpenAIClient;
use MichaelBelgium\FlarumAIAutoReply\OpenrouterClient;
use Psr\Log\LoggerInterface;

class ReplyOnPost
{
    private string $platform;

    public function __construct(
        protected Queue $queue,
        protected SettingsRepositoryInterface $settings,
        protected LoggerInterface $logger,
        protected OpenAIClient $openAIClient,
        protected AnthropicClient $anthropicClient,
        protected OpenrouterClient $openrouterClient,
        protected GoogleClient $googleClient,
    ) {
        $this->platform = $this->settings->get('michaelbelgium-ai-autoreply.platform');
    }

    public function handle(Posted $event): void
    {
        $discussion = $event->post->discussion;
        $enabledTagIds = $this->settings->get('michaelbelgium-ai-autoreply.enabled-tags', '[]');

        if ($enabledTagIds = json_decode($enabledTagIds, true))
        {
            $tagIds = Arr::pluck($discussion->tags, 'id');

            if (!array_intersect($enabledTagIds, $tagIds))
                return;
        }

        $event->actor->assertCan('useChatGPTAssistant', $discussion);

        $posts = $discussion->posts;
        $replyOnDiscussionStart = $this->settings->get('michaelbelgium-ai-autoreply.enable_on_discussion_started', true);
        $assistantId = $this->settings->get('michaelbelgium-ai-autoreply.user_prompt');

        if (empty($assistantId))
        {
            $this->logger->error('AI assistant: No assistant user set');
            return;
        }

        $assistant = User::find($assistantId);

        if ($assistant === null)
        {
            $this->logger->error("AI assistant: No assistant user found with ID $assistantId");
            return;
        }

        if ($posts->count() == 1)
            $op = $event->actor->id; //$discussion->firstPost is null when discussion is started :(
        else
        {
            if ($replyOnDiscussionStart)
                return; //only reply on discussion start, not on subsequent posts

            $op = $discussion->firstPost->user->id;

            if ($op != $event->actor->id)
                return; //only reply to posts made by OP
        }

        $conversation = $this->postsToAiMessages(
            $posts->whereIn('user_id', [$op, $assistantId])->collect()
        );

        $this->queue->push(new Reply(
            $this->platform,
            $discussion->id,
            $assistantId,
            $conversation->toArray())
        );
    }

    private function postsToAiMessages(Collection $posts): Collection
    {
        $messages = new Collection();

        /** @var Post $post */
        foreach ($posts as $post)
        {
            $messages->add([
                'role' => $post->user->id == $this->settings->get('michaelbelgium-ai-autoreply.user_prompt') ? 'assistant' : 'user',
                'content' => $post->content,
            ]);
        }

        return $messages;
    }
}
