<?php

use App\Http\Controllers\AmostraController;
use App\Http\Controllers\SolicitacaoServicoController;
use App\Http\Controllers\ArquivoCRAController;
use App\Http\Controllers\ArquivoLaboratorioController;
use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\EmpregadoController;
use App\Http\Controllers\ExecucaoAnaliseController;
use App\Http\Controllers\FracaoAmostraController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\LaudoController;
use App\Http\Controllers\OrdemServicoController;
use App\Http\Controllers\PerfilAcessoController;
use App\Http\Controllers\PermissaoAtividadeController;
use App\Http\Controllers\PermissaoUnidadeOperacionalController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\UnidadeOperacionalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ListaSolicitacaoServicoController;
use App\Http\Controllers\CraReceberAmostraController;
use App\Http\Controllers\PlanoAcaoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerfilSelecaoController;
use App\Http\Controllers\SolicitanteController;
use App\Http\Controllers\DropdownController;

use App\Models\ArquivoCRA;
use App\Models\ArquivoLaboratorio;
use App\Models\Empregado;
use App\Models\FracaoAmostra;
use App\Models\Historico;
use App\Models\Laudo;
// use App\Models\ExecucaoAnalise
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Página Inicial do Site
Route::get('/', [HomeController::class, 'index'])->name('home');

// Tela de login
Route::get('/login', [LoginController::class, 'index'])->name('login');

// Processar os dados do login
Route::post('/login', [LoginController::class, 'loginProcess'])->name('login.process');

// Tela de login
//Route::get('/login', [AuthController::class, 'index'])->name('login');

// Processar os dados do login
//Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');

// Logout
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Formulário cadastrar novo usuário
Route::get('/register', [LoginController::class, 'create'])->name('register');

// Receber os dados do formulário e cadastrar novo usuário
Route::post('/register', [LoginController::class, 'store'])->name('register.store');


// Tela de seleção de perfil exibida logo após o login
Route::get('/selecionar-perfil', [PerfilSelecaoController::class, 'index'])->name('perfil.selecionar')->middleware('auth');

// Página inicial do administrativo
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('auth');

// Amostras
Route::prefix('amostras')->middleware('auth')->group(function(){
    Route::get('/', [AmostraController::class, 'index'])->name('amostras.index');
    Route::get('/create', [AmostraController::class, 'create'])->name('amostras.create');
    Route::get('/{amostra}', [AmostraController::class, 'show'])->name('amostras.show');
    Route::post('/', [AmostraController::class, 'store'])->name('amostras.store');
    Route::get('/{amostra}/edit', [AmostraController::class, 'edit'])->name('amostras.edit');
    Route::put('/{amostra}', [AmostraController::class, 'update'])->name('amostras.update');
    Route::delete('/{amostra}', [AmostraController::class, 'destroy'])->name('amostras.destroy');
});

// Solicitação Serviços
Route::prefix('solicitacoes_servicos')->middleware('auth')->group(function(){
    Route::get('/', [SolicitacaoServicoController::class, 'index'])->name('solicitacoes_servicos.index');
    Route::get('/create', [SolicitacaoServicoController::class, 'create'])->name('solicitacoes_servicos.create');
    Route::get('/{solicitacao_servico}', [SolicitacaoServicoController::class, 'show'])->name('solicitacoes_servicos.show');
    //Route::get('solicitacoes_servicos.show', compact('solicitacao', 'amostras', 'ordens')), // alterado 02/06/2026
    //Route::get('/create2', [SolicitacaoServicoController::class, 'create'])->name('solicitacoes_servicos.create');
    Route::post('/store', [SolicitacaoServicoController::class, 'store'])->name('solicitacoes_servicos.store');
    Route::get('/{solicitacao_servico}/edit', [SolicitacaoServicoController::class, 'edit'])->name('solicitacoes_servicos.edit');
});

// routes/web.php
/*
Route::resource('solicitacao-servico', SolicitacaoServicoController::class)->only(['index']);

Route::post(
    'solicitacao-servico/{solicitacao}/amostras',
    [SolicitacaoServicoController::class, 'storeAmostra']
)->name('solicitacao-servico.amostras.store');
*/

// Arquivo CRA
Route::prefix('arquivos_cra')->group(function(){
    Route::get('/', [ArquivoCRAController::class, 'index'])->name('arquivos_cra.index');
//    Route::get('/create', [ArquivoCRAController::class, 'create'])->name('arquivos_cra.create');
    Route::get('/{arquivo_cra}', [ArquivoCRAController::class, 'show'])->name('arquivos_cra.show');
//    Route::get('/create', [ArquivoCRAController::class, 'create'])->name('arquivos_cra.create');
//    Route::post('/store', [ArquivoCRAController::class, 'store'])->name('arquivos_cra.store');
//    Route::get('/{arquivo_cra}/edit', [ArquivoCRAController::class, 'edit'])->name('arquivos_cra.edit');
});

