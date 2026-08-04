<?php

namespace App\Http\Middleware;

use App\Models\Amostra;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'can' => [
                    // Class-level check only: "does this user hold permissão for at least
                    // one atividade at all". Whether they can update/delete a *specific*
                    // amostra depends on that amostra's own atividade, so those checks are
                    // computed per-row in AmostraController and shipped as `can_update` /
                    // `can_delete` on each amostra instead of living here.
                    'amostras' => [
                        'create' => fn () => $request->user()?->can('create', Amostra::class) ?? false,
                    ],
                ],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
