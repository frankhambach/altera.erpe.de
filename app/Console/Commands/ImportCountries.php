<?php

namespace App\Console\Commands;

use App\Enums\GrammaticalNumber;
use App\Models\Area;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Culture;
use App\Models\Dependency;
use App\Models\FederalState;
use App\Models\FederatedState;
use App\Models\Landmass;
use App\Models\Quarter;
use App\Models\Region;
use App\Models\TerritoryDemonym;
use App\Models\TerritoryName;
use App\Models\TerritoryOfficialName;
use App\Models\UnitaryState;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ImportCountries extends Command
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
    protected $signature = 'import:countries {spreadsheet}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->resetTable(TerritoryName::class);
        $this->resetTable(TerritoryOfficialName::class);
        $this->resetTable(TerritoryDemonym::class);
        $this->resetTable(Culture::class);
        $this->resetTable(Dependency::class);
        $this->resetTable(FederatedState::class);
        $this->resetTable(FederalState::class);
        $this->resetTable(UnitaryState::class);
        $this->resetTable(Country::class);
        $this->resetTable(Area::class);
        $this->resetTable(Quarter::class);
        $this->resetTable(Region::class);
        $this->resetTable(Continent::class);
        $this->resetTable(Landmass::class);

        $reader = new Xlsx();
        $fileName = $this->argument('spreadsheet');
        $spreadsheet = $reader->load($fileName);

        $landmasses = new Collection();
        $continents = new Collection();
        $regions = new Collection();
        $quarters = new Collection();
        $areas = new Collection();

        $cultures = new Collection();

        $countrySheet = $spreadsheet->getSheetByName('GeoRef');
        $maxRowIndex = $countrySheet->getHighestRow();
        for ($rowIndex = 2; $rowIndex <= $maxRowIndex; $rowIndex++) {
            $regionName = $countrySheet->getCell([3, $rowIndex])->getValue();
            $areaName = $countrySheet->getCell([4, $rowIndex])->getValue();
            $cultureName = $countrySheet->getCell([5, $rowIndex])->getValue();
            $grammaticalNumber = $this->readGrammaticalNumber($countrySheet->getCell([6, $rowIndex])->getValue());
            if (Str::startsWith($cultureName, ['the ', 'The '])) {
                $grammaticalNumber = GrammaticalNumber::Singular;
            }

            $cultureName = $this->normalizeCountryName($cultureName);

            $cultures[$cultureName] = ['name' => $cultureName, 'grammaticalNumber' => $grammaticalNumber, 'areaName' => $areaName, 'regionName' => $regionName];
        }

        $areaSheet = $spreadsheet->getSheetByName('Geography Raw');
        $maxRowIndex = $areaSheet->getHighestRow();
        for ($rowIndex = 2; $rowIndex <= $maxRowIndex; $rowIndex++) {
            if ($areaSheet->getCell([2, $rowIndex])->getValue() === null) {
                break;
            }

            $areaName = $this->normalizeAreaName($areaSheet->getCell([2, $rowIndex])->getValue());
            $areaGrammaticalNumber = $this->readGrammaticalNumber($areaSheet->getCell([1, $rowIndex])->getValue());

            $landmass = $this->getOrCreateTerritory(Landmass::class, $landmasses, ['name' => $areaSheet->getCell([6, $rowIndex])->getValue()]);
            $continent = $this->getOrCreateTerritory(Continent::class, $continents, ['name' => $areaSheet->getCell([5, $rowIndex])->getValue(), 'landmassId' => $landmass->id]);
            $region = $this->getOrCreateTerritory(Region::class, $regions, ['name' => $areaSheet->getCell([4, $rowIndex])->getValue(), 'continentId' => $continent->id]);

            $regionsOfCountriesOfArea = $cultures
                ->where(fn ($attributes, $countryName) => $attributes['areaName'] == $areaName)
                ->map(fn ($attributes) => $attributes['regionName'])
                ->unique();
            $areaAttributes = [];
            if ($regionsOfCountriesOfArea->count() == 1) {
                $quarter = $this->getOrCreateTerritory(Quarter::class, $quarters, ['name' => $areaSheet->getCell([3, $rowIndex])->getValue(), 'regionId' => $region->id]);
                $areaAttributes = ['quarterId' => $quarter->id];
            }

            $area = $this->getOrCreateTerritory(Area::class, $areas, ['name' => $areaName, 'grammaticalNumber' => $areaGrammaticalNumber, ...$areaAttributes]);
        }

        $federalStates = new Collection();
        $countries = new Collection();

        $countrySheet = $spreadsheet->getSheetByName('Columns of the World');
        $maxRowIndex = $countrySheet->getHighestRow();
        foreach ([FederalState::class, FederatedState::class, Dependency::class] as $iteration) {
            for ($rowIndex = 4; $rowIndex <= $maxRowIndex; $rowIndex++) {
                if ($countrySheet->getCell([1, $rowIndex])->getValue() === null || $countrySheet->getCell([2, $rowIndex])->getValue() === null) {
                    continue;
                }

                $countryName = $this->normalizeCountryName($countrySheet->getCell([1, $rowIndex])->getValue());
                $cultureName = $this->normalizeCountryName($countrySheet->getCell([2, $rowIndex])->getValue());
                $countryCode = $countrySheet->getCell([7, $rowIndex])->getValue();
                $countryDemonymPrefix = $countrySheet->getCell([8, $rowIndex])->getValue();
                if ($countryDemonymPrefix === 0) {
                    $countryDemonymPrefix = null;
                }

                $countryDemonymNoun = $countrySheet->getCell([9, $rowIndex])->getValue();
                $countryDemonymAdjective = $countryDemonymNoun;
                if ($countryDemonymNoun === 0) {
                    $countryDemonymNoun = null;
                    $countryDemonymAdjective = null;
                }

                $capital = $countrySheet->getCell([11, $rowIndex])->getValue();
                $cultureReligion = $countrySheet->getCell([45, $rowIndex])->getValue();
                $cultureSect = $countrySheet->getCell([44, $rowIndex])->getValue();
                $cultureLens = $countrySheet->getCell([41, $rowIndex])->getValue();
                $cultureLaw = $countrySheet->getCell([42, $rowIndex])->getValue();
                $cultureJurisprudence = $countrySheet->getCell([43, $rowIndex])->getValue();

                $type = $countrySheet->getCell([3, $rowIndex])->getValue();
                if (! $cultures->has($cultureName) && $type != 'federation' && $type != 'confederation') {
                    $this->warn("Could not determine area for $cultureName");
                } else {
                    switch ($type) {
                        case 'federation':
                        case 'confederation':
                            if ($iteration == FederalState::class) {
                                $federalStateName = $countryName;
                                $federalStateSlug = Str::slug($federalStateName);
                                $areaName = $this->normalizeAreaName($countrySheet->getCell([12, $rowIndex])->getValue());
                                $regionName = $countrySheet->getCell([14, $rowIndex])->getValue();

                                $federalState = FederalState::create(['slug' => $federalStateSlug]);
                                $federalState->name()->create(['name' => $federalStateName, 'grammaticalNumber' => GrammaticalNumber::None]);
                                if ($countryDemonymPrefix !== null || $countryDemonymNoun !== null) {
                                    $federalState->demonym()->create(['prefix' => $countryDemonymPrefix, 'noun' => $countryDemonymNoun, 'adjective' => $countryDemonymAdjective]);
                                }

                                $federalState->country()->create(['slug' => $federalStateSlug, 'code' => $countryCode, 'capital' => $capital, 'areaId' => $areas[$areaName]->id, 'regionId' => $regions[$regionName]->id]);

                                $federalStates[$federalStateName] = $federalState;
                                $countries[$federalStateName] = $federalState->country;
                            }
                            break;
                        case 'codependency':
                        case 'interdependency':
                        case 'archidistrict':
                            if ($iteration == FederatedState::class) {
                                $federalStateName = $countryName;
                                $federatedStateName = $cultureName;
                                $federatedStateSlug = Str::slug($federatedStateName);
                                [$federalStateCode, $federatedStateCode] = explode('-', $countryCode);

                                if (! $federalStates->has($federalStateName)) {
                                    $this->warn("Could not determine federal state $federalStateName for federated state $federatedStateName");
                                } else {
                                    $federalState = $federalStates[$federalStateName];
                                    $federatedState = $federalState->federatedStates()->create(['slug' => $federatedStateSlug, 'code' => $federatedStateCode]);
                                    $federatedState->name()->create(['name' => $federatedStateName, 'grammaticalNumber' => $cultures[$federatedStateName]['grammaticalNumber']]);
                                    $federatedState->culture()->create(['slug' => $federatedStateSlug]);
                                }
                            }
                            break;
                        case 'independency':
                        case 'mandate':
                            if ($iteration == FederatedState::class) {
                                $unitaryStateName = $countryName;
                                if ($type == 'mandate') {
                                    $countryCode = Str::substr($countryCode, 3);
                                }

                                if (! $countries->has($unitaryStateName)) {
                                    $unitaryStateSlug = Str::slug($unitaryStateName);

                                    $unitaryState = UnitaryState::create(['slug' => $unitaryStateSlug]);
                                    $unitaryState->name()->create(['name' => $unitaryStateName, 'grammaticalNumber' => $cultures[$unitaryStateName]['grammaticalNumber']]);
                                    if ($countryDemonymPrefix !== null || $countryDemonymNoun !== null) {
                                        $unitaryState->demonym()->create(['prefix' => $countryDemonymPrefix, 'noun' => $countryDemonymNoun, 'adjective' => $countryDemonymAdjective]);
                                    }

                                    $unitaryState->culture()->create(['slug' => $unitaryStateSlug]);
                                    $unitaryState->country()->create(['slug' => $unitaryStateSlug, 'code' => $countryCode, 'capital' => $capital, 'areaId' => $areas[$cultures[$unitaryStateName]['areaName']]->id, 'regionId' => $regions[$cultures[$unitaryStateName]['regionName']]->id]);

                                    $countries[$unitaryStateName] = $unitaryState->country;
                                }
                            }
                            break;
                        case 'dependency':
                            if ($iteration == Dependency::class) {
                                $dependencyName = $cultureName;
                                $dependencySlug = Str::slug($dependencyName);

                                if (! $countries->has($countryName)) {
                                    $this->warn("Could not determine country $countryName for dependency $dependencyName");
                                } else {
                                    $country = $countries[$countryName];
                                    $dependency = $country->dependencies()->create(['slug' => $dependencySlug, 'areaId' => $areas[$cultures[$dependencyName]['areaName']]->id, 'regionId' => $regions[$cultures[$dependencyName]['regionName']]->id]);
                                    $dependency->name()->create(['name' => $dependencyName, 'grammaticalNumber' => $cultures[$dependencyName]['grammaticalNumber']]);
                                    if ($countryDemonymPrefix !== null || $countryDemonymNoun !== null) {
                                        $dependency->demonym()->create(['prefix' => $countryDemonymPrefix, 'noun' => $countryDemonymNoun, 'adjective' => $countryDemonymAdjective]);
                                    }

                                    $dependency->culture()->create(['slug' => $dependencySlug]);
                                }
                            }
                            break;
                    }
                }
            }
        }

        return Command::SUCCESS;
    }

    private function getOrCreateTerritory($model, Collection $territories, array $attributes = [])
    {
        $attributes = collect(['grammaticalNumber' => GrammaticalNumber::None])->merge($attributes);

        $territory = $territories->get($attributes['name']);
        if (! $territory) {
            $territory = $model::create([...$attributes->except(['name', 'grammaticalNumber']), 'slug' => Str::slug($attributes['name'])]);
            $territory->name()->create($attributes->only(['name', 'grammaticalNumber'])->all());
            $territories[$attributes['name']] = $territory;
        }

        return $territory;
    }

    private function normalizeAreaName(string $value): string
    {
        if (Str::startsWith($value, ['East ', 'West '])) {
            return Str::substr($value, 5);
        }

        if (Str::startsWith($value, ['North ', 'South '])) {
            return Str::substr($value, 6);
        }

        return $value;
    }

    private function normalizeCountryName(string $value): string
    {
        $value = Str::before($value, ' (');
        if (Str::startsWith($value, ['the ', 'The '])) {
            $value = Str::substr($value, 4);
        }

        if (Str::endsWith($value, '+')) {
            $value = Str::substr($value, 0, Str::length($value) - 1);
        }

        return $value;
    }

    private function readGrammaticalNumber(string $value): string
    {
        return match ($value) {
            '1' => 'singular',
            '2' => 'plural',
            default => 'none',
        };
    }

    private function resetTable($model)
    {
        $model::query()->delete();
        $tableName = with(new $model)->getTable();
        DB::statement("ALTER TABLE $tableName AUTO_INCREMENT = 1;");
    }
}
