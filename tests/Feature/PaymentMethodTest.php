<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pengujian pengguna dapat menambah, mengedit, dan menghapus metode pembayaran beserta log aktivitasnya.
     */
    public function test_user_can_create_edit_and_delete_an_alias(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($user);

        $this->get('/payment-methods')->assertOk()->assertSee('Belum ada metode pembayaran.');
        $this->get('/payment-methods/create')->assertOk()
            ->assertSee('name="name"', false)->assertDontSee('name="card_number"', false);

        $this->post('/payment-methods', [
            'name' => 'BCA Debit',
            'user_id' => $otherUser->id,
            'card_number' => '4111111111111111',
            'cvv' => '123',
            'pin' => '123456',
        ])->assertRedirect('/payment-methods')->assertSessionHas('success');

        $paymentMethod = $user->paymentMethods()->sole();
        $this->assertSame('BCA Debit', $paymentMethod->name);
        $this->assertDatabaseMissing('payment_methods', ['user_id' => $otherUser->id]);
        $this->get('/payment-methods')->assertOk()->assertSee('BCA Debit');
        $this->get("/payment-methods/{$paymentMethod->id}/edit")->assertOk()
            ->assertSee('value="BCA Debit"', false);

        $this->put("/payment-methods/{$paymentMethod->id}", ['name' => 'BCA Debit'])
            ->assertRedirect('/payment-methods')->assertSessionHasNoErrors();
        $this->put("/payment-methods/{$paymentMethod->id}", ['name' => 'Jenius'])
            ->assertRedirect('/payment-methods')->assertSessionHas('success');
        $this->assertDatabaseHas('payment_methods', ['id' => $paymentMethod->id, 'name' => 'Jenius']);

        $this->delete("/payment-methods/{$paymentMethod->id}")
            ->assertRedirect('/payment-methods')->assertSessionHas('success');
        $this->assertDatabaseMissing('payment_methods', ['id' => $paymentMethod->id]);

        foreach (['CREATE', 'UPDATE', 'DELETE'] as $action) {
            $this->assertDatabaseHas('activity_logs', [
                'user_id' => $user->id,
                'entity' => 'PaymentMethod',
                'entity_id' => $paymentMethod->id,
                'action' => $action,
            ]);
        }
    }

    /**
     * Pengujian nama wajib berupa teks, maksimal 100 karakter, dan unik untuk pengguna yang sama.
     */
    public function test_name_is_required_text_limited_and_unique_for_the_user(): void
    {
        $user = User::factory()->create();
        $paymentMethod = $user->paymentMethods()->create(['name' => 'BCA Debit']);
        $user->paymentMethods()->create(['name' => 'GoPay']);
        $this->actingAs($user);

        foreach (['', '   ', ['GoPay'], str_repeat('a', 101), 'GoPay', ' GoPay '] as $name) {
            $this->post('/payment-methods', ['name' => $name])->assertSessionHasErrors('name');
            $this->put("/payment-methods/{$paymentMethod->id}", ['name' => $name])
                ->assertSessionHasErrors('name');
        }

        $this->assertSame(2, $user->paymentMethods()->count());
        $this->assertSame('BCA Debit', $paymentMethod->fresh()->name);
    }

    /**
     * Pengujian pengguna berbeda dapat memakai nama metode pembayaran yang sama.
     */
    public function test_different_users_can_use_the_same_alias(): void
    {
        $owner = User::factory()->create();
        $owner->paymentMethods()->create(['name' => 'GoPay']);
        $owner->paymentMethods()->create(['name' => 'BCA Debit']);
        $user = User::factory()->create();
        $paymentMethod = $user->paymentMethods()->create(['name' => 'Jenius']);

        $this->actingAs($user)->post('/payment-methods', ['name' => 'GoPay'])
            ->assertRedirect('/payment-methods')->assertSessionHasNoErrors();
        $this->put("/payment-methods/{$paymentMethod->id}", ['name' => 'BCA Debit'])
            ->assertRedirect('/payment-methods')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('payment_methods', ['id' => $paymentMethod->id, 'name' => 'BCA Debit']);
        $this->post('/payment-methods', ['name' => 'Jenius'])
            ->assertRedirect('/payment-methods')->assertSessionHasNoErrors();
    }

    /**
     * Pengujian pengguna tidak dapat melihat, mengedit, atau menghapus metode pembayaran akun lain.
     */
    public function test_other_users_cannot_view_edit_or_delete_an_alias(): void
    {
        $owner = User::factory()->create();
        $paymentMethod = $owner->paymentMethods()->create(['name' => 'Alias Pribadi']);
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/payment-methods')->assertOk()->assertDontSee('Alias Pribadi');
        $this->get("/payment-methods/{$paymentMethod->id}/edit")->assertForbidden();
        $this->put("/payment-methods/{$paymentMethod->id}", ['name' => 'Diubah'])->assertForbidden();
        $this->put("/payment-methods/{$paymentMethod->id}", ['name' => ''])->assertForbidden();
        $this->delete("/payment-methods/{$paymentMethod->id}")->assertForbidden();
        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id, 'user_id' => $owner->id, 'name' => 'Alias Pribadi',
        ]);
    }

    /**
     * Pengujian nama metode pembayaran di-escape agar kode HTML tidak dijalankan pada daftar dan formulir edit.
     */
    public function test_aliases_are_escaped_in_the_list_and_edit_form(): void
    {
        $user = User::factory()->create();
        $name = '<script>alert("alias")</script>';
        $paymentMethod = $user->paymentMethods()->create(['name' => $name]);
        $this->actingAs($user);

        foreach (['/payment-methods', "/payment-methods/{$paymentMethod->id}/edit"] as $url) {
            $this->get($url)->assertOk()->assertSee($name)->assertDontSee($name, false);
        }
    }

    /**
     * Pengujian penghapusan ditolak saat metode pembayaran masih dipakai tanpa mengubah data langganan.
     */
    public function test_an_alias_in_use_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $paymentMethod = $user->paymentMethods()->create(['name' => 'BCA Debit']);
        $subscription = $user->subscriptions()->create([
            'payment_method_id' => $paymentMethod->id,
            'name' => 'Netflix',
            'price' => 186000,
            'currency' => 'IDR',
            'billing_period' => 'MONTHLY',
            'next_payment_date' => now()->addDays(3)->toDateString(),
            'status' => 'ACTIVE',
        ]);

        $this->actingAs($user)->from('/payment-methods')->delete("/payment-methods/{$paymentMethod->id}")
            ->assertRedirect('/payment-methods')->assertSessionHasErrors('payment_method');
        $this->withCookie(config('session.cookie'), session()->getId())->get('/payment-methods')
            ->assertOk()->assertSee('Metode pembayaran masih digunakan oleh langganan.');
        $this->assertDatabaseHas('payment_methods', ['id' => $paymentMethod->id]);
        $this->assertSame($paymentMethod->id, $subscription->fresh()->payment_method_id);
        $this->assertDatabaseMissing('activity_logs', ['entity' => 'PaymentMethod', 'action' => 'DELETE']);
    }

    /**
     * Pengujian submit biasa mengembalikan error, isian, dan pilihan edit setelah redirect validasi.
     */
    public function test_modal_preserves_input_and_edit_context_after_validation_redirect(): void
    {
        $user = User::factory()->create();
        $paymentMethod = $user->paymentMethods()->create(['name' => 'BCA Debit']);
        $user->paymentMethods()->create(['name' => 'GoPay']);
        $this->actingAs($user);

        $this->from('/payment-methods')->post('/payment-methods', ['name' => 'GoPay', '_method' => 'POST'])
            ->assertRedirect('/payment-methods')->assertSessionHasErrors('name')->assertSessionHasInput('name', 'GoPay');
        $this->withCookie(config('session.cookie'), session()->getId())->get('/payment-methods')
            ->assertOk()->assertSee('Tambah Metode Pembayaran')->assertSee('value="GoPay"', false)
            ->assertSee('Nama metode pembayaran sudah digunakan di akun Anda.');

        $this->from('/payment-methods')->post("/payment-methods/{$paymentMethod->id}", [
            'name' => 'GoPay',
            '_method' => 'PUT',
            'payment_method_id' => $paymentMethod->id,
        ])->assertRedirect('/payment-methods')->assertSessionHasErrors('name')
            ->assertSessionHasInput('payment_method_id', $paymentMethod->id);
        $this->withCookie(config('session.cookie'), session()->getId())->get('/payment-methods')
            ->assertOk()->assertSee('Edit Metode Pembayaran')->assertSee('value="GoPay"', false)
            ->assertSee('Nama metode pembayaran sudah digunakan di akun Anda.');

        $this->assertSame(2, $user->paymentMethods()->count());
        $this->assertSame('BCA Debit', $paymentMethod->fresh()->name);
    }
}
