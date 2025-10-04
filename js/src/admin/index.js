import app from 'flarum/admin/app';

app.initializers.add('michaelbelgium/flarum-ai-autoreply', () => {
    app.extensionData
        .for('michaelbelgium-ai-autoreply')
        .registerSetting({
            setting: 'michaelbelgium-ai-autoreply.platform',
            type: 'dropdown',
            options: {openai: 'Open AI', anthropic: 'Anthropic', openrouter: 'OpenRouter'},
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.platform_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.platform_help'),
        })
        .registerSetting({
            setting: 'michaelbelgium-ai-autoreply.api_key',
            type: 'text',
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.api_key_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.api_key_help', {
                a: <a href="https://platform.openai.com/account/api-keys" target="_blank" rel="noopener" />,
            }),
            placeholder: 'sk-...',
        })
        .registerSetting({
            setting: 'michaelbelgium-ai-autoreply.model',
            type: 'text',
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.model_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.model_help', {
                openai: <a href="https://platform.openai.com/docs/models/overview" target="_blank" rel="noopener">Open AI models</a>,
                anthropic: <a href="https://docs.claude.com/en/docs/about-claude/models/overview" target="_blank" rel="noopener">Anthropic models</a>,
                openrouter: <a href="https://openrouter.ai/models" target="_blank" rel="noopener">OpenRouter models</a>,
            }),
        })
        .registerSetting({
            setting: 'michaelbelgium-ai-autoreply.max_tokens',
            type: 'number',
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.max_tokens_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.max_tokens_help', {
                a: <a href="https://help.openai.com/en/articles/4936856" target="_blank" rel="noopener" />,
            }),
        })
        .registerSetting({
            setting: 'michaelbelgium-ai-autoreply.user_prompt',
            type: 'text',
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.user_prompt_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.user_prompt_help'),
        })
        .registerSetting({
            setting: 'michaelbelgium-ai-autoreply.user_prompt_badge_text',
            type: 'text',
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.user_prompt_badge_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.user_prompt_badge_help'),
        })
        .registerSetting({
            setting: 'michaelbelgium-ai-autoreply.enable_on_discussion_started',
            type: 'boolean',
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.enable_on_discussion_started_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.enable_on_discussion_started_help'),
        })
        .registerSetting({
            type: 'flarum-tags.select-tags',
            setting: 'michaelbelgium-ai-autoreply.enabled-tags',
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.enabled_tags_label'),
            help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.enabled_tags_help'),
            options: {
                requireParentTag: false,
                limits: {
                    max: {
                        secondary: 0,
                    },
                },
            },
        })
        .registerPermission({
            label: app.translator.trans('michaelbelgium-ai-autoreply.admin.permissions.use_chatgpt_assistant_label'),
            icon: 'fas fa-comment',
            permission: 'discussion.useChatGPTAssistant',
            allowGuest: false,
        }, 'start');
});