// Arquivo Laboratório
Route::prefix('arquivos_laboratorios')->group(function(){
    Route::get('/', [ArquivoLaboratorioController::class, 'index'])->name('arquivos_laboratorios.index');
//    Route::get('/create', [ArquivoLaboratorioController::class, 'create'])->name('arquivos_laboratorios.create');
//    Route::get('/{arquivos_laboratorios}', [ArquivoLaboratorioController::class, 'show'])->name('arquivos_laboratorios.show');
//    Route::get('/create', [ArquivoLaboratorioController::class, 'create'])->name('arquivos_laboratorios.create');
//    Route::post('/store', [ArquivoLaboratorioController::class, 'store'])->name('arquivos_laboratorios.store');
//    Route::get('/{arquivos_laboratorios}/edit', [ArquivoLaboratorioController::class, 'edit'])->name('arquivos_laboratorios.edit');
});

// Atividade
Route::prefix('atividades')->group(function(){
    Route::get('/', [AtividadeController::class, 'index'])->name('atividades.index');
//    Route::get('/create', [AtividadeController::class, 'create'])->name('atividades.create');
    Route::get('/{atividade}', [AtividadeController::class, 'show'])->name('atividades.show');
//    Route::get('/create', [AtividadeController::class, 'create'])->name('atividades.create');
//    Route::post('/store', [AtividadeController::class, 'store'])->name('atividades.store');
    Route::get('/{atividade}/edit', [AtividadeController::class, 'edit'])->name('atividades.edit');
    Route::delete('/{atividade}', [AtividadeController::class, 'destroy'])->name('atividades.destroy');  
});

// Empregado
Route::prefix('empregados')->group(function(){
    Route::get('/', [EmpregadoController::class, 'index'])->name('empregados.index');
    Route::get('/create', [EmpregadoController::class, 'create'])->name('empregados.create');
    Route::get('/{empregado}', [EmpregadoController::class, 'show'])->name('empregados.show');
//    Route::get('/create', [EmpregadoController::class, 'create'])->name('empregados.create');
//    Route::post('/store', [EmpregadoController::class, 'store'])->name('empregados.store');
    Route::get('/{empregado}/edit', [EmpregadoController::class, 'edit'])->name('empregados.edit');
    Route::delete('/{empregado}', [EmpregadoController::class, 'destroy'])->name('empregados.destroy');
});

// FracaoAmostra
Route::prefix('fracoes_amostras')->group(function(){
    Route::get('/', [FracaoAmostraController::class, 'index'])->name('fracoes_amostras.index');
//    Route::get('/create', [FracaoAmostraController::class, 'create'])->name('fracoes_amostras.create');
    Route::get('/{fracao_amostra}', [FracaoAmostraController::class, 'show'])->name('fracoes_amostras.show');
//    Route::get('/create', [FracaoAmostraController::class, 'create'])->name('fracoes_amostras.create');
//    Route::post('/store', [FracaoAmostraController::class, 'store'])->name('fracoes_amostras.store');
    Route::get('/{fracao_amostra}/edit', [FracaoAmostraController::class, 'edit'])->name('fracoes_amostras.edit');
    Route::delete('/{fracao_amostra}', [FracaoAmostraController::class, 'destroy'])->name('fracoes_amostras.destroy');
});

// Execucao Analise
Route::prefix('execucoes_analises')->group(function(){
    Route::get('/', [ExecucaoAnaliseController::class, 'index'])->name('execucoes_analises.index');
    Route::get('/create', [ExecucaoAnaliseController::class, 'create'])->name('execucoes_analises.create');
    Route::get('/{execucao_analise}', [ExecucaoAnaliseController::class, 'show'])->name('execucoes_analises.show');
//    Route::get('/create', [FracaoAmostraController::class, 'create'])->name('fracoes_amostras.create');
    Route::post('/store', [ExecucaoAnaliseController::class, 'store'])->name('execucoes_analises.store');
//    Route::get('/{fracaoAmostra}/edit', [FracaoAmostraController::class, 'edit'])->name('fracoes_amostras.edit');
});

