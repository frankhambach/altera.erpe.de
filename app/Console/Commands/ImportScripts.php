<?php

namespace App\Console\Commands;

use App\Models\Script;
use App\Models\ScriptSource;
use App\Models\TerritoryName;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ImportScripts extends Command
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
    protected $signature = 'import:scripts {spreadsheet}';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->resetTable(Script::class);
        $this->resetTable(ScriptSource::class);

        $reader = new Xlsx();
        $fileName = $this->argument('spreadsheet');
        $spreadsheet = $reader->load($fileName);

        $scriptSheet = $spreadsheet->getSheetByName('Pantographia');
        $maxRowIndex = $scriptSheet->getHighestRow();
        for ($rowIndex = 3; $rowIndex <= $maxRowIndex; $rowIndex++) {
            $code = $scriptSheet->getCell([4, $rowIndex])->getValue();
            if ($code) {
                $name = $scriptSheet->getCell([5, $rowIndex])->getValue();
                $type = $this->readType($scriptSheet->getCell([7, $rowIndex])->getValue());
                $case = $this->readCase($scriptSheet->getCell([8, $rowIndex])->getValue());
                $verticalOrientation = $this->readVerticalOrientation($scriptSheet->getCell([9, $rowIndex])->getValue());
                $horizontalOrientation = $this->readHorizontalOrientation($scriptSheet->getCell([10, $rowIndex])->getValue());
                $flow = $this->readFlow($scriptSheet->getCell([11, $rowIndex])->getValue());
                $fitting = $this->readFitting($scriptSheet->getCell([12, $rowIndex])->getValue());
                $format = $this->readFormat($scriptSheet->getCell([13, $rowIndex])->getValue());
                $sourceIsoCode = null;
                if ($scriptSheet->getStyle([18, $rowIndex])->getFill()->getStartColor()->getARGB() != 'FFD9D9D9') {
                    $sourceIsoCode = $scriptSheet->getCell([18, $rowIndex])->getValue();
                }

                $sourceName = $scriptSheet->getCell([16, $rowIndex])->getValue();
                $sourceNotes = $scriptSheet->getCell([20, $rowIndex])->getValue();

                $script = Script::create(['slug' => Str::slug($name), 'code' => $code, 'case' => $case, 'fitting' => $fitting, 'flow' => $flow, 'format' => $format, 'horizontalOrientation' => $horizontalOrientation, 'name' => $name, 'type' => $type, 'verticalOrientation' => $verticalOrientation]);
                $script->source()->create(['isoCode' => $sourceIsoCode, 'name' => $sourceName, 'notes' => $sourceNotes]);
            }
        }

        $languoidSheet = $spreadsheet->getSheetByName('Gaia Polyglotta');
        $maxRowIndex = $languoidSheet->getHighestRow();
        for ($rowIndex = 4; $rowIndex <= $maxRowIndex; $rowIndex++) {
            if ($languoidSheet->getCell([9, $rowIndex])->getValue()) {
                $cultureName = $this->normalizeCultureName($languoidSheet->getCell([9, $rowIndex])->getValue());
                $territoryName = TerritoryName::where('name', $cultureName)->whereIn('territory_type', ['unitary_state', 'federated_state', 'dependency'])->first();
                if (! $territoryName) {
                    $this->warn("Could not find a state or dependency named $cultureName");
                }

                $scriptName = $languoidSheet->getCell([10, $rowIndex])->getValue();
                $script = Script::where('name', $scriptName)->first();
                if (! $script) {
                    $this->warn("Could not find a script named $scriptName");
                }

                if ($territoryName && $script) {
                    $territoryName->territory->culture->scriptId = $script->id;
                    $territoryName->territory->culture->save();
                }
            }
        }

        return Command::SUCCESS;
    }

    private function normalizeCultureName(string $value): string
    {
        $value = Str::before($value, ' (');
        if (Str::startsWith($value, ['the ', 'The '])) {
            $value = Str::substr($value, 4);
        }

        return $value;
    }

    private function readCase(string $value): string
    {
        return match ($value) {
            'bicameral' => 'bicameral',
            'ideocameral' => 'ideocameral',
            'unicameral' => 'unicameral',
            default => 'none',
        };
    }

    private function readFitting(string $value): string
    {
        return match ($value) {
            'continual' => 'continual',
            'interpunctual' => 'interpunctual',
            'interspatial' => 'interspatial',
            default => 'none',
        };
    }

    private function readFlow(string $value): string
    {
        return match ($value) {
            'linear' => 'linear',
            'zigzag' => 'zig_zag',
            default => 'none',
        };
    }

    private function readFormat(string $value): string
    {
        return match ($value) {
            'columns' => 'columns',
            'rows' => 'rows',
            default => 'none',
        };
    }

    private function readHorizontalOrientation(string $value): string
    {
        return match ($value) {
            'left-to-right' => 'left_to_right',
            'right-to-left' => 'right_to_left',
            default => 'none',
        };
    }

    private function readType(string $value): string
    {
        return match ($value) {
            'abjad' => 'abjad',
            'abugida' => 'abugida',
            'alphabet' => 'alphabet',
            'charactery' => 'charactery',
            'featurary' => 'featurary',
            'ideatary' => 'ideatary',
            'syllabary' => 'syllabary',
            default => 'none',
        };
    }

    private function readVerticalOrientation(string $value): string
    {
        return match ($value) {
            'downwards' => 'downwards',
            'upwards' => 'upwards',
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
