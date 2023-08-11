# Welcome to Telegram Bot GitHub Notify 👋

[![Latest Version](https://img.shields.io/github/release/lbiltech/telegram-bot-github-notify.svg?style=flat-square)](https://github.com/lbiltech/telegram-bot-github-notify/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/lbiltech/telegram-bot-github-notify.svg?style=flat-square)](https://packagist.org/packages/lbiltech/telegram-bot-github-notify)
[![StyleCI](https://styleci.io/repos/656960426/shield)](https://styleci.io/repos/656960426)
[![Quality Score](https://img.shields.io/scrutinizer/g/lbiltech/telegram-bot-github-notify.svg?style=flat-square)](https://scrutinizer-ci.com/g/lbiltech/telegram-bot-github-notify)
[![Maintainability](https://api.codeclimate.com/v1/badges/460e2b6fba334a156b2e/maintainability)](https://codeclimate.com/github/lbiltech/telegram-bot-github-notify/maintainability)

## Information

- Send GitHub notifications of your repositories to Telegram Bots, Groups, and Channels.
- The bot must be created using the [BotFather](https://core.telegram.org/bots#6-botfather)

## Requirement

- PHP ^8.0
- Composer
- Telegram Bot

## Installation

First, please clone and install this project via [Composer](https://getcomposer.org/):

```bash
composer create-project lbiltech/telegram-bot-github-notify
```

After running the command above, you will have the project installed under `telegram-bot-github-notify` directory,
and the environment file `.env` will be created automatically.

### Create a New Bot

To create a new bot, you need to talk to [BotFather](https://core.telegram.org/bots#6-botfather) and follow a few simple steps.

1. Open a chat with [BotFather](https://telegram.me/botfather) and send `/newbot` command.
2. Enter a friendly name for your bot. This name will be displayed in contact details and elsewhere.
3. Enter a unique username for your bot. It must end in `bot`. Like this, for example: `TetrisBot` or `tetris_bot`.
4. Copy the HTTP API access token provided by [BotFather](https://telegram.me/botfather) and paste it into your `.env` file.

```shell
TELEGRAM_BOT_TOKEN=123456789:ABCDEFGHIJKLMNOPQRSTUVWXYZ
```

### Get your Chat ID and add it to the .env file

1. Open a chat with your bot.
2. Send a message to your bot.
3. Go to the following URL: `https://api.telegram.org/bot<YourBOTToken>/getUpdates`
4. Look for the `"chat":{"id":` field and copy the number after it. This is your Chat ID.
5. Paste the Chat ID in your `.env` file.

```shell
TELEGRAM_BOT_CHAT_ID=123456789
```

### Set up your domain and SSL certificate

**You need to set up your domain and SSL certificate to use the webhook**. You can build your own server or use a service like [Heroku](https://www.heroku.com/).

In this example, we will use localhost and ngrok to set up domain and webhook:
1. Download and install [ngrok](https://ngrok.com/download).
2. Run command in terminal: `php -S localhost:8000`
3. Run command in terminal: `ngrok http 8000`
4. Copy the HTTPS URL provided by ngrok and paste it into your `.env` file.

```shell
APP_URL=https://123456789.ngrok.io
```

### Set the webhook

We have two ways to set the webhook:

#### 1. Set the webhook from this project

After setting up your domain and SSL certificate, you need to set up the webhook for your bot. Go to:

```text
<APP_URL>/webhook/set.php
```

> **Note:** Replace `<APP_URL>` with your app URL in .env file.

If you see the following message, it means that the webhook has been sent successfully.

```json
{"ok":true,"result":true,"description":"Webhook was set"}
```

#### 2. Set the webhook manually

If you want to set the webhook manually, you can use the following URL:

```text
https://api.telegram.org/bot<YourBOTToken>/setWebhook?url=<APP_URL>
```

> **Note:** Replace `<YourBOTToken>` with your bot token and `<APP_URL>` with your app URL in .env file.

### Add chat ids you want to receive notifications to the .env file

```shell
TELEGRAM_NOTIFY_CHAT_IDS="-978339113,-1001933979183"
```

Now your configuration is complete. And it will be like this:

```shell
APP_NAME='Telegram Github Notify Bot'

# Set your app URL here
APP_URL=https://123456789.ngrok.io

TELEGRAM_BOT_TOKEN=6162840106:AAH3g20lMQIkG_wHHu8R_ngdtG541uzoq4
TELEGRAM_BOT_CHAT_ID=6872320129

# Set your telegram group chat ids here ( please use comma to separate )
TELEGRAM_NOTIFY_CHAT_IDS="-978339113,-1001933979183"

TIMEZONE=Asia/Ho_Chi_Minh
```

## Usage

Now you can send a message to your bot, and you will receive a notification.

```text
/start
```

## Set webhook on your GitHub repository

1. Go to your repository settings
2. Go to the `Webhooks` section
3. Click on `Add webhook`
4. Set `Payload URL` to `<APP_URL>`
5. Set `Content type` to `application/x-www-form-urlencoded`
6. Which events would you like to trigger this webhook? Select `Let me select individual events.`
7. Click on the `Active` checkbox and Add webhook button.
8. Done. You will receive a notification when your repository has a new event.

Here is the first notification you will receive: ♻️ **Connection Successful**

> **Note: You can add multiple webhooks to your repository. Please similarly set up the webhook for each repository.**
