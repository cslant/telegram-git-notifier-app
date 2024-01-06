# Welcome to Telegram Bot GitHub/GitLab Notify 👋

This package provides the ability to integrate the Telegram messaging service and GitHub/GitLab.
With this package,
you can create a Telegram bot to receive notifications from GitHub or GitLab events
and manage customization through messages and buttons on Telegram.

<p align="center">
  <img alt="GitHub and GitLab notifications to telegram php" src="https://github.com/cslant/telegram-git-notifier-app/assets/35853002/5da8b972-0072-4f7e-ba4b-a341898bb959" />
</p>

![License](https://img.shields.io/github/license/cslant/telegram-git-notifier-app.svg?style=flat-square)
[![Latest Version](https://img.shields.io/github/release/cslant/telegram-git-notifier-app.svg?style=flat-square)](https://github.com/cslant/telegram-git-notifier-app/releases)
![Test Status](https://img.shields.io/github/actions/workflow/status/cslant/telegram-git-notifier-app/setup_test.yml?label=tests&branch=main)
![Code Style Status](https://img.shields.io/github/actions/workflow/status/cslant/telegram-git-notifier-app/php-cs-fixer.yml?label=code%20style&branch=main)
[![StyleCI](https://styleci.io/repos/656960426/shield)](https://styleci.io/repos/656960426)
[![Quality Score](https://img.shields.io/scrutinizer/g/cslant/telegram-git-notifier-app.svg?style=flat-square)](https://scrutinizer-ci.com/g/cslant/telegram-git-notifier-app)
[![Maintainability](https://api.codeclimate.com/v1/badges/7ccaccebe9cd58ff3df5/maintainability)](https://codeclimate.com/github/cslant/telegram-git-notifier-app/maintainability)

## 📝 Information

- Send notifications of your GitHub/GitLab repositories to Telegram Bots, Groups, Super Groups (Multiple Topics), and Channels.
- The bot must be created using the [BotFather](https://core.telegram.org/bots#6-botfather)

## 🎉 Features

1. **GitHub/GitLab Notifications to Telegram**: The package allows you to configure a Telegram bot to receive notifications from various GitHub/GitLab events, including events like **commits, pull requests, issues, releases, and many more**.

<p align="center">
  <img alt="GitHub/GitLab Notifications to Telegram" src="https://github.com/cslant/telegram-git-notifier-app/assets/35853002/462f330f-11d3-43ef-89cf-c70ade57b654" />
</p>

2. **Customize Notifications**: You can customize the types of notifications you want to receive through options on Telegram.

[//]: # (features image)

3. **Interactive Buttons**: The package supports creating interactive buttons on Telegram to perform actions such as enabling or disabling notifications.

[//]: # (features image)

4. **Event Management**: You can manage specific events that you want to receive notifications for, allowing you to focus on what's most important for your projects.

   - Support for multiple platforms: GitHub and GitLab.
   - Manage event notifications separately between platforms.

<p align="center">
  <img alt="Event Management to Telegram" src="https://github.com/cslant/telegram-git-notifier-app/assets/35853002/e217a2ad-49b5-4936-a2cd-fe4af66e2bfb" />
</p>

5. **Easy Integration**:
   The package provides an API and user-friendly functions
   to create a Telegram bot and link it to your GitHub/GitLab account.

[//]: # (features image)

6. **Support for multiple chats**: You can add multiple chat IDs to the `.env` file. 
   These chat IDs will be the chat IDs of your groups, channels, or users.
   Also, you can add **the chat ID of this bot** to receive incoming notifications for itself.

[//]: # (features image)

7. **For premium users, you can use the following features:**
   - **Support for multiple topics**: You can add multiple topics to the `.env` file. 
     These topics will be the topics of your supergroups.

[//]: # (features image)

## 📋 Requirements

- PHP ^8.1
- [Composer](https://getcomposer.org/)
- Core: [Telegram Git Notifier](https://github.com/cslant/telegram-git-notifier)

## 🔧 Installation

As for the installation and configuration,
this project provides two different installation ways depending on your preference or suitability for your system.

> **Way 1:** Install by composer directly on the system (Requires the system to install composer, previous PHP version)
> 
> **Way 2:** Install by Docker (Requires the system to install Docker, Docker Compose)

### I. Installation and configuration 🛠

Please choose only one of the following two ways to set up the project.

<details open>

<summary><b>Way 1: Install by composer directly on the system ⚙</b></summary>
<br/>

First, please clone and install this project via [Composer](https://getcomposer.org/):

```bash
composer create-project cslant/telegram-git-notifier-app
```

After running the command above, you will have the project installed under the `telegram-git-notifier-app` directory,
and the environment file `.env` will be created automatically.

Some of the JSON files will be created automatically in the `storage` directory.
These files are used to store the data and serve for features in this bot.

#### 1. Create a New Bot

To create a new bot, you need to talk to [BotFather](https://core.telegram.org/bots#6-botfather) and follow a few simple steps.

1. Open a chat with [BotFather](https://telegram.me/botfather) and send `/newbot` command.
2. Enter a friendly name for your bot. This name will be displayed in contact details and elsewhere.
3. Enter a unique username for your bot. It must end in `bot`. Like this, for example: `TetrisBot` or `tetris_bot`.
4. Copy the HTTP API access token provided by [BotFather](https://telegram.me/botfather) and paste it into your `.env` file.

```dotenv
TELEGRAM_BOT_TOKEN=123456789:ABCDEFGHIJKLMNOPQRSTUVWXYZ
```

#### 2. Set up your domain and SSL certificate

**We recommend that you use HTTPS to set up your domain and webhook.**
You can build your own server or use a service like [Heroku](https://www.heroku.com/).

In this example, we will use localhost and [ngrok](https://ngrok.com/) to set up the domain and webhook:
1. Download and install [ngrok](https://ngrok.com/download).
2. Go to this project directory and run the command in the terminal: `php -S localhost:8000`
3. Continue to run the command in the terminal: `ngrok http 8000`
4. Copy the HTTPS URL provided by ngrok and paste it into your `.env` file.

```dotenv
TGN_APP_URL=https://123456789.ngrok.io
```

#### 3. Get your Chat ID and add it to the .env file

1. Open a chat with your bot.
2. Send any message to your bot. (This handle needs to get your chat ID)
3. Go to the following URL: `<TGN_APP_URL>/webhooks/getUpdate.php`
4. Look for the `"chat":{"id":` field and copy the number after it. This is your Chat ID.
5. Paste the Chat ID in your `.env` file.

```dotenv
TELEGRAM_BOT_CHAT_ID=123456789
```

At this time, the source launch process is done, please skip way 2 and go to step [II. Set the webhook](#ii-set-the-webhook) to continue.

</details>

---

<details>

<summary><b>Way 2: Install by Docker :whale:</b></summary>
<br/>

> **Note:** This way requires the system to install Docker and Docker Compose. 
> 
> ⚠ **If you set up the project by way one, please skip this way.** 🚸
> 
>And go to step [II. Set the webhook](#ii-set-the-webhook) to continue.

First, please clone this project and copy the environment file `.env.example` to `.env`:

```bash
git clone git@github.com:cslant/telegram-git-notifier-app.git
cd telegram-git-notifier-app
cp .env.example .env
```

#### 1. Update the environment variables

Open the `.env` file and update the following variables:

```dotenv
PHP_VERSION_SELECTED=8.2
APP_PORT=3180

# You can customize the container name to suit your needs using GitHub and GitLab
CONTAINER_NAME=tgn-app
```

> **Note:** 
> 
> - The `PHP_VERSION_SELECTED` variable is the PHP version you want to use in the container.
> - The `APP_PORT` variable is the port of the container. (Please don't set the same port as the host)
> - The `CONTAINER_NAME` variable is the name of the container you want to create.

#### 2. Install and run the container

Run the following command to install and run the container:

```shell
bash ./docker.sh
```

Some of the JSON files will be created automatically in the `storage` directory.
These files are used to store the data and serve for features in this bot.

#### 3. Create a New Bot

To create a new bot,
you need to talk to [BotFather](https://core.telegram.org/bots#6-botfather) and follow a few simple steps.

1. Open a chat with [BotFather](https://telegram.me/botfather) and send `/newbot` command.
2. Enter a friendly name for your bot. This name will be displayed in contact details and elsewhere.
3. Enter a unique username for your bot. It must end in `bot`. Like this, for example: `TetrisBot` or `tetris_bot`.
4. Copy the HTTP API access token provided by [BotFather](https://telegram.me/botfather) and paste it into your `.env` file.

```dotenv
TELEGRAM_BOT_TOKEN=123456789:ABCDEFGHIJKLMNOPQRSTUVWXYZ
```


#### 4. Set up your domain and SSL certificate

In this way, we use the proxy in the container and [ngrok](https://ngrok.com/) to set up the domain and webhook:
1. Check the proxy of the container: `docker inspect <CONTAINER_NAME>-nginx | grep IPAddress`

> **Note:** Replace `<CONTAINER_NAME>` with the name of the container in .env file.

Example:

![image](https://github.com/cslant/lemp-docker/assets/35853002/8dc8ba3f-b1e9-4bff-901d-6bb7747acda4)

2. Download and install [ngrok](https://ngrok.com/download).
2. Continue to run the command in the terminal: `ngrok http <CONTAINER_IP>`

> **Note:** Replace `<CONTAINER_IP>` is the IP address of the container in step 1.

Example:

```bash
ngrok http 172.28.0.3
```

3. Copy the HTTPS URL provided by ngrok and paste it into your `.env` file.

```dotenv
TGN_APP_URL=https://123456789.ngrok-free.app
```

#### 5. Get your Chat ID and add it to the .env file

1. Open a chat with your bot.
2. Send any message to your bot. (This handle needs to get your chat ID)
3. Go to the following URL: `<TGN_APP_URL>/webhooks/getUpdate.php`
4. Look for the `"chat":{"id":` field and copy the number after it. This is your Chat ID.
5. Paste the Chat ID in your `.env` file.

```dotenv
TELEGRAM_BOT_CHAT_ID=123456789
```

</details>

### II. Set the webhook

We have two ways to set the webhook:

#### 1. Set the webhook from this project

After setting up your domain and SSL certificate, you need to set up the webhook for your bot. 

**Go to:**

```text
<APP_URL>/webhooks/set.php
```

> **Note:** Replace `<APP_URL>` with your app URL in .env file.

If you see the following message, it means that the webhook has been sent successfully.

```json
{"ok":true,"result":true,"description":"Webhook was set"}
```

[//]: # (status image)

#### 2. Set the webhook manually from Telegram

If you want to set the webhook manually, you can use the following URL:

```url
https://api.telegram.org/bot<YourTelegramBotToken>/setWebhook?url=<APP_URL>
```

> **Note:** Replace `<YourTelegramBotToken>` with your bot token and `<APP_URL>` with your app URL in the `.env` file.

### III. Add chat IDs you want to receive notifications to the .env file

#### 1. Add multiple chat IDs to the `.env` file.

**These chat IDs will be the chat IDs of your groups, channels, or users.**

Also, you can add **the chat ID of this bot** to receive incoming notifications for itself.

```dotenv
TELEGRAM_NOTIFY_CHAT_IDS="-978339113;-1001933979183"
```

#### 2. Add a topic for supergroups with premium users (Thread ID)

You can add topic for supergroups with premium users (Thread ID).

**These topics will be the topics of your supergroups.**

```dotenv
TELEGRAM_NOTIFY_CHAT_IDS="-978339113;-1001933979183:topic_1;"
```

#### 3. Add multiple topics for supergroups with premium users (Thread IDs)

You can add multiple topics for each supergroup with premium users (Thread IDs).

```dotenv
TELEGRAM_NOTIFY_CHAT_IDS="-978339113;-1001933979183:topic_1,topic_2;"
```

> **Note:**
> 
> - Please use semicolon ";" to separate chat ids
>   - And use a colon ":" to separate chat ID and topic
>   - And use the comma "," if you want to add multiple topics

---

Now your configuration is complete. And the `.env` file will be like this:

```dotenv
TGN_APP_NAME='Telegram GitHub/GitLab Notify Bot'

# Set your app URL here (Required for the bot to work properly)
TGN_APP_URL=https://123456789.ngrok.io

TELEGRAM_BOT_TOKEN=6162840106:AAH3g20lMQIkG_wHHu8R_ngdtG541uzoq4
TELEGRAM_BOT_CHAT_ID=6872320129

# Set the chat IDs that will receive notifications here.
# You can add the owner bot ID, group id, ...
# -------------------------------------------------------
# Note:
# Please use semicolon ";" to separate chat ids
# And use a colon ":" to separate chat ID and thread ID
# And use comma "," if you want to add multiple thread ids
# -------------------------------------------------------
# The environment variable is expected to be in the format:
# "chat_id1;chat_id2:thread_id2;chat_id3:thread_id3_1,thread_id3_2;..."
TELEGRAM_NOTIFY_CHAT_IDS="-978339113;-1001933979183:2,13;6872320129"

TIMEZONE=Asia/Ho_Chi_Minh

PHP_VERSION_SELECTED=8.2
CONTAINER_NAME=tgn-bot
APP_PORT=3180
```

## 🚀 Usage

Now you can send a message to your bot, and you will receive a welcome message from the bot.

```text
/start
```

[//]: # (image)

If you want to check the menu, you can send the following message to your bot.

```text
/menu
```

🎊 At this point, the configuration process for your telegram bot is completed.
You can use all the features of this bot.
🎉🎉

<p align="center">
  <img alt="Menu features of this bot" src="https://github.com/cslant/telegram-git-notifier-app/assets/35853002/1a725130-c7c4-4594-9669-abc6d2dc1fba" />
</p>

**To increase ease of use. Let's create a menu with a list of commands listed for you.**

Please send the following message to your bot to create a menu button.

```text
/set_menu
```

<p align="center">
  <img alt="Set menu" src="https://github.com/cslant/telegram-git-notifier-app/assets/35853002/70f79e8f-b075-455d-b928-f721ca5b11cc" /> <img alt="Set menu" src="https://github.com/cslant/telegram-git-notifier-app/assets/35853002/53af5d51-7aa8-4dd8-99f6-3b55a9971cbe" />
</p>

Now you will need to add the Webhook for your GitHub and GitLab repository to receive notifications.

## 📌 Add webhook on your GitHub repository to receive notifications

1. Go to your repository settings.
2. Go to the `Webhooks` section.
3. Click on `Add webhook`.
4. Set `Payload URL` to `<APP_URL>`.
5. Set `Content type` to `application/x-www-form-urlencoded`.
6. Which events would you like to trigger this webhook? Select `Let me select individual events.`.
7. Click on the `Active` checkbox and Add webhook button.
8. Done. You will receive a notification when your repository has a new event.

Here is the first notification you will receive:

<p align="center">
  <img alt="Github ping event notification" src="https://github.com/cslant/telegram-git-notifier-app/assets/35853002/66b7fffa-d2fa-41f6-8caa-3c1ab96b63be" />
</p>

## 📌 Add a webhook on your GitLab repository to receive notifications

1. Go to your repository settings.
2. Go to the `Webhooks` section.
3. Click on `Add new webhook`.
4. Set `URL` to `<APP_URL>`.
5. Choose any `Trigger` you want.
6. Click on the `Enable SSL verification` checkbox and Add webhook button.

> **Note: You can set up this webhook for different repositories. Please similarly set up the webhook for each repository.**

Then every time one of those repositories appears an event, this telegram bot will immediately send you a notification. 
