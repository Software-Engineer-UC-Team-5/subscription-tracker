<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pengujian CRUD kategori mencatat nama, ikon, warna, dan log aktivitas milik akun aktif.
     */
    public function test_user_can_create_edit_and_delete_a_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $this->get('/categories')->assertOk()->assertSee('Belum ada kategori.');
        $this->get('/categories/create')->assertOk()->assertSee('name="icon"', false);
        $this->post('/categories', [
            'name' => 'Hiburan', 'icon' => 'tv', 'color' => '#ef4444', 'user_id' => $otherUser->id,
        ])->assertRedirect('/categories')->assertSessionHas('success');

        $category = $user->categories()->sole();
        $this->assertSame('tv', $category->icon);
        $this->assertSame('#ef4444', $category->color);
        $this->assertDatabaseMissing('categories', ['user_id' => $otherUser->id]);
        $this->get('/categories')->assertOk()->assertSee('Hiburan')->assertSee('color: #ef4444')
            ->assertSee('background-color: #ef44441a; border-color: #ef444466', false);
        $this->get("/categories/{$category->id}/edit")->assertOk()->assertSee('value="Hiburan"', false);
        $this->get('/subscriptions/create')->assertOk()->assertSee('Hiburan');

        $this->put("/categories/{$category->id}", [
            'name' => 'Hiburan', 'icon' => 'music', 'color' => '#2563eb', 'user_id' => $otherUser->id,
        ])->assertRedirect('/categories')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id, 'user_id' => $user->id, 'icon' => 'music', 'color' => '#2563eb',
        ]);
        $this->delete("/categories/{$category->id}")->assertRedirect('/categories')->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        foreach (['CREATE', 'UPDATE', 'DELETE'] as $action) {
            $this->assertDatabaseHas('activity_logs', [
                'user_id' => $user->id, 'entity' => 'Category', 'entity_id' => $category->id, 'action' => $action,
            ]);
        }
    }

    /**
     * Pengujian salinan kategori awal memiliki Edit/Hapus tanpa mengubah template atau akun lain.
     */
    public function test_users_can_manage_their_own_copies_of_default_categories(): void
    {
        $systemCategory = Category::create(['name' => 'Kategori Bawaan']);
        $owner = User::factory()->create();
        $privateCategory = $owner->categories()->create(['name' => 'Kategori Pribadi']);
        $user = User::factory()->create();
        $categoryRepository = app(CategoryRepository::class);
        $categoryRepository->copyDefaultsForUser($owner->id);
        $categoryRepository->copyDefaultsForUser($user->id);
        $category = $user->categories()->sole();
        $this->actingAs($user);

        $this->get('/categories')->assertOk()->assertSee('Kategori Bawaan')
            ->assertDontSee('Kategori Pribadi')->assertSee('Edit kategori Kategori Bawaan')
            ->assertSee('Hapus kategori Kategori Bawaan');
        $this->get('/subscriptions/create')->assertOk()->assertSee('Kategori Bawaan')->assertDontSee('Kategori Pribadi');

        foreach ([$systemCategory, $privateCategory] as $inaccessibleCategory) {
            $this->get("/categories/{$inaccessibleCategory->id}/edit")->assertForbidden();
            $this->put("/categories/{$inaccessibleCategory->id}", ['name' => 'Diubah'])->assertForbidden();
            $this->put("/categories/{$inaccessibleCategory->id}", ['name' => ''])->assertForbidden();
            $this->delete("/categories/{$inaccessibleCategory->id}")->assertForbidden();
            $this->assertDatabaseHas('categories', ['id' => $inaccessibleCategory->id, 'name' => $inaccessibleCategory->name]);
        }

        $this->put("/categories/{$category->id}", ['name' => 'Kategori Saya'])
            ->assertRedirect('/categories')->assertSessionHasNoErrors();
        $this->assertSame('Kategori Bawaan', $systemCategory->fresh()->name);
        $this->assertDatabaseHas('categories', ['user_id' => $owner->id, 'name' => 'Kategori Bawaan']);
        $this->delete("/categories/{$category->id}")->assertRedirect('/categories');
        $this->get('/categories')->assertOk()->assertSee('Belum ada kategori.');
        $this->get('/subscriptions/create')->assertOk()->assertDontSee('Kategori Bawaan');
        $this->assertSame(0, $user->categories()->count());
    }

    /**
     * Pengujian migrasi memindahkan referensi lama dan menjaga kategori kustom dengan nama yang sama.
     */
    public function test_migration_preserves_subscriptions_and_existing_custom_categories(): void
    {
        $defaultCategory = Category::create(['name' => 'Hiburan', 'icon' => 'tv', 'color' => '#ef4444']);
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $existingCategory = $user->categories()->create(['name' => 'Hiburan', 'icon' => 'music', 'color' => '#2563eb']);
        $subscriptions = [];
        foreach ([$user, $otherUser] as $subscriber) {
            $subscriptions[] = $subscriber->subscriptions()->create([
                'category_id' => $defaultCategory->id, 'name' => 'Netflix', 'price' => 186000, 'currency' => 'IDR',
                'billing_period' => 'MONTHLY', 'next_payment_date' => now()->addDays(3)->toDateString(), 'status' => 'ACTIVE',
            ]);
        }

        $migration = require database_path('migrations/2026_10_07_000000_assign_default_categories_to_users.php');
        $migration->up();
        $migration->up();

        $this->assertSame(1, $user->categories()->count());
        $this->assertSame(1, $otherUser->categories()->count());
        $this->assertSame($existingCategory->id, $subscriptions[0]->fresh()->category_id);
        $this->assertSame('music', $existingCategory->fresh()->icon);
        $this->assertSame('#2563eb', $existingCategory->fresh()->color);
        foreach ($subscriptions as $subscription) {
            $subscription->refresh();
            $this->assertSame($subscription->user_id, $subscription->category->user_id);
            $this->assertSame('Netflix', $subscription->name);
            $this->assertSame('186000.00', $subscription->price);
        }
        $this->assertSame(null, $defaultCategory->fresh()->user_id);
    }

    /**
     * Pengujian registrasi membuat salinan kategori awal yang dimiliki oleh akun baru.
     */
    public function test_registration_creates_categories_owned_by_the_new_user(): void
    {
        $defaultCategory = Category::create(['name' => 'Hiburan', 'icon' => 'tv', 'color' => '#ef4444']);
        $this->post('/register', [
            'name' => 'Pengguna Baru', 'email' => 'category-register@example.test',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', 'category-register@example.test')->sole();
        $category = $user->categories()->sole();
        $this->assertNotSame($defaultCategory->id, $category->id);
        $this->assertSame('Hiburan', $category->name);
        $this->assertSame('tv', $category->icon);
        $this->assertSame('#ef4444', $category->color);
        $this->get('/categories')->assertOk()->assertSee('Edit kategori Hiburan')->assertSee('Hapus kategori Hiburan');
    }

    /**
     * Pengujian isian tidak valid ditolak sebelum nama, ikon, atau warna kategori berubah.
     */
    public function test_name_icon_and_color_are_validated(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->create(['name' => 'Hiburan', 'icon' => 'tv', 'color' => '#ef4444']);
        $this->actingAs($user);

        foreach (['', '   ', ['Nama'], str_repeat('a', 101)] as $name) {
            $this->post('/categories', ['name' => $name])->assertSessionHasErrors('name');
            $this->put("/categories/{$category->id}", ['name' => $name])->assertSessionHasErrors('name');
        }
        foreach ([['icon', '<svg onload=alert(1)>'], ['icon', ['tv']], ['color', '#fff'],
            ['color', 'red;'], ['color', ['#ef4444']]] as [$field, $value]) {
            $this->post('/categories', ['name' => 'Baru', $field => $value])->assertSessionHasErrors($field);
            $this->put("/categories/{$category->id}", ['name' => 'Baru', $field => $value])->assertSessionHasErrors($field);
        }

        $this->assertSame(1, $user->categories()->count());
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Hiburan', 'icon' => 'tv']);
        $this->assertDatabaseMissing('activity_logs', ['entity' => 'Category']);
    }

    /**
     * Pengujian nama unik per pengguna tetap mengizinkan nama yang sama pada akun berbeda.
     */
    public function test_names_are_unique_per_user(): void
    {
        $owner = User::factory()->create();
        $owner->categories()->create(['name' => 'Hiburan']);
        $user = User::factory()->create();
        $category = $user->categories()->create(['name' => 'Kerja']);
        $this->actingAs($user)->post('/categories', ['name' => 'Hiburan'])
            ->assertRedirect('/categories')->assertSessionHasNoErrors();
        $this->post('/categories', ['name' => ' Hiburan '])->assertSessionHasErrors('name');
        $this->put("/categories/{$category->id}", ['name' => 'Hiburan'])->assertSessionHasErrors('name');
        $this->assertSame(2, $user->categories()->count());
        $this->assertSame('Kerja', $category->fresh()->name);
    }

    /**
     * Pengujian penghapusan kategori yang dipakai ditolak tanpa mengubah data langganan.
     */
    public function test_a_category_in_use_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->create(['name' => 'Hiburan']);
        $subscription = $user->subscriptions()->create([
            'category_id' => $category->id, 'name' => 'Netflix', 'price' => 186000, 'currency' => 'IDR',
            'billing_period' => 'MONTHLY', 'next_payment_date' => now()->addDays(3)->toDateString(), 'status' => 'ACTIVE',
        ]);

        $this->actingAs($user)->from('/categories')->delete("/categories/{$category->id}")
            ->assertRedirect('/categories')->assertSessionHasErrors('category');
        $this->withCookie(config('session.cookie'), session()->getId())->get('/categories')
            ->assertOk()->assertSee('Kategori masih digunakan oleh langganan.');
        $this->assertSame($category->id, $subscription->fresh()->category_id);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('activity_logs', ['entity' => 'Category', 'action' => 'DELETE']);
    }

    /**
     * Pengujian markup dalam nama dan data lama tidak dijalankan sebagai HTML atau CSS pada daftar.
     */
    public function test_category_values_are_escaped_in_views(): void
    {
        $user = User::factory()->create();
        $name = '<script>alert("category")</script>';
        $category = $user->categories()->create([
            'name' => $name, 'icon' => '<svg onload=alert(1)>', 'color' => 'red;',
        ]);
        $this->actingAs($user);

        $this->get('/categories')->assertOk()->assertSee($name)->assertDontSee($name, false)
            ->assertDontSee('color: red;', false)->assertDontSee('<svg onload=alert(1)>', false);
        $this->get("/categories/{$category->id}/edit")->assertOk()->assertSee($name)->assertDontSee($name, false);
    }

    /**
     * Pengujian error dan isian tambah serta edit dipulihkan setelah redirect validasi biasa.
     */
    public function test_modal_preserves_input_and_edit_context_after_validation(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->create(['name' => 'Hiburan']);
        $user->categories()->create(['name' => 'Kerja']);
        $this->actingAs($user);

        $this->from('/categories')->post('/categories', ['name' => 'Kerja', 'icon' => 'cloud', 'color' => '#10b981'])
            ->assertRedirect('/categories')->assertSessionHasErrors('name');
        $this->withCookie(config('session.cookie'), session()->getId())->get('/categories')
            ->assertOk()->assertSee('Tambah Kategori')->assertSee('value="Kerja"', false)
            ->assertSee('value="#10b981"', false)->assertSee('Nama kategori sudah digunakan di akun Anda.');

        $this->from('/categories')->post("/categories/{$category->id}", [
            '_method' => 'PUT', 'category_id' => $category->id, 'name' => 'Kerja', 'icon' => 'music', 'color' => '#2563eb',
        ])->assertRedirect('/categories')->assertSessionHasErrors('name')->assertSessionHasInput('category_id', $category->id);
        $this->withCookie(config('session.cookie'), session()->getId())->get('/categories')
            ->assertOk()->assertSee('Edit Kategori')->assertSee('value="Kerja"', false);

        $this->from('/categories')->post('/categories', ['name' => ['Tidak valid'], 'icon' => ['tv'], 'color' => ['red']])
            ->assertSessionHasErrors(['name', 'icon', 'color']);
        $this->withCookie(config('session.cookie'), session()->getId())->get('/categories')->assertOk();
        $this->assertSame('Hiburan', $category->fresh()->name);
    }
}
