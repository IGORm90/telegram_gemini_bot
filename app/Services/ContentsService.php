<?php

namespace App\Services;

use App\Repositories\UserContentRepository;
use Illuminate\Support\Carbon;

class ContentsService
{
    protected $userContentRepository;
    private const CONTEXT_LIMIT = 3;

    public function __construct()
    {
        $this->userContentRepository = new UserContentRepository();
    }

    public function getContextByChatId(int $chatId): ?array
    {
        $userContent = $this->userContentRepository->getUserContent($chatId);
        
        if ($userContent === null) {
            return null;
        }
        
        $updatedAt = $userContent->updated_at;
        if ($updatedAt && Carbon::now()->subHour()->isAfter($updatedAt)) {
            return ['contents' => []];
        }
        
        return $userContent->context;
    }

    public function updateContextByChatId(int $chatId, array $context): bool
    {
        return $this->userContentRepository->updateContextByChatId($chatId, $context);
    }
    
    public function prepareContext(int $chatId, string $userQuestion): array
    {
        $context = $this->getContextByChatId($chatId);

        if ($context === null || !isset($context['contents'])) {
            $context = ['contents' => []];
        }

        $context['contents'][] = [
            'role' => 'user',
            'parts' => [
                [
                    'text' => $userQuestion
                ]
            ]
        ];

        return $context;
    }


    /**
     * 
     * @param int $chatId
     * @param string $userQuestion
     * @param array $gptResponse
     * @return bool
     */
    public function addGptResponseToContext(int $chatId, string $userQuestion, array $gptResponse): bool
    {
        $modelResponseText = $this->extractModelResponse($gptResponse);
        
        if ($modelResponseText === null) {
            return false;
        }

        $context = $this->getContextByChatId($chatId);
        
        if ($context === null || !isset($context['contents'])) {
            $context = ['contents' => []];
        }

        $context['contents'][] = [
            'role' => 'user',
            'parts' => [
                [
                    'text' => $userQuestion
                ]
            ]
        ];

        $context['contents'][] = [
            'role' => 'model',
            'parts' => [
                [
                    'text' => $modelResponseText
                ]
            ]
        ];

        $limit = self::CONTEXT_LIMIT * -2;
        $context['contents'] = array_slice($context['contents'], $limit);

        return $this->updateContextByChatId($chatId, $context);
    }

    /**
     * 
     * @param array $gptResponse 
     * @return string|null 
     */
    protected function extractModelResponse(array $gptResponse): ?string
    {
        if (!isset($gptResponse['candidates']) || !is_array($gptResponse['candidates'])) {
            return null;
        }

        $candidate = $gptResponse['candidates'][0] ?? null;
        if ($candidate === null) {
            return null;
        }

        $content = $candidate['content'] ?? null;
        if ($content === null) {
            return null;
        }

        $parts = $content['parts'] ?? [];
        if (empty($parts) || !isset($parts[0]['text'])) {
            return null;
        }

        return $parts[0]['text'];
    }

}