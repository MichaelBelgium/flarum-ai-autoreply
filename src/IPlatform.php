<?php

namespace MichaelBelgium\FlarumAIAutoReply;

interface IPlatform
{
    public function completions(array $messages): ?string;
}