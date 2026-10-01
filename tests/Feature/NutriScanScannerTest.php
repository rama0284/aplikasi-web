<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodScan;
use App\Models\User;
use App\Services\AIServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NutriScanScannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scanner_page_requires_authentication(): void
    {
        $response = $this->get('/scanner');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_scanner_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/scanner');
        $response->assertStatus(200);
        $response->assertSee('AI Food Scanner');
    }

    public function test_scanner_rejects_invalid_file_upload(): void
    {
        $user = User::factory()->create();

        // Unggah file non-gambar (misal file .txt)
        $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

        $response = $this->actingAs($user)->post('/scanner', [
            'image' => $file,
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_scanner_processes_image_and_matches_database_food(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // Buat makanan di database
        $food = Food::create([
            'name' => 'Nasi Ayam Bakar',
            'category' => 'Lauk Pauk',
            'calories_per_100g' => 195.0,
            'protein_per_100g' => 20.0,
            'carbohydrates_per_100g' => 15.0,
            'fat_per_100g' => 6.0,
            'fiber_per_100g' => 1.0,
            'nutrition_source' => 'TKPI',
        ]);

        // Mock AIService
        $mockAi = $this->mock(AIServiceInterface::class);
        $mockAi->shouldReceive('identifyFood')->once()->andReturn([
            'success' => true,
            'is_demo' => false,
            'food_name' => 'Nasi Ayam Bakar',
            'possible_ingredients' => ['Nasi', 'Ayam'],
            'estimated_portion_grams' => 250.0,
            'confidence' => 0.95,
            'notes' => 'Terdeteksi jelas',
            'raw_response' => [],
            'error_message' => null,
        ]);

        $image = UploadedFile::fake()->image('food.jpg', 640, 480);

        $response = $this->actingAs($user)->post('/scanner', [
            'image' => $image,
        ]);

        $this->assertDatabaseHas('food_scans', [
            'user_id' => $user->id,
            'identified_food_name' => 'Nasi Ayam Bakar',
            'food_id' => $food->id,
        ]);

        $scan = FoodScan::where('user_id', $user->id)->first();
        $response->assertRedirect("/scan/{$scan->id}");
    }

    public function test_user_cannot_access_another_users_scan_result(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $scan = FoodScan::create([
            'user_id' => $user1->id,
            'image_path' => 'food_scans/sample.jpg',
            'identified_food_name' => 'Nasi Goreng',
            'status' => 'completed',
        ]);

        // User2 mencoba membuka scan milik User1
        $response = $this->actingAs($user2)->get("/scan/{$scan->id}");
        $response->assertStatus(404);
    }

    public function test_nutrition_calculation_formula(): void
    {
        $food = new Food([
            'name' => 'Ayam Panggang',
            'category' => 'Lauk Pauk',
            'calories_per_100g' => 200.0,
            'protein_per_100g' => 25.0,
            'carbohydrates_per_100g' => 0.0,
            'fat_per_100g' => 10.0,
            'fiber_per_100g' => 0.0,
        ]);

        // Porsi 250 gram: faktor = 2.5
        $calc = $food->calculateForPortion(250);

        $this->assertEquals(500.0, $calc['calories']);
        $this->assertEquals(62.5, $calc['protein']);
        $this->assertEquals(0.0, $calc['carbohydrates']);
        $this->assertEquals(25.0, $calc['fat']);
    }
}
