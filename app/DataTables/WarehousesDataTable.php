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
            ->editColumn('created_at', function (Warehouse $warehouse) {
                return $warehouse->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function (Warehouse $warehouse) {
                return $warehouse->updated_at->format('Y-m-d H:i:s');
            })
            // ->addColumn('action', function (Warehouse $warehouse) {
            //     return view('warehouse.actions', compact('warehouse'));
            // })
            // ->addColumn(['action'])
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
            ->select([ // multiple select row W/o shift/ctrl
                'style' => 'multi',
            ])
            ->buttons([
                Button::make('selectAll'),
                Button::make('selectNone'),
                Button::raw('')
                    ->text('Edit')
                    ->attr(['id' => 'edit-selected-btn']),
                Button::raw('')
                    ->text('Delete')
                    ->attr(['id' => 'delete-selected-btn']),
                Button::raw('')
                    ->text('+ New Warehouse')
                    ->addClass('open-create-modal'),
            ]);
            // ->addScript('datatables::functions.batch_remove');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::make('id')
                ->addClass('text-center'),
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
