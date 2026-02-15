namespace App\Livewire;

use Livewire\Component;
use App\Models\Tracer;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class ResultPaginationTable extends Component implements HasTable
{
    use InteractsWithTable;

    public $recordId;

    public function table(Table $table): Table
    {
        $record = Tracer::findOrFail($this->recordId);

        return $table
            ->query($record->results()->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->copyable()
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab(),
            ])
            ->paginated([5, 10, 25, 100]);
    }

    public function render()
    {
        return view('livewire.result-pagination-table');
    }
}
