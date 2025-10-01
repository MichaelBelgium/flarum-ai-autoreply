<?php

namespace MichaelBelgium\FlarumAIAutoReply;

interface IPlatform
{
    public function completions(string $content): ?string;
}