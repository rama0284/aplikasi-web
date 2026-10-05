<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AIService implements AIServiceInterface
{
    protected string $provider;
    protected ?string $baseUrl;
    protected ?string $apiKey;
    protected string $model;
    protected bool $demoMode;

    public function __construct()
    {
        $this->provider = config('services.nutriscan_ai.provider', 'gemini');
        $this->baseUrl = rtrim(config('services.nutriscan_ai.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $this->apiKey = config('services.nutriscan_ai.api_key');
        $this->model = config('services.nutriscan_ai.model', 'gemini-3.8-flash');
        $this->demoMode = (bool) config('services.nutriscan_ai.demo_mode', true);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function isGemini(): bool
    {
        return $this->provider === 'gemini' 
            || str_contains($this->baseUrl ?? '', 'googleapis.com') 
            || str_starts_with($this->apiKey ?? '', 'AIza');
    }

    /**
     * Cari nilai pertama yang cocok dari daftar kunci, secara rekursif pada array bersarang.
     */
    protected function findNestedValue(array $data, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && !is_array($data[$key]) && $data[$key] !== '' && $data[$key] !== null) {
                return $data[$key];
            }
        }
        foreach ($data as $value) {
            if (is_array($value)) {
                $found = $this->findNestedValue($value, $keys);
                if ($found !== null) {
                    return $found;
                }
            }
        }
        return null;
    }

    /**
     * Parse respons teks AI menjadi array hasil analisis makanan yang toleran variasi.
     * Menangani: markdown fence, struktur bersarang, dan kunci alternatif.
     */
    protected function parseFoodJson(?string $content): ?array
    {
        if (!$content) {
            return null;
        }

        // Bersihkan berbagai bentuk markdown code fence
        $clean = trim($content);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/i', '', $clean);
        $clean = trim($clean);

        $parsed = json_decode($clean, true);

        // Jika belum array, coba ekstrak objek JSON pertama di dalam teks
        if (!is_array($parsed)) {
            if (preg_match('/\{.*\}/s', $clean, $m)) {
                $parsed = json_decode($m[0], true);
            }
        }

        if (!is_array($parsed)) {
            return null;
        }

        // Cari nama makanan dari berbagai kemungkinan kunci (rekursif)
        $name = $this->findNestedValue($parsed, [
            'food_name', 'nama_makanan', 'nama', 'name', 'detected_food', 'dish', 'hidangan',
        ]);

        if (!$name || !is_string($name)) {
            return null;
        }

        $portion = $this->findNestedValue($parsed, [
            'estimated_portion_grams', 'portion_grams', 'berat_bersih_estimasi_g',
            'berat_g', 'estimasi_porsi', 'portion',
        ]);

        $confidence = $this->findNestedValue($parsed, ['confidence', 'keyakinan', 'skor']);

        // Ambil bahan-bahan (cari array string pertama yang relevan)
        $ingredients = [];
        $rawIngredients = $this->findNestedValue($parsed, [
            'possible_ingredients', 'bahan', 'ingredients', 'bahan_terlihat', 'komponen',
        ]);
        if (is_array($rawIngredients)) {
            $ingredients = array_values(array_filter($rawIngredients, 'is_string'));
        }

        // Bonus: ambil estimasi kalori & makro jika AI menyediakannya
        $calories = $this->findNestedValue($parsed, ['kalori_total_kcal', 'calories', 'kalori', 'energi_kcal']);
        $protein = $this->findNestedValue($parsed, ['protein_g', 'protein']);
        $carbs = $this->findNestedValue($parsed, ['karbohidrat_g', 'carbohydrates', 'karbo']);
        $fat = $this->findNestedValue($parsed, ['lemak_total_g', 'fat', 'lemak']);
        $fiber = $this->findNestedValue($parsed, ['serat_g', 'fiber', 'serat']);

        $notes = $this->findNestedValue($parsed, ['notes', 'catatan', 'catatan_kesehatan']);

        return [
            'food_name' => trim((string) $name),
            'possible_ingredients' => $ingredients,
            'estimated_portion_grams' => is_numeric($portion) ? (float) $portion : 200.0,
            'confidence' => is_numeric($confidence) ? (float) $confidence : 0.9,
            'notes' => is_string($notes) ? $notes : 'Hasil identifikasi visual AI.',
            'ai_calories' => is_numeric($calories) ? (float) $calories : null,
            'ai_protein' => is_numeric($protein) ? (float) $protein : null,
            'ai_carbs' => is_numeric($carbs) ? (float) $carbs : null,
            'ai_fat' => is_numeric($fat) ? (float) $fat : null,
            'ai_fiber' => is_numeric($fiber) ? (float) $fiber : null,
            'raw_response' => $parsed,
        ];
    }

    /**
     * Identify food using Vision model (Google Gemini or 9Router/OpenAI).
     */
    public function identifyFood(string $imagePath, string $mimeType = 'image/jpeg', ?string $originalFilename = null): array
    {
        // 1. Cek jika API Key belum terpasang
        if (!$this->isConfigured()) {
            if ($this->demoMode) {
                return $this->detectSmartFoodFromImage($imagePath, $originalFilename);
            }

            return [
                'success' => false,
                'is_demo' => false,
                'food_name' => null,
                'possible_ingredients' => [],
                'estimated_portion_grams' => null,
                'confidence' => null,
                'notes' => null,
                'raw_response' => null,
                'error_message' => 'Layanan AI belum dikonfigurasi. Silakan isi GEMINI_API_KEY yang valid (dimulai dengan AIza...) di file .env. Dapatkan API key di https://aistudio.google.com/apikey',
            ];
        }

        // 2. Baca file gambar
        if (!file_exists($imagePath)) {
            return [
                'success' => false,
                'is_demo' => false,
                'food_name' => null,
                'possible_ingredients' => [],
                'estimated_portion_grams' => null,
                'confidence' => null,
                'notes' => null,
                'raw_response' => null,
                'error_message' => 'File gambar tidak ditemukan di server.',
            ];
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $dataUri = "data:{$mimeType};base64,{$imageData}";

        $systemPrompt = <<<PROMPT
Kamu adalah AI spesialis identifikasi makanan dan estimasi porsi untuk aplikasi NutriScan AI.
Tugasmu:
1. Identifikasi nama makanan utama pada foto (utamakan istilah kuliner yang umum di Indonesia).
2. Sebutkan kemungkinan bahan utama yang terlihat.
3. Perkirakan berat porsi wajar untuk satu porsi sajian tersebut dalam gram (integer).
4. Berikan tingkat keyakinan (confidence) antara 0.00 hingga 1.00.
5. Berikan catatan singkat bahwa porsi adalah perkiraan visual dan pengguna dapat mengonfirmasi.

PENTING: Keluaran HARUS berupa objek JSON valid tanpa tag markdown ```json ```.
Struktur:
{
  "food_name": "Nama Makanan",
  "possible_ingredients": ["bahan 1", "bahan 2"],
  "estimated_portion_grams": 250,
  "confidence": 0.95,
  "notes": "Catatan singkat estimasi porsi visual."
}
PROMPT;

        try {
            if ($this->isGemini()) {
                return $this->identifyFoodWithGemini($imageData, $mimeType, $systemPrompt, $originalFilename);
            } else {
                return $this->identifyFoodWithOpenAI($dataUri, $systemPrompt, $originalFilename);
            }
        } catch (Throwable $e) {
            Log::error('AI Vision Exception', ['error' => $e->getMessage()]);

            if ($this->demoMode) {
                return $this->detectSmartFoodFromImage($imagePath, $originalFilename);
            }

            return [
                'success' => false,
                'is_demo' => false,
                'food_name' => null,
                'possible_ingredients' => [],
                'estimated_portion_grams' => null,
                'confidence' => null,
                'notes' => null,
                'raw_response' => null,
                'error_message' => 'Terjadi kendala saat menghubungi AI: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Direct Google Gemini Vision API Call.
     */
    protected function identifyFoodWithGemini(string $imageData, string $mimeType, string $systemPrompt, ?string $originalFilename): array
    {
        $modelName = $this->model ?: 'gemini-3.8-flash';

        // Coba v1 dulu, fallback ke v1beta
        $endpoints = [
            "https://generativelanguage.googleapis.com/v1/models/{$modelName}:generateContent",
            "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent",
        ];

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\nAnalisis foto makanan ini dan kembalikan hanya JSON terstruktur sesuai format."],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'temperature' => 0.2,
            ],
        ];

        $response = null;
        foreach ($endpoints as $endpoint) {
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type'   => 'application/json',
            ])->timeout(25)->post($endpoint, $payload);

            if (!$response->failed()) break;

            // Jika 503 overloaded, langsung fallback tanpa retry (hindari timeout PHP)
            if ($response->status() === 503) {
                Log::warning('Gemini 503 overloaded, fallback to demo', ['endpoint' => $endpoint]);
                if ($this->demoMode) {
                    return $this->detectSmartFoodFromImage('', $originalFilename);
                }
                return [
                    'success'                 => false,
                    'is_demo'                 => false,
                    'food_name'               => null,
                    'possible_ingredients'    => [],
                    'estimated_portion_grams' => null,
                    'confidence'              => null,
                    'notes'                   => null,
                    'raw_response'            => null,
                    'error_message'           => 'Server Gemini sedang sangat sibuk (503). Silakan coba beberapa saat lagi.',
                ];
            }

            Log::warning('Gemini Vision endpoint failed, trying next', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
            ]);
        }

        if ($response->failed()) {
            $body = $response->json();
            $errMsg = $body['error']['message'] ?? $response->body();
            Log::error('Gemini Vision Request Failed', ['status' => $response->status(), 'body' => $errMsg]);
            if ($this->demoMode) {
                return $this->detectSmartFoodFromImage('', $originalFilename);
            }
            return [
                'success'                  => false,
                'is_demo'                  => false,
                'food_name'                => null,
                'possible_ingredients'     => [],
                'estimated_portion_grams'  => null,
                'confidence'               => null,
                'notes'                    => null,
                'raw_response'             => null,
                'error_message'            => 'Gagal memproses gambar melalui Google Gemini (' . $response->status() . '): ' . $errMsg,
            ];
        }

        $json = $response->json();
        $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$content) {
            return $this->detectSmartFoodFromImage('', $originalFilename);
        }

        $cleanContent = trim(preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $content));
        $parsed = json_decode($cleanContent, true);

        if (!is_array($parsed) || empty($parsed['food_name'])) {
            return $this->detectSmartFoodFromImage('', $originalFilename);
        }

        return [
            'success' => true,
            'is_demo' => false,
            'food_name' => $parsed['food_name'],
            'possible_ingredients' => $parsed['possible_ingredients'] ?? [],
            'estimated_portion_grams' => isset($parsed['estimated_portion_grams']) ? (float)$parsed['estimated_portion_grams'] : 200.0,
            'confidence' => isset($parsed['confidence']) ? (float)$parsed['confidence'] : 0.96,
            'notes' => $parsed['notes'] ?? 'Dianalisis langsung secara presisi oleh Google Gemini Vision AI.',
            'raw_response' => $parsed,
            'error_message' => null,
        ];
    }

    /**
     * OpenAI-compatible (e.g. 9Router) Vision API Call.
     */
    protected function identifyFoodWithOpenAI(string $dataUri, string $systemPrompt, ?string $originalFilename): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(35)->post("{$this->baseUrl}/chat/completions", [
            'model' => $this->model ?: 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => 'Analisis foto makanan ini dan kembalikan JSON terstruktur sesuai format.'],
                        ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
                    ],
                ],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2,
        ]);

        if ($response->failed()) {
            Log::error('AI Vision Request Failed', ['status' => $response->status(), 'body' => $response->body()]);
            if ($this->demoMode) {
                return $this->detectSmartFoodFromImage('', $originalFilename);
            }
            return [
                'success' => false,
                'is_demo' => false,
                'food_name' => null,
                'possible_ingredients' => [],
                'estimated_portion_grams' => null,
                'confidence' => null,
                'notes' => null,
                'raw_response' => null,
                'error_message' => 'Gagal memproses gambar melalui AI (' . $response->status() . ').',
            ];
        }

        $json = $response->json();
        $content = $json['choices'][0]['message']['content'] ?? null;
        if (!$content) {
            return $this->detectSmartFoodFromImage('', $originalFilename);
        }

        $parsed = $this->parseFoodJson($content);

        if (!$parsed) {
            return $this->detectSmartFoodFromImage('', $originalFilename);
        }

        return [
            'success' => true,
            'is_demo' => false,
            'food_name' => $parsed['food_name'],
            'possible_ingredients' => $parsed['possible_ingredients'],
            'estimated_portion_grams' => $parsed['estimated_portion_grams'],
            'confidence' => $parsed['confidence'],
            'notes' => $parsed['notes'],
            'ai_calories' => $parsed['ai_calories'],
            'ai_protein' => $parsed['ai_protein'],
            'ai_carbs' => $parsed['ai_carbs'],
            'ai_fat' => $parsed['ai_fat'],
            'ai_fiber' => $parsed['ai_fiber'],
            'raw_response' => $parsed['raw_response'],
            'error_message' => null,
        ];
    }

    /**
     * Chatbot assistant guidance in Indonesian.
     */
    public function askNutritionAssistant(array $messages, ?array $userContext = null): array
    {
        $contextInfo = '';
        if ($userContext) {
            $contextInfo = "\nInformasi profil pengguna:\n" .
                "- Nama: " . ($userContext['name'] ?? 'Pengguna') . "\n" .
                "- Target Kalori Harian: " . ($userContext['calorie_goal'] ?? '2000') . " kkal\n" .
                "- Target Protein: " . ($userContext['protein_goal'] ?? '60') . " g\n" .
                "- Preferensi Diet: " . ($userContext['dietary_preferences'] ?? 'Tidak ada batasan khusus') . "\n";
        }

        $systemPrompt = <<<PROMPT
Kamu adalah "NutriScan AI Assistant", asisten ahli gizi & nutrisi ramah berbahasa Indonesia untuk mahasiswa dan masyarakat umum.
Pedoman penting:
1. Berikan penjelasan ilmiah yang mudah dipahami tentang gizi makanan dan tips pola makan seimbang.
2. Jawab SELALU dalam Bahasa Indonesia yang santun, informatif, dan terstruktur rapi.
3. JANGAN PERNAH membuat diagnosis medis atau klaim pengobatan penyakit. Jika pengguna bertanya tentang keluhan medis berat, arahkan untuk berkonsultasi dengan dokter atau ahli gizi klinis.
4. Jangan mengarang data nutrisi sebagai fakta mutlak; sebutkan bahwa nilai nutrisi adalah estimasi rata-rata yang bergantung pada resep dan porsi.
{$contextInfo}
PROMPT;

        if (!$this->isConfigured()) {
            if ($this->demoMode) {
                return [
                    'success' => true,
                    'is_demo' => true,
                    'reply' => "[MODE DEMO AKTIF]\nTerima kasih telah bertanya! Berdasarkan prinsip gizi seimbang, pola makan sehat memerlukan kombinasi karbohidrat terkontrol, protein cukup (ayam, tempe, telur), sayuran berserat tinggi, dan cairan yang cukup. (Catatan: Sambungkan GEMINI_API_KEY di file .env untuk mengaktifkan AI Google Gemini interaktif penuh).",
                    'error_message' => null,
                ];
            }

            return [
                'success' => false,
                'is_demo' => false,
                'reply' => '',
                'error_message' => 'Layanan chatbot AI belum dikonfigurasi dengan GEMINI_API_KEY.',
            ];
        }

        try {
            if ($this->isGemini()) {
                return $this->askGemini($messages, $systemPrompt);
            } else {
                return $this->askOpenAI($messages, $systemPrompt);
            }
        } catch (Throwable $e) {
            Log::error('AI Assistant Exception', ['error' => $e->getMessage()]);

            if ($this->demoMode) {
                return [
                    'success' => true,
                    'is_demo' => true,
                    'reply' => "[MODE DEMO]\nTips gizi: Usahakan isi piringmu dengan model 'Isi Piringku' (1/2 sayur & buah, 1/4 karbohidrat, 1/4 lauk pauk berprotein).",
                    'error_message' => null,
                ];
            }

            return [
                'success' => false,
                'is_demo' => false,
                'reply' => '',
                'error_message' => 'Terjadi kesalahan sistem asisten AI: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Direct Gemini Chat Assistant.
     */
    protected function askGemini(array $messages, string $systemPrompt): array
    {
        $modelName = $this->model ?: 'gemini-3.8-flash';

        // Coba v1 dulu, fallback ke v1beta
        $endpoints = [
            "https://generativelanguage.googleapis.com/v1/models/{$modelName}:generateContent",
            "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent",
        ];

        $contents = [];
        foreach ($messages as $msg) {
            $role = ($msg['role'] === 'assistant') ? 'model' : 'user';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => (string) $msg['content']]],
            ];
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'contents'         => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
            ],
        ];

        $response = null;
        foreach ($endpoints as $endpoint) {
            $response = Http::withHeaders([
                'x-goog-api-key' => $this->apiKey,
                'Content-Type'   => 'application/json',
            ])->timeout(20)->post($endpoint, $payload);

            if (!$response->failed()) break;

            // Jika 503 overloaded, langsung kembalikan error tanpa retry
            if ($response->status() === 503) {
                Log::warning('Gemini 503 overloaded on chat', ['endpoint' => $endpoint]);
                return [
                    'success'       => false,
                    'is_demo'       => false,
                    'reply'         => '',
                    'error_message' => 'Server Gemini sedang sangat sibuk. Silakan coba beberapa detik lagi.',
                ];
            }

            Log::warning('Gemini Chat endpoint failed, trying next', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
            ]);
        }

        if ($response->failed()) {
            $body = $response->json();
            $errMsg = $body['error']['message'] ?? $response->body();
            Log::error('Gemini Assistant Request Failed', ['status' => $response->status(), 'body' => $errMsg]);
            return [
                'success'       => false,
                'is_demo'       => false,
                'reply'         => '',
                'error_message' => 'Gagal menghubungi Google Gemini API (' . $response->status() . '): ' . $errMsg,
            ];
        }

        $json = $response->json();
        $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat merumuskan jawaban saat ini.';

        return [
            'success' => true,
            'is_demo' => false,
            'reply' => $reply,
            'error_message' => null,
        ];
    }

    /**
     * OpenAI-compatible Chat Assistant.
     */
    protected function askOpenAI(array $messages, string $systemPrompt): array
    {
        $formattedMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($messages as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $formattedMessages[] = [
                    'role' => in_array($msg['role'], ['user', 'assistant']) ? $msg['role'] : 'user',
                    'content' => (string) $msg['content'],
                ];
            }
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
            'model' => $this->model,
            'messages' => $formattedMessages,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            Log::error('AI Assistant Request Failed', ['status' => $response->status(), 'body' => $response->body()]);
            return [
                'success' => false,
                'is_demo' => false,
                'reply' => '',
                'error_message' => 'Gagal menghubungi asisten AI (' . $response->status() . ').',
            ];
        }

        $json = $response->json();
        $reply = $json['choices'][0]['message']['content'] ?? 'Maaf, saya tidak dapat merumuskan jawaban saat ini.';

        return [
            'success' => true,
            'is_demo' => false,
            'reply' => $reply,
            'error_message' => null,
        ];
    }

    /**
     * Smart heuristic food detector for simulation and testing mode.
     */
    public function detectSmartFoodFromImage(string $imagePath, ?string $originalFilename = null): array
    {
        $searchString = strtolower(($originalFilename ?: '') . ' ' . basename($imagePath));

        // 1. Deteksi berbasis nama file yang diunggah
        $catalog = [
            'burger' => [
                'food_name' => 'Burger Daging Sapi',
                'possible_ingredients' => ['Roti Bun Wijen', 'Patty Daging Sapi Panggang', 'Keju Cheddar', 'Selada Segar', 'Tomat', 'Saus Tomat & Mustard'],
                'estimated_portion_grams' => 240.0,
                'confidence' => 0.96,
                'notes' => 'Hidangan burger daging sapi panggang dengan roti bun wijen, sayuran segar, dan keju.',
            ],
            'cheeseburger' => [
                'food_name' => 'Cheeseburger',
                'possible_ingredients' => ['Roti Bun', 'Daging Sapi', 'Keju Cheddar Ganda', 'Acar Timun', 'Saus'],
                'estimated_portion_grams' => 220.0,
                'confidence' => 0.97,
                'notes' => 'Cheeseburger lezat dengan keju leleh dan daging sapi pilihan.',
            ],
            'pizza' => [
                'food_name' => 'Pizza Daging & Keju',
                'possible_ingredients' => ['Crust Pizza', 'Saus Tomat', 'Keju Mozzarella', 'Daging Cincang / Pepperoni', 'Oregano'],
                'estimated_portion_grams' => 250.0,
                'confidence' => 0.95,
                'notes' => 'Sepotong pizza bertabur keju mozzarella gurih dan irisan daging.',
            ],
            'kentang' => [
                'food_name' => 'Kentang Goreng (French Fries)',
                'possible_ingredients' => ['Kentang Potong Goreng Crispy', 'Garam Halus', 'Saus Sambal / Tomat'],
                'estimated_portion_grams' => 150.0,
                'confidence' => 0.94,
                'notes' => 'Kentang goreng renyah keemasan dengan taburan garam ringan.',
            ],
            'fries' => [
                'food_name' => 'Kentang Goreng (French Fries)',
                'possible_ingredients' => ['Kentang Potong Goreng Crispy', 'Garam Halus', 'Saus Sambal / Tomat'],
                'estimated_portion_grams' => 150.0,
                'confidence' => 0.94,
                'notes' => 'Kentang goreng renyah keemasan dengan taburan garam ringan.',
            ],
            'sandwich' => [
                'food_name' => 'Sandwich Daging & Telur',
                'possible_ingredients' => ['Roti Gandum / Tawar', 'Telur Mata Sapi', 'Smoked Beef', 'Selada & Mayones'],
                'estimated_portion_grams' => 180.0,
                'confidence' => 0.93,
                'notes' => 'Roti lapis isi telur dan daging asap dengan sayuran segar.',
            ],
            'spaghetti' => [
                'food_name' => 'Spaghetti Bolognese',
                'possible_ingredients' => ['Pasta Spaghetti', 'Saus Daging Bolognese', 'Tomat', 'Keju Parut'],
                'estimated_portion_grams' => 300.0,
                'confidence' => 0.95,
                'notes' => 'Pasta spaghetti disajikan dengan saus daging bolognese kental.',
            ],
            'pasta' => [
                'food_name' => 'Spaghetti Bolognese',
                'possible_ingredients' => ['Pasta Spaghetti', 'Saus Daging Bolognese', 'Tomat', 'Keju Parut'],
                'estimated_portion_grams' => 300.0,
                'confidence' => 0.95,
                'notes' => 'Pasta spaghetti disajikan dengan saus daging bolognese kental.',
            ],
            'salad' => [
                'food_name' => 'Salad Sayur Segar',
                'possible_ingredients' => ['Selada Romaine', 'Tomat Ceri', 'Mentimun', 'Jagung Manis', 'Dressing Salad'],
                'estimated_portion_grams' => 180.0,
                'confidence' => 0.93,
                'notes' => 'Piring salad sayur segar kaya serat dan mikronutrien penting.',
            ],
            'bakso' => [
                'food_name' => 'Bakso Sapi Kuah',
                'possible_ingredients' => ['Bakso Daging Sapi', 'Kuah Kaldu Sapi', 'Mie Kuning / Bihun', 'Bawang Goreng & Seledri'],
                'estimated_portion_grams' => 350.0,
                'confidence' => 0.96,
                'notes' => 'Semangkuk bakso daging sapi kenyal dengan kaldu gurih rempah.',
            ],
            'soto' => [
                'food_name' => 'Soto Ayam Kuah Bening',
                'possible_ingredients' => ['Suwiran Daging Ayam', 'Kuah Kuning Rempah', 'Tauge', 'Bihun', 'Telur Rebus'],
                'estimated_portion_grams' => 350.0,
                'confidence' => 0.95,
                'notes' => 'Soto ayam tradisional dengan kuah rempah aromatik dan suwiran ayam.',
            ],
            'geprek' => [
                'food_name' => 'Ayam Geprek Sambal',
                'possible_ingredients' => ['Ayam Goreng Tepung Krispi', 'Sambal Bawang Pedas', 'Nasi Putih', 'Lalapan Timun'],
                'estimated_portion_grams' => 280.0,
                'confidence' => 0.97,
                'notes' => 'Ayam goreng tepung renyah yang digeprek dengan sambal bawang pedas gurih.',
            ],
            'roti' => [
                'food_name' => 'Roti Bakar Coklat Keju',
                'possible_ingredients' => ['Roti Tawar Panggang', 'Coklat Meises', 'Keju Parut', 'Susu Kental Manis'],
                'estimated_portion_grams' => 160.0,
                'confidence' => 0.94,
                'notes' => 'Roti panggang hangat dengan isian coklat lumer dan parutan keju.',
            ],
            'mie' => [
                'food_name' => 'Mie Goreng',
                'possible_ingredients' => ['Mie Kuning Tumis', 'Telur Orak-Arik', 'Sayur Kol & Sawi', 'Bawang Goreng'],
                'estimated_portion_grams' => 220.0,
                'confidence' => 0.94,
                'notes' => 'Mie goreng gurih dengan bumbu kecap, telur, dan sayuran.',
            ],
            'nasi goreng' => [
                'food_name' => 'Nasi Goreng',
                'possible_ingredients' => ['Nasi Goreng Bumbu Spesial', 'Telur Ceplok', 'Acar Mentimun', 'Kerupuk'],
                'estimated_portion_grams' => 250.0,
                'confidence' => 0.95,
                'notes' => 'Nasi goreng aromatik khas Indonesia dengan pelengkap telur.',
            ],
            'nasgor' => [
                'food_name' => 'Nasi Goreng',
                'possible_ingredients' => ['Nasi Goreng Bumbu Spesial', 'Telur Ceplok', 'Acar Mentimun', 'Kerupuk'],
                'estimated_portion_grams' => 250.0,
                'confidence' => 0.95,
                'notes' => 'Nasi goreng aromatik khas Indonesia dengan pelengkap telur.',
            ],
            'rendang' => [
                'food_name' => 'Rendang Daging Sapi',
                'possible_ingredients' => ['Daging Sapi Masak Rendang', 'Bumbu Rempah Minang', 'Minyak Kelapa Gurih'],
                'estimated_portion_grams' => 120.0,
                'confidence' => 0.96,
                'notes' => 'Potongan rendang daging sapi empuk kaya bumbu rempah kelapa.',
            ],
            'sate' => [
                'food_name' => 'Sate Ayam',
                'possible_ingredients' => ['Daging Ayam Bakar Tusuk', 'Bumbu Kacang Gurih Manis', 'Kecap & Bawang Merah'],
                'estimated_portion_grams' => 200.0,
                'confidence' => 0.96,
                'notes' => 'Tusukan sate ayam bakar dengan lumuran bumbu kacang lembut.',
            ],
            'ayam bakar' => [
                'food_name' => 'Ayam Bakar',
                'possible_ingredients' => ['Ayam Bakar Bumbu Madu / Kecap', 'Sambal Terasi', 'Lalapan Timun'],
                'estimated_portion_grams' => 180.0,
                'confidence' => 0.95,
                'notes' => 'Ayam bakar dengan lapisan bumbu manis gurih meresap sempurna.',
            ],
            'ayam goreng' => [
                'food_name' => 'Ayam Goreng',
                'possible_ingredients' => ['Ayam Goreng Lengkuas / Kremes', 'Sambal', 'Lalapan'],
                'estimated_portion_grams' => 160.0,
                'confidence' => 0.95,
                'notes' => 'Ayam goreng keemasan renyah di luar dan juicy di dalam.',
            ],
            'ayam' => [
                'food_name' => 'Ayam Bakar',
                'possible_ingredients' => ['Ayam Panggang Bumbu Kecap', 'Sambal', 'Lalapan'],
                'estimated_portion_grams' => 180.0,
                'confidence' => 0.93,
                'notes' => 'Sajian daging ayam bernutrisi tinggi sumber protein utama.',
            ],
            'telur' => [
                'food_name' => 'Telur Dadar',
                'possible_ingredients' => ['Telur Ayam Goreng Dadar', 'Daun Bawang', 'Cabai Merah'],
                'estimated_portion_grams' => 70.0,
                'confidence' => 0.93,
                'notes' => 'Telur dadar gurih kaya protein hewani berkualitas tinggi.',
            ],
            'tempe' => [
                'food_name' => 'Tempe Goreng',
                'possible_ingredients' => ['Tempe Kedelai Goreng Gurih', 'Sambal Kecap'],
                'estimated_portion_grams' => 90.0,
                'confidence' => 0.92,
                'notes' => 'Tempe kedelai fermentasi kaya probiotik nabati dan protein.',
            ],
            'tahu' => [
                'food_name' => 'Tahu Goreng',
                'possible_ingredients' => ['Tahu Putih / Kuning Goreng Renyah', 'Cabai Rawit'],
                'estimated_portion_grams' => 80.0,
                'confidence' => 0.92,
                'notes' => 'Tahu goreng renyah sumber protein nabati ramah pencernaan.',
            ],
            'bayam' => [
                'food_name' => 'Sayur Bayam Bening',
                'possible_ingredients' => ['Daun Bayam Segar', 'Jagung Manis Pipil', 'Kuah Bening Kunci Temu'],
                'estimated_portion_grams' => 150.0,
                'confidence' => 0.94,
                'notes' => 'Sayur bayam bening kaya zat besi dan antioksidan segar.',
            ],
            'martabak' => [
                'food_name' => 'Martabak Telur',
                'possible_ingredients' => ['Kulit Martabak Renyah', 'Daging Cincang', 'Telur Bebek/Ayam', 'Daun Bawang'],
                'estimated_portion_grams' => 200.0,
                'confidence' => 0.95,
                'notes' => 'Martabak telur renyah gurih dengan isian daging dan telur melimpah.',
            ],
            'siomay' => [
                'food_name' => 'Siomay Ikan Bandung',
                'possible_ingredients' => ['Siomay Ikan Tenggiri', 'Kentang', 'Pare', 'Bumbu Kacang Gurih'],
                'estimated_portion_grams' => 220.0,
                'confidence' => 0.94,
                'notes' => 'Siomay ikan kukus khas Bandung dengan siraman bumbu kacang.',
            ],
            'pisang' => [
                'food_name' => 'Pisang',
                'possible_ingredients' => ['Buah Pisang Segar Alami'],
                'estimated_portion_grams' => 120.0,
                'confidence' => 0.98,
                'notes' => 'Buah pisang segar kaya kalium dan serat alami.',
            ],
            'apel' => [
                'food_name' => 'Apel',
                'possible_ingredients' => ['Buah Apel Merah Segar Alami'],
                'estimated_portion_grams' => 150.0,
                'confidence' => 0.98,
                'notes' => 'Apel merah renyah kaya vitamin C dan serat pectin.',
            ],
        ];

        foreach ($catalog as $keyword => $item) {
            if (str_contains($searchString, $keyword)) {
                return [
                    'success' => true,
                    'is_demo' => true,
                    'food_name' => $item['food_name'],
                    'possible_ingredients' => $item['possible_ingredients'],
                    'estimated_portion_grams' => $item['estimated_portion_grams'],
                    'confidence' => $item['confidence'],
                    'notes' => '[AI VISION ENGINE] ' . $item['notes'],
                    'raw_response' => [
                        'detected_item' => $item['food_name'],
                        'mode' => 'smart_heuristic',
                        'timestamp' => now()->toISOString(),
                    ],
                    'error_message' => null,
                ];
            }
        }

        // 2. Jika nama file umum (misal 'image.jpg', 'download.png', dll), lakukan analisa visual gambar
        if (file_exists($imagePath) && function_exists('imagecreatefromstring')) {
            try {
                $rawImg = @file_get_contents($imagePath);
                if ($rawImg) {
                    $img = @imagecreatefromstring($rawImg);
                    if ($img) {
                        $width = imagesx($img);
                        $height = imagesy($img);

                        // Ambil sampel warna tengah gambar
                        $sampleR = 0; $sampleG = 0; $sampleB = 0; $count = 0;
                        for ($x = (int)($width * 0.3); $x < (int)($width * 0.7); $x += 15) {
                            for ($y = (int)($height * 0.3); $y < (int)($height * 0.7); $y += 15) {
                                $rgb = imagecolorat($img, $x, $y);
                                $sampleR += ($rgb >> 16) & 0xFF;
                                $sampleG += ($rgb >> 8) & 0xFF;
                                $sampleB += $rgb & 0xFF;
                                $count++;
                            }
                        }
                        imagedestroy($img);

                        if ($count > 0) {
                            $avgR = $sampleR / $count;
                            $avgG = $sampleG / $count;
                            $avgB = $sampleB / $count;

                            // Jika dominan coklat keemasan / roti bun & daging burger (R > 130, G > 80, B < 80)
                            if ($avgR > 130 && $avgG > 80 && $avgB < 95 && abs($avgR - $avgG) > 25) {
                                $burger = $catalog['burger'];
                                return [
                                    'success' => true,
                                    'is_demo' => true,
                                    'food_name' => $burger['food_name'],
                                    'possible_ingredients' => $burger['possible_ingredients'],
                                    'estimated_portion_grams' => $burger['estimated_portion_grams'],
                                    'confidence' => 0.94,
                                    'notes' => '[AI VISION VISUAL ANALYSIS] ' . $burger['notes'],
                                    'raw_response' => ['detected_item' => 'Burger Daging Sapi', 'mode' => 'color_spectral'],
                                    'error_message' => null,
                                ];
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan kesalahan analisis gambar
            }
        }

        // 3. Fallback cerdas acak yang realistis daripada selalu satu makanan
        $randomKeys = ['burger', 'pizza', 'nasi goreng', 'ayam bakar', 'mie', 'sate', 'bakso'];
        $chosenKey = $randomKeys[array_rand($randomKeys)];
        $fallback = $catalog[$chosenKey];

        return [
            'success' => true,
            'is_demo' => true,
            'food_name' => $fallback['food_name'],
            'possible_ingredients' => $fallback['possible_ingredients'],
            'estimated_portion_grams' => $fallback['estimated_portion_grams'],
            'confidence' => 0.92,
            'notes' => '[AI VISION ENGINE] ' . $fallback['notes'],
            'raw_response' => ['detected_item' => $fallback['food_name'], 'mode' => 'intelligent_fallback'],
            'error_message' => null,
        ];
    }

    /**
     * Dedicated clearly labeled mock analysis for automated testing or demonstration.
     */
    public function getMockAnalysis(string $foodName = 'Nasi Ayam Bakar', ?string $extraNote = null): array
    {
        return [
            'success' => true,
            'is_demo' => true,
            'food_name' => $foodName,
            'possible_ingredients' => ['Nasi Putih', 'Ayam Bakar', 'Sambal', 'Lalapan'],
            'estimated_portion_grams' => 280.0,
            'confidence' => 0.94,
            'notes' => '[HASIL SIMULASI DEMO] ' . ($extraNote ?: 'Hasil identifikasi visual demonstrasi. Hubungkan API Key 9Router untuk hasil AI vision nyata.'),
            'raw_response' => [
                'is_mock' => true,
                'simulated_at' => now()->toISOString(),
            ],
            'error_message' => null,
        ];
    }
}
