<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContentsService;
use App\Services\MessageHandlerService;
use Illuminate\Support\Facades\Validator;
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
        $requestData = $request->json()->all();
		$validatorRequest = Validator::make($requestData, ValidatorsRepository::getTelegramRequestRules());
		if ($validatorRequest->fails()) {
			throw new ValidationException($validatorRequest);
		}

		$this->messageHandlerService->handleMessage($requestData);

        return response()->json(['message' => 'Message handled successfully']);
    }
}
