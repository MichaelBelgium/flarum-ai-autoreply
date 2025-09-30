import app from 'flarum/admin/app';

app.initializers.add('michaelbelgium/flarum-chatgpt', () => {
    app.extensionData
        .for('michaelbelgium-chatgpt')
        .registerSetting({
            setting: 'datlechin-chatgpt.api_key',
            type: 'text',
            label: app.translator.trans('datlechin-chatgpt.admin.settings.api_key_label'),
            help: app.translator.trans('datlechin-chatgpt.admin.settings.api_key_help', {
                a: <a href="https://platform.openai.com/account/api-keys" target="_blank" rel="noopener" />,
            }),
            placeholder: 'sk-...',
        })
        .registerSetting({
            setting: 'datlechin-chatgpt.model',
            type: 'text',
            label: app.translator.trans('datlechin-chatgpt.admin.settings.model_label'),
            help: app.translator.trans('datlechin-chatgpt.admin.settings.model_help', {
                a: <a href="https://platform.openai.com/docs/models/overview" target="_blank" rel="noopener" />,
            }),
        })
        .registerSetting({
            setting: 'datlechin-chatgpt.max_tokens',
            type: 'number',
            label: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_label'),
            help: app.translator.trans('datlechin-chatgpt.admin.settings.max_tokens_help', {
                a: <a href="https://help.openai.com/en/articles/4936856" target="_blank" rel="noopener" />,
            }),
            default: 100,
        })
        .registerSetting({
            setting: 'datlechin-chatgpt.user_prompt',
            type: 'text',
            label: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_label'),
            help: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_help'),
        })
        .registerSetting({
            setting: 'datlechin-chatgpt.user_prompt_badge_text',
            type: 'text',
            label: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_label'),
            help: app.translator.trans('datlechin-chatgpt.admin.settings.user_prompt_badge_help'),
        })
        .registerSetting({
            setting: 'datlechin-chatgpt.enable_on_discussion_started',
            type: 'boolean',
            label: app.translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_label'),
            help: app.translator.trans('datlechin-chatgpt.admin.settings.enable_on_discussion_started_help'),
        })
        .registerSetting({
            type: 'flarum-tags.select-tags',
            setting: 'datlechin-chatgpt.enabled-tags',
            label: app.translator.trans('datlechin-chatgpt.admin.settings.enabled_tags_label'),
            help: app.translator.trans('datlechin-chatgpt.admin.settings.enabled_tags_help'),
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
            label: app.translator.trans('datlechin-chatgpt.admin.permissions.use_chatgpt_assistant_label'),
            icon: 'fas fa-comment',
            permission: 'discussion.useChatGPTAssistant',
            allowGuest: false,
        }, 'start');
});
