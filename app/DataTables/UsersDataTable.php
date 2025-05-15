<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('users.index'))
            ->orderBy(1)
            ->selectStyleOS()
            ->buttons([
                Button::make('selectAll'),
                Button::make('selectNone'),
                Button::make('create')->editor('create'),
                Button::make('edit')->editor('editor'),
                Button::make('remove')->editor('editor'),
                Button::make('collection')
                    ->text('Export')
                    ->buttons([
                        Button::raw()->text('Excel')->action('alert("Excel button")'),
                        Button::raw()->text('CSV')->action('alert("CSV button")'),
                    ]),
            ])
            ->addScript('datatables::functions.batch_remove')
            ->editors([
                Editor::make('dummy')
                    ->fields([
                        Fields\Image::make('image'),
                        Fields\Text::make('text'),
                        Fields\Password::make('password'),
                        Fields\TextArea::make('textarea'),
                        Fields\Select::make('select')->options([
                            'Option 1',
                            'Option 2',
                            'Option 3',
                        ]),
                        Fields\Checkbox::make('checkbox')->options([
                            'Option 1',
                            'Option 2',
                            'Option 3',
                        ]),
                        Fields\Radio::make('radio')->options([
                            'Option 1',
                            'Option 2',
                            'Option 3',
                        ]),
                        Fields\Date::make('date'),
                        Fields\Time::make('time'),
                        Fields\DateTime::make('datetime')->label('Date Time'),
                        Fields\File::make('file'),
                    ]),
                Editor::make('create')
                    ->fields([
                        Fields\Text::make('first_name'),
                        Fields\Text::make('last_name'),
                        Fields\Text::make('email'),
                        Fields\Number::make('phone'),
                        Fields\Select::make('role')->options([
                            'Member' => 'member',
                            'Admin' => 'admin',
                        ]),
                        Fields\Password::make('password'),
                        Fields\Password::make('password_confirmation')->label('Confirm Password'),
                    ]),
                Editor::make('editor') // Editor specifically for the edit button
                    ->fields([
                        Fields\Text::make('first_name'), // Add the fields you want to edit
                        Fields\Text::make('last_name'),
                        Fields\Text::make('email')->multiEditable(false), // Maybe disable editing email
                        Fields\Number::make('phone'),
                        Fields\Select::make('role')->options([
                            'Member' => 'member',
                            'Admin' => 'admin',
                        ]),
                    ]),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::make('id'),
            Column::make('first_name'),
            Column::make('last_name'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('role'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    protected function filename(): string
    {
        return 'Users_'.date('YmdHis');
    }
}
