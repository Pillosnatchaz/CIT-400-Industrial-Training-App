<?php

namespace App\DataTables;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WarehousesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Warehouse> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            // ->addColumn('action', 'warehouses.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Warehouse>
     */
    public function query(Warehouse $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('warehouses-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('warehouse.index'))
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
                Editor::make('create')
                    ->fields([
                        Fields\Text::make('name'),
                        Fields\Text::make('address'),
                    ]),
                Editor::make('editor') 
                    ->fields([
                        Fields\Text::make('name'),
                        Fields\Text::make('address'),
                    ]),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::make('id'),
            Column::make('name'),
            Column::make('address'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Warehouses_' . date('YmdHis');
    }
}
