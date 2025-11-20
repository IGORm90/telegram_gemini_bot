<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use RuntimeException;

class TelegramService
{
    private string $telegramBotToken;
    private HttpService $httpService;

    public function __construct()
    {
        $this->telegramBotToken = (string) env('TELEGRAM_BOT_TOKEN');

        if ($this->telegramBotToken === '') {
            throw new RuntimeException('Telegram bot token is not configured.');
        }

        $this->httpService = new HttpService();
    }

    public function sendMessage(int|string $chatId, string $text, array $options = []): Response
    {
        $payload = Arr::only($options, [
            'business_connection_id',
            'message_thread_id',
            'parse_mode',
            'entities',
            'link_preview_options',
            'disable_notification',
            'protect_content',
            'allow_paid_broadcast',
            'message_effect_id',
            'reply_parameters',
            'reply_markup',
            'direct_messages_topic_id',
            'suggested_post_parameters',
        ]);

        $payload['chat_id'] = $chatId;
        $payload['text'] = $text;

        $url = sprintf('https://api.telegram.org/bot%s/sendMessage', $this->telegramBotToken);

        return $this->httpService->post($url, $payload);
    }

    public function sendErrorMessage(int|string $chatId, array $options = []): Response
    {
        $errorText = "Произошла ошибка при обработке запроса!";
        
        return $this->sendMessage($chatId, $errorText, $options);
    }
}