<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContentsService;
use App\Services\MessageHandlerService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Repositories\ValidatorsRepository;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

class MainController extends BaseController
{
    private MessageHandlerService $messageHandlerService;

    public function __construct()
    {
        $this->messageHandlerService = new MessageHandlerService();
    }

    public function telegramWebhook(Request $request)
    {
        try {
            $requestData = $request->json()->all();
            Log::info('gptResponseArray', ['response' => $requestData]);

            $validatorRequest = Validator::make($requestData, ValidatorsRepository::getTelegramRequestRules());
            if ($validatorRequest->fails()) {
                throw new ValidationException($validatorRequest);
            }

            $this->messageHandlerService->handleMessage($requestData);

            return response()->json(['message' => 'Message handled successfully']);
        } catch (\Exception $e) {
            Log::error('Error in telegramWebhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->json()->all() ?? null
            ]);

            return response()->json(['error' => 'An error occurred while processing the request'], 500);
        }
    }
}
