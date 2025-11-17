<?php

namespace App\Repositories;

use App\Models\UserContent;

class UserContentRepository
{
    public function getUserContent(int $chatId): ?array
    {
        $userContent = UserContent::where('chat_id', $chatId)->first();
        return $userContent ? $userContent->context : null;
    }

    public function updateContextByChatId(int $chatId, $context): bool
    {
        $userContent = UserContent::where('chat_id', $chatId)->first();
        if ($userContent) {
            $userContent->context = $context;
            return $userContent->save();
        }
        
        // Если записи нет, создаем новую
        $userContent = new UserContent();
        $userContent->chat_id = $chatId;
        $userContent->context = $context;
        return $userContent->save();
    }
}