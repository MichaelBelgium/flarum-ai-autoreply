import app from 'flarum/admin/app';
import Select from 'flarum/common/components/Select';

const models = {
    openai: {
        name: 'Open AI',
        modelsUrl: 'https://platform.openai.com/docs/models/overview',
        keysUrl: 'https://platform.openai.com/account/api-keys',
        defaultModel: 'gpt-5-mini',
    },
    anthropic: {
        name: 'Anthropic',
        modelsUrl: 'https://docs.claude.com/en/docs/about-claude/models/overview',
        keysUrl: 'https://console.anthropic.com/settings/keys',
        defaultModel: 'claude-sonnet-4-5'
    },
    openrouter: {
        name: 'OpenRouter',
        modelsUrl: 'https://openrouter.ai/models',
        keysUrl: 'https://openrouter.ai/settings/keys',
        defaultModel: 'openrouter/auto',
    },
};

const modelNames = Object.entries(models).reduce((result, [key, value]) => {
    result[key] = value.name;
    return result;
}, {});


app.initializers.add('michaelbelgium/flarum-ai-autoreply', () => {
    const savedPlatform = app.data.settings['michaelbelgium-ai-autoreply.platform'] || 'openai';
    let selectedModel = models[savedPlatform];

    app.extensionData
        .for('michaelbelgium-ai-autoreply')
        // .registerSetting({
        //     setting: 'michaelbelgium-ai-autoreply.platform',
        //     type: 'dropdown',
        //     options: modelNames,
        //     label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.platform_label'),
        //     help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.platform_help'),
        // })
        .registerSetting(function () {
            return m('.Form-group', [
                m('label', app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.platform_label')),
                m('.helpText', app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.platform_help')),
                Select.component({
                    value: this.setting('michaelbelgium-ai-autoreply.platform')(),
                    options: modelNames,
                    onchange: (value) => {
                        selectedModel = models[value];
                        this.setting('michaelbelgium-ai-autoreply.platform')(value);
                    }
                })
            ]);
        })
        // .registerSetting({
        //     setting: 'michaelbelgium-ai-autoreply.api_key',
        //     type: 'text',
        //     label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.api_key_label'),
        //     help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.api_key_help', {
        //         a: <a href={selectedModel.keysUrl} target="_blank" rel="noopener" />,
        //         platform: selectedModel.name,
        //     }),
        //     placeholder: 'sk-...',
        //     required: true,
        // })
        // .registerSetting({
        //     setting: 'michaelbelgium-ai-autoreply.model',
        //     type: 'text',
        //     label: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.model_label'),
        //     help: app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.model_help', {
        //         a: <a href={selectedModel.modelsUrl} target="_blank" rel="noopener" />,
        //         platform: selectedModel.name,
        //     }),
        //     required: true
        // })
        .registerSetting(function () {
            return m('.Form-group', [
                m('label', app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.api_key_label')),
                m('.helpText', app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.api_key_help', {
                    a: <a href={selectedModel.keysUrl} target="_blank" rel="noopener" />,
                    platform: selectedModel.name,
                })),
                m('input.FormControl', {
                    type: 'text',
                    bidi: this.setting('michaelbelgium-ai-autoreply.api_key'),
                    placeholder: 'sk-...',
                    required: true,
                }),
            ]);
        })
        .registerSetting(function () {
            return m('.Form-group', [
                m('label', app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.model_label')),
                m('.helpText', app.translator.trans('michaelbelgium-ai-autoreply.admin.settings.model_help', {
                    a: <a href={selectedModel.modelsUrl} target="_blank" rel="noopener" />,
                    platform: selectedModel.name,
                    model: selectedModel.defaultModel,
                })),
                m('input.FormControl', {
                    type: 'text',
                    bidi: this.setting('michaelbelgium-ai-autoreply.model'),
                    required: true,
                }),
            ]);
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
            type: 'number',
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
