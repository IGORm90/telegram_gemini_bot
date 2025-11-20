<?php

namespace App\Services;

class MessageHandlerService
{
    private TelegramService $telegramService;

    public function __construct()
    {
        $this->telegramService = new TelegramService();
    }

    public function handleMessage(array $requestData): bool
    {
        $chatId = $requestData['message']['chat']['id'] ?? null;
		$userQuestion = $requestData['message']['text'] ?? null;

		if ($chatId === null || $userQuestion === null) {
			$this->telegramService->sendErrorMessage($chatId);
            return false;
		}

		$contentsService = new ContentsService();
        $geminiService = new GeminiService();
        
		$context = $contentsService->prepareContext($chatId, $userQuestion);

		$gptResponse = $geminiService->makeGptRequest($context);
		$gptResponseArray = $gptResponse->json();

		$contentsService->addGptResponseToContext($chatId, $userQuestion, $gptResponseArray);

        $text = $gptResponseArray['candidates'][0]['content']['parts'][0]['text'] ?? null;

		if ($text === null) {
			$this->telegramService->sendErrorMessage($chatId);
            return false;
		}

		$this->telegramService->sendMessage($chatId, $text);

        return true;
    }
}