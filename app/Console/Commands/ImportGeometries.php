<?php

namespace App\Console\Commands;

use App\Models\TerritoryGeometry;
use App\Models\TerritoryName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Geometry;

class ImportGeometries extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:geometries {geoJson*}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->resetTable(TerritoryGeometry::class);

        foreach ($this->argument('geoJson') as $fileName) {
            $this->readGeoJson(File::get($fileName));
        }

        return Command::SUCCESS;
    }

    private function normalizeFeatureId(string $id): string
    {
        return Str::of($id)->rtrim('+')->before('(');
    }

    private function readGeoJson(string $geoJson)
    {
        $featureCollection = json_decode($geoJson);
        foreach ($featureCollection->features as $feature) {
            $name = $this->normalizeFeatureId($feature->properties->id);
            $territoryName = TerritoryName::where('name', $name)->whereIn('territory_type', ['unitary_state', 'federal_state', 'federated_state', 'dependency'])->first();
            if (! $territoryName) {
                $this->warn("Could not find territory for $name");
            } else {
                $territoryName->territory->geometry()->create(['land' => Geometry::fromJson(json_encode($feature->geometry), 4326)]);
            }
        }
    }

    private function resetTable($model)
    {
        $model::query()->delete();
        $tableName = with(new $model)->getTable();
        DB::statement("ALTER TABLE $tableName AUTO_INCREMENT = 1;");
    }
}
