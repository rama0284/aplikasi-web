<?php

namespace App\Services;

interface AIServiceInterface
{
    /**
     * Check if AI service is properly configured with an API Key.
     */
    public function isConfigured(): bool;

    /**
     * Identify food from an image file using Vision AI model (e.g. via 9Router).
     *
     * @param string $imagePath Local absolute or relative storage path to image.
     * @param string $mimeType e.g. image/jpeg, image/png, image/webp
     * @return array [
     *     'success' => bool,
     *     'is_demo' => bool,
     *     'food_name' => string|null,
     *     'possible_ingredients' => array,
     *     'estimated_portion_grams' => float|null,
     *     'confidence' => float|null,
     *     'notes' => string|null,
     *     'raw_response' => array|null,
     *     'error_message' => string|null
     * ]
     */
    public function identifyFood(string $imagePath, string $mimeType = 'image/jpeg', ?string $originalFilename = null): array;

    /**
     * Ask nutrition assistant chatbot for advice in Indonesian.
     *
     * @param array $messages Array of ['role' => 'user'|'assistant', 'content' => string]
     * @param array|null $userContext Optional user profile / goals context
     * @return array ['success' => bool, 'is_demo' => bool, 'reply' => string, 'error_message' => string|null]
     */
    public function askNutritionAssistant(array $messages, ?array $userContext = null): array;
}
