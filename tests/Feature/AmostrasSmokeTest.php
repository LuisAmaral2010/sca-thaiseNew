<?php

namespace Tests\Feature;

use App\Models\Amostra;
use App\Models\SolicitacaoServico;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AmostrasSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_and_amostras_pages_render_for_authenticated_user(): void
    {
        $user = User::where('username', 'luis.amaral')->firstOrFail();
        // Amostra #1 belongs to a solicitação/atividade this user holds a
        // permissao_atividade grant for (verified via tinker against the real dev DB).
        $permittedAmostra = Amostra::findOrFail(1);
        $otherAmostra = Amostra::orderBy('amostra_id', 'DESC')->firstOrFail();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Dashboard'));

        $this->actingAs($user)
            ->get('/amostras')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Amostras/Index')
                ->has('amostras.data')
            );

        $this->actingAs($user)
            ->get('/amostras/' . $permittedAmostra->amostra_id)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Amostras/Show')
                ->where('amostra.amostra_id', $permittedAmostra->amostra_id)
            );

        $this->actingAs($user)
            ->get('/amostras/create')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Amostras/Create')
                ->has('solicitacoes')
            );

        // Positive case: user holds permissao_atividade for this amostra's atividade.
        $this->actingAs($user)
            ->get('/amostras/' . $permittedAmostra->amostra_id . '/edit')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Amostras/Edit'));

        // Negative case: an amostra outside the user's permitted atividades is blocked.
        if ($otherAmostra->amostra_id !== $permittedAmostra->amostra_id) {
            $this->actingAs($user)
                ->get('/amostras/' . $otherAmostra->amostra_id . '/edit')
                ->assertForbidden();
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/amostras')->assertRedirect(route('login'));
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_store_update_and_destroy_a_permitted_amostra(): void
    {
        $user = User::where('username', 'luis.amaral')->firstOrFail();
        $solicitacao = SolicitacaoServico::findOrFail(Amostra::findOrFail(1)->solicitacao_id);

        $payload = [
            'descricao' => 'Amostra de teste (smoke test)',
            'solicitacao_id' => $solicitacao->solicitacao_servico_id,
            'validade_dias' => 30,
            'condicao_armazenamento' => 'Refrigerado',
            'numero_cra' => 991234,
        ];

        $this->actingAs($user)
            ->post('/amostras', $payload)
            ->assertRedirect();

        $amostra = Amostra::where('numero_cra', 991234)->firstOrFail();

        $this->actingAs($user)
            ->put('/amostras/' . $amostra->amostra_id, [...$payload, 'descricao' => 'Amostra atualizada'])
            ->assertRedirect(route('amostras.show', ['amostra' => $amostra->amostra_id]));

        $this->assertSame('Amostra atualizada', $amostra->fresh()->descricao);

        $this->actingAs($user)
            ->delete('/amostras/' . $amostra->amostra_id)
            ->assertRedirect(route('amostras.index'));

        $this->assertNull(Amostra::find($amostra->amostra_id));
    }
}
