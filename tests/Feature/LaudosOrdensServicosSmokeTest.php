<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LaudosOrdensServicosSmokeTest extends TestCase
{
    public function test_laudos_and_ordens_servicos_pages_render_for_authenticated_user(): void
    {
        $user = User::where('username', 'luis.amaral')->firstOrFail();

        $this->actingAs($user)
            ->get('/laudos')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Laudos/Index')
                ->has('laudos.data')
            );

        $this->actingAs($user)
            ->get('/ordens_servicos')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('OrdensServicos/Index')
                ->has('ordensServicos.data')
            );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/laudos')->assertRedirect(route('login'));
        $this->get('/ordens_servicos')->assertRedirect(route('login'));
    }
}
