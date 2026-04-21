<?php

namespace Tests\Feature\Client;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCabinetAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('client.login'));

        $response->assertStatus(200);
    }

    public function test_client_can_login_and_open_cabinet(): void
    {
        $client = Client::query()->create([
            'first_name' => 'Test',
            'last_name' => 'Client',
            'phone' => '+99361111111',
            'password' => 'password123',
        ]);

        $response = $this->post(route('client.login.post'), [
            'phone' => '+99361111111',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($client, 'client');
        $response->assertRedirect(route('client.cabinet'));
    }

    public function test_guest_client_is_redirected_from_cabinet_to_client_login(): void
    {
        $response = $this->get(route('client.cabinet'));

        $response->assertRedirect(route('client.login'));
    }
}
