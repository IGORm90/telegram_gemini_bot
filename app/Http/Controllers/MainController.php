<?php

namespace App\Http\Controllers;

use App\Services\LogService;
use Illuminate\Http\Request;
use App\Services\MessageHandlerService;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ValidatorsRepository;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

class MainController extends BaseController
{
    private MessageHandlerService $messageHandlerService;
    private LogService $logService;

    public function __construct()
    {
        $this->messageHandlerService = new MessageHandlerService();
        $this->logService = new LogService();
    }

    public function telegramWebhook(Request $request)
    {
        try {
            $requestData = $request->json()->all();
            $this->logService->infoLog('gptResponseArray', ['response' => $requestData]);

            $validatorRequest = Validator::make($requestData, ValidatorsRepository::getTelegramRequestRules());
            if ($validatorRequest->fails()) {
                throw new ValidationException($validatorRequest);
            }

            $this->messageHandlerService->handleMessage($requestData);

            return response()->json(['message' => 'Message handled successfully']);
        } catch (\Exception $e) {
            $this->logService->errorLog('Error in telegramWebhook', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->json()->all() ?? null
            ]);

            return response()->json(['error' => 'An error occurred while processing the request'], 500);
        }
    }
}