// Historico
Route::prefix('historicos')->group(function(){
    Route::get('/', [HistoricoController::class, 'index'])->name('historicos.index');
//    Route::get('/create', [HistoricoController::class, 'create'])->name('historicos.create');
//    Route::get('/{historico}', [HistoricoController::class, 'show'])->name('historicos.show');
//    Route::get('/create', [HistoricoController::class, 'create'])->name('historicos.create');
//    Route::post('/store', [HistoricoController::class, 'store'])->name('historicos.store');
//    Route::get('/{historico}/edit', [HistoricoController::class, 'edit'])->name('historicos.edit');
});

// Laudo
Route::prefix('laudos')->middleware('auth')->group(function(){
    Route::get('/', [LaudoController::class, 'index'])->name('laudos.index');
//    Route::get('/create', [LaudoController::class, 'create'])->name('laudos.create');
//    Route::get('/{laudo}', [LaudoController::class, 'show'])->name('laudos.show');
//    Route::get('/create', [LaudoController::class, 'create'])->name('laudos.create');
//    Route::post('/store', [LaudoController::class, 'store'])->name('laudos.store');
//    Route::get('/{laudo}/edit', [LaudoController::class, 'edit'])->name('laudos.edit');
});

// Ordem Servico
Route::prefix('ordens_servicos')->middleware('auth')->group(function(){
    Route::get('/', [OrdemServicoController::class, 'index'])->name('ordens_servicos.index');
//    Route::get('/create', [OrdemServicoController::class, 'create'])->name('ordensServicos.create');
//    Route::get('/{ordensServico}', [OrdemServicoController::class, 'show'])->name('ordensServicos.show');
//    Route::get('/create', [OrdemServicoController::class, 'create'])->name('ordensServicos.create');
//    Route::post('/store', [OrdemServicoController::class, 'store'])->name('ordensServicos.store');
//    Route::get('/{ordensServico}/edit', [OrdemServicoController::class, 'edit'])->name('ordensServicos.edit');
});

// Perfil Acesso
Route::prefix('perfis_acessos')->group(function(){
    Route::get('/', [PerfilAcessoController::class, 'index'])->name('perfis_acessos.index');
//    Route::get('/create', [PerfilAcessoController::class, 'create'])->name('perfisAccessos.create');
    Route::get('/{perfil_acesso}', [PerfilAcessoController::class, 'show'])->name('perfis_acessos.show');
//    Route::get('/create', [PerfilAcessoController::class, 'create'])->name('perfisAccessos.create');
//    Route::post('/store', [PerfilAcessoController::class, 'store'])->name('perfisAccessos.store');
    Route::get('/{perfil_acesso}/edit', [PerfilAcessoController::class, 'edit'])->name('perfis_acessos.edit');
    Route::delete('/{perfil_acesso}', [PerfilAcessoController::class, 'destroy'])->name('perfis_acessos.destroy');
});

// Permissão Atividade
Route::prefix('permissoes_atividades')->group(function(){
    Route::get('/', [PermissaoAtividadeController::class, 'index'])->name('permissoes_atividades.index');
//    Route::get('/create', [PermissaoAtividadeController::class, 'create'])->name('permissoesAtividades.create');
    Route::get('/{permissao_atividade}', [PermissaoAtividadeController::class, 'show'])->name('permissoes_atividades.show');
//    Route::get('/create', [PermissaoAtividadeController::class, 'create'])->name('permissoesAtividades.create');
//    Route::post('/store', [PermissaoAtividadeController::class, 'store'])->name('permissoesAtividades.store');
    Route::get('/{permissao_atividade}/edit', [PermissaoAtividadeController::class, 'edit'])->name('permissoes_atividades.edit');
    Route::delete('/{permissao_atividade}', [PerfilAcessoController::class, 'destroy'])->name('permissoes_atividades.destroy');
});

// Permissão Unidade Operacional
Route::prefix('permissoes_unidades_operacionais')->group(function(){
    Route::get('/', [PermissaoUnidadeOperacionalController::class, 'index'])->name('permissoesUnidadesOperacionais.index');
//    Route::get('/create', [PermissaoUnidadeOperacionalController::class, 'create'])->name('permissoesUnidadesOperacionais.create');
    Route::get('/{permissao_unidade_operacional}', [PermissaoUnidadeOperacionalController::class, 'show'])->name('permissoes_unidades_operacionais.show');
//    Route::get('/create', [PermissaoUnidadeOperacionalController::class, 'create'])->name('permissoesUnidadesOperacionais.create');
//    Route::post('/store', [PermissaoUnidadeOperacionalController::class, 'store'])->name('permissoesUnidadesOperacionais.store');
    Route::get('/{permissao_unidade_operacional}/edit', [PermissaoUnidadeOperacionalController::class, 'edit'])->name('permissoes_unidades_operacionais.edit');
    Route::delete('/{permissao_unidade_operacional}', [PermissaoUnidadeOperacionalController::class, 'destroy'])->name('permissoes_unidades_operacionais.destroy');    
});

