# AI-powered Auto-Reply Extension for Flarum

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/michaelbelgium/flarum-ai-autoreply.svg)](https://packagist.org/packages/michaelbelgium/flarum-ai-autoreply) [![Total Downloads](https://img.shields.io/packagist/dt/michaelbelgium/flarum-ai-autoreply.svg)](https://packagist.org/packages/michaelbelgium/flarum-ai-autoreply)

A [Flarum](http://flarum.org) extension.

This extension includes an auto-reply discussion feature, customizable max tokens, and permission controls who can use this feature.

This extension is a fixed fork from [flarum-chatgpt](https://github.com/datlechin/flarum-chatgpt) with support for multiple platforms. Currently supporting:
- OpenAI
- Anthropic
- OpenRouter
- Google

Models from any of the supported platforms can be used. If you're using OpenRouter, you can specify a comma-separated list of models in the `model` setting to take advantage of its [model routing feature](https://openrouter.ai/docs/features/model-routing#the-models-parameter).

The auto-answer feature uses the OpenAI gpt-5-mini model by default to generate quick and accurate responses to users' questions. AI can respond on discussion start or a discussion can act as chat between user and AI.

## Installation

This extension requires **Flarum >= 1.8** and **PHP 8.2**.

Install with composer:

```sh
composer require michaelbelgium/flarum-ai-autoreply
```

## Updating

```sh
composer update michaelbelgium/flarum-ai-autoreply
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/michaelbelgium/flarum-ai-autoreply)
- [GitHub](https://github.com/michaelbelgium/flarum-ai-autoreply)
- [Discuss](https://discuss.flarum.org/d/38244)
