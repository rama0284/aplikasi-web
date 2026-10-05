<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\MealLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test Nutritionist',
            'email' => 'nutritionist@nutriscan.ai',
        ]);
    }

    public function test_authenticated_user_can_view_food_database_index(): void
    {
        Food::create([
            'name' => 'Smoothie Buah Naga & Pisang',
            'category' => 'Minuman Sehat',
            'calories_per_100g' => 75.0,
            'protein_per_100g' => 1.5,
            'carbohydrates_per_100g' => 16.0,
            'fat_per_100g' => 0.5,
            'serving_size' => '1 gelas (250ml)',
            'health_grade' => 'A',
        ]);

        $response = $this->actingAs($this->user)->get(route('foods.index'));

        $response->assertStatus(200);
        $response->assertSee('Smoothie Buah Naga');
        $response->assertSee('Minuman Sehat');
    }

    public function test_authenticated_user_can_create_new_food_item(): void
    {
        $payload = [
            'name' => 'Salad Alpukat Quinoa',
            'category' => 'Sayuran',
            'calories_per_100g' => 140.0,
            'protein_per_100g' => 4.5,
            'carbohydrates_per_100g' => 18.0,
            'fat_per_100g' => 6.2,
            'fiber_per_100g' => 4.0,
            'sugar_g' => 1.2,
            'sodium_mg' => 95.0,
            'serving_size' => '1 mangkuk (180g)',
            'health_grade' => 'A',
            'icon_emoji' => '🥗',
            'description' => 'Salad superfood kaya asam lemak omega-9 dan serat.',
            'nutrition_source' => 'TKPI & USDA FoodData',
        ];

        $response = $this->actingAs($this->user)->post(route('foods.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('foods', [
            'name' => 'Salad Alpukat Quinoa',
            'category' => 'Sayuran',
            'health_grade' => 'A',
        ]);
    }

    public function test_authenticated_user_can_view_food_details(): void
    {
        $food = Food::create([
            'name' => 'Steak Salmon Panggang',
            'category' => 'Lauk Pauk',
            'calories_per_100g' => 208.0,
            'protein_per_100g' => 20.0,
            'carbohydrates_per_100g' => 0.0,
            'fat_per_100g' => 13.0,
            'serving_size' => '1 potong fillet (150g)',
            'health_grade' => 'A',
            'icon_emoji' => '🐟',
        ]);

        $response = $this->actingAs($this->user)->get(route('foods.show', $food->id));

        $response->assertStatus(200);
        $response->assertSee('Steak Salmon Panggang');
        $response->assertSee('Kalkulator Porsi Dinamis');
        $response->assertSee('Informasi Nilai Gizi');
    }

    public function test_authenticated_user_can_update_food(): void
    {
        $food = Food::create([
            'name' => 'Roti Gandum Murni',
            'category' => 'Makanan Pokok',
            'calories_per_100g' => 245.0,
            'protein_per_100g' => 9.0,
            'carbohydrates_per_100g' => 45.0,
            'fat_per_100g' => 3.5,
            'serving_size' => '2 lembar (70g)',
            'health_grade' => 'B',
        ]);

        $updatePayload = [
            'name' => 'Roti Gandum Utuh Super Fiber',
            'category' => 'Makanan Pokok',
            'calories_per_100g' => 230.0,
            'protein_per_100g' => 11.0,
            'carbohydrates_per_100g' => 41.0,
            'fat_per_100g' => 2.8,
            'fiber_per_100g' => 6.5,
            'serving_size' => '2 lembar (70g)',
            'health_grade' => 'A',
        ];

        $response = $this->actingAs($this->user)->put(route('foods.update', $food->id), $updatePayload);

        $response->assertRedirect();
        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'name' => 'Roti Gandum Utuh Super Fiber',
            'health_grade' => 'A',
        ]);
    }

    public function test_authenticated_user_can_quick_log_food_to_diary(): void
    {
        $food = Food::create([
            'name' => 'Yogurt Berry Parfait',
            'category' => 'Camilan Sehat',
            'calories_per_100g' => 95.0,
            'protein_per_100g' => 6.0,
            'carbohydrates_per_100g' => 14.0,
            'fat_per_100g' => 1.5,
            'serving_size' => '1 cup (150g)',
            'health_grade' => 'A',
        ]);

        $response = $this->actingAs($this->user)->post(route('foods.quick-log', $food->id), [
            'portion_grams' => 150,
            'meal_type' => 'camilan',
        ]);

        $response->assertRedirect(route('diary.index'));
        $this->assertDatabaseHas('meal_logs', [
            'user_id' => $this->user->id,
            'food_id' => $food->id,
            'meal_type' => 'camilan',
            'portion_grams' => 150,
        ]);
    }

    public function test_authenticated_user_can_delete_food(): void
    {
        $food = Food::create([
            'name' => 'Makanan Percobaan Hapus',
            'category' => 'Camilan Sehat',
            'calories_per_100g' => 100.0,
            'protein_per_100g' => 2.0,
            'carbohydrates_per_100g' => 15.0,
            'fat_per_100g' => 3.0,
            'serving_size' => '1 porsi (50g)',
            'health_grade' => 'C',
        ]);

        $response = $this->actingAs($this->user)->delete(route('foods.destroy', $food->id));

        $response->assertRedirect(route('foods.index'));
        $this->assertDatabaseMissing('foods', ['id' => $food->id]);
    }
}
