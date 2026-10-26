<?php

namespace MichaelBelgium\FlarumAIAutoReply\Access;

use Flarum\Discussion\Discussion;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class DiscussionPolicy extends AbstractPolicy
{
    public function useChatGPTAssistant(User $actor, Discussion $discussion): bool
    {
        return $actor->hasPermission('discussion.useChatGPTAssistant');
    }
}
