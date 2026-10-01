<?php

namespace App\Http\Controllers;

use App\Services\AIServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssistantController extends Controller
{
    protected AIServiceInterface $aiService;

    public function __construct(AIServiceInterface $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(): View
    {
        $user = Auth::user();
        $goal = $user->getOrCreateGoal();
        $isAiConfigured = $this->aiService->isConfigured();
        $isDemoMode = (bool) config('services.nutriscan_ai.demo_mode', true);

        return view('assistant.index', compact('user', 'goal', 'isAiConfigured', 'isDemoMode'));
    }

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ], [
            'messages.required' => 'Pesan percakapan tidak boleh kosong.',
            'messages.*.content.max' => 'Pesan maksimal 2000 karakter.',
        ]);

        $user = Auth::user();
        $goal = $user->nutritionGoal;

        $userContext = [
            'name' => $user->name,
            'calorie_goal' => $goal?->calorie_goal ?: 2000,
            'protein_goal' => $goal?->protein_goal ?: 60,
            'dietary_preferences' => $goal?->dietary_preferences,
        ];

        $response = $this->aiService->askNutritionAssistant($request->input('messages'), $userContext);

        return response()->json($response);
    }
}
