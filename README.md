# AI-powered Auto-Reply Extension for Flarum

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/michaelbelgium/flarum-ai-autoreply.svg)](https://packagist.org/packages/michaelbelgium/flarum-ai-autoreply) [![Total Downloads](https://img.shields.io/packagist/dt/michaelbelgium/flarum-ai-autoreply.svg)](https://packagist.org/packages/michaelbelgium/flarum-ai-autoreply)

A [Flarum](http://flarum.org) extension.

The extension for Flarum includes an auto-reply discussion feature, customizable max tokens, and permission controls who can use this feature.

The auto-answer feature uses the OpenAI gpt-5-mini model by default to generate quick and accurate responses to users' questions.

![](https://user-images.githubusercontent.com/56961917/224526200-4aee65bf-59df-4892-b23d-aab644238101.gif)

## Installation

This extension requierd **Flarum >= 1.7** and **PHP 8.2**.

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
- [Discuss](https://discuss.flarum.org/d/32535)