// Serviços
Route::prefix('servicos')->group(function(){
    Route::get('/', [ServicoController::class, 'index'])->name('servicos.index');
    Route::get('/create', [ServicoController::class, 'create'])->name('servicos.create');
    Route::get('/{servico}', [ServicoController::class, 'show'])->name('servicos.show');
    Route::get('/create', [ServicoController::class, 'create'])->name('servicos.create');
    Route::post('/store', [ServicoController::class, 'store'])->name('servicos.store');
    Route::get('/{servico}/edit', [ServicoController::class, 'edit'])->name('servicos.edit');
    Route::delete('/{servico}', [ServicoController::class, 'destroy'])->name('servicos.destroy');    
});

// Unidade Operacional
Route::prefix('unidades_operacionais')->group(function(){
    Route::get('/', [UnidadeOperacionalController::class, 'index'])->name('unidades_operacionais.index');
//    Route::get('/create', [UnidadeOperacionalController::class, 'create'])->name('unidadesOperacionais.create');
    Route::get('/{unidade_operacional}', [UnidadeOperacionalController::class, 'show'])->name('unidades_operacionais.show');
//    Route::get('/create', [UnidadeOperacionalController::class, 'create'])->name('unidadesOperacionais.create');
//    Route::post('/store', [UnidadeOperacionalController::class, 'store'])->name('unidadesOperacionais.store');
    Route::get('/{unidade_operacional}/edit', [UnidadeOperacionalController::class, 'edit'])->name('unidades_operacionais.edit');
    Route::delete('/{unidade_operacional}', [UnidadeOperacionalController::class, 'destroy'])->name('unidades_operacionais.destroy'); 
    Route::get('/{unidade_operacional_id}/servicos', [ServicoController::class, 'porUnidadeOperacional'])->name('unidades_operacionais.servicos');
});

// Usuários
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/{user}/edit-password', [UserController::class, 'editPassword'])->name('users.edit_password');
    Route::put('/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update_password');

});

// Usuários Status
/*
Route::prefix('user-statuses')->group(function () {
    Route::get('/', [UserStatusController::class, 'index'])->name('user_statuses.index');
    Route::get('/create', [UserStatusController::class, 'create'])->name('user_statuses.create');
    Route::get('/{userStatus}', [UserStatusController::class, 'show'])->name('user_statuses.show');
    Route::post('/', [UserStatusController::class, 'store'])->name('user_statuses.store');
    Route::get('/{userStatus}/edit', [UserStatusController::class, 'edit'])->name('user_statuses.edit');
    Route::put('/{userStatus}', [UserStatusController::class, 'update'])->name('user_statuses.update');
    Route::delete('/{userStatus}', [UserStatusController::class, 'destroy'])->name('user_statuses.destroy');
});
*/

Route::get('/listasolicitacaoservico', [ListaSolicitacaoServicoController::class, 'index'])
    ->name('listasolicitacaoservico.index');

Route::get('/cra', function () {
    return Inertia::render('Cra');
})->name('cra');

Route::prefix('cra/receber-amostra')->middleware('auth')->group(function () {
    Route::get('/', [CraReceberAmostraController::class, 'index'])->name('cra.receber-amostra.index');
    Route::get('/solicitacao/{solicitacao_servico}', [CraReceberAmostraController::class, 'ordens'])->name('cra.receber-amostra.ordens');
    Route::get('/{ordem_servico}', [CraReceberAmostraController::class, 'show'])->name('cra.receber-amostra.show');
    Route::post('/{ordem_servico}', [CraReceberAmostraController::class, 'store'])->name('cra.receber-amostra.store');
});

Route::get('/laboratorio', function () {
    return Inertia::render('Laboratorio');
})->name('laboratorio');

Route::get('/resptec', function () {
    return Inertia::render('Resptec');
})->name('resptec');

/*
Route::get('/solicitante', function () {
    return view('/solicitante/index');
});
*/

Route::get('/solicitante', [SolicitanteController::class, 'index'])->name('solicitante.index');

Route::get('dropdown', [DropdownController::class, 'index']);
Route::post('api/fetch-servicos', [DropdownController::class, 'fetchServico']);

