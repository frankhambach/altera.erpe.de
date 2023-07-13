<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::enforceMorphMap([
            'area' => 'App\Models\Area',
            'continent' => 'App\Models\Continent',
            'country' => 'App\Models\Country',
            'dependency' => 'App\Models\Dependency',
            'federal_state' => 'App\Models\FederalState',
            'federated_state' => 'App\Models\FederatedState',
            'landmass' => 'App\Models\Landmass',
            'quarter' => 'App\Models\Quarter',
            'region' => 'App\Models\Region',
            'unitary_state' => 'App\Models\UnitaryState',
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
