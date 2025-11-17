<?php

namespace App\Services;

class MessageHandlerService
{
    public function handleMessage(array $requestData): bool
    {
        $chatId = $requestData['message']['chat']['id'];
		$userQuestion = $requestData['message']['text'];

		$contentsService = new ContentsService();
        $geminiService = new GeminiService();
        
		$context = $contentsService->prepareContext($chatId, $userQuestion);

		$gptResponse = $geminiService->makeGptRequest($context);
		$gptResponseArray = $gptResponse->json();

		$contentsService->addGptResponseToContext($chatId, $userQuestion, $gptResponseArray);

		$telegramService = new TelegramService();
        $text = $gptResponseArray['candidates'][0]['content']['parts'][0]['text'];
		$telegramService->sendMessage($chatId, $text);

        return true;
    }
}