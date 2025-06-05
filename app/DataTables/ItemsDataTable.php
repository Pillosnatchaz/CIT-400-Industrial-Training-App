<?php

namespace App\DataTables;

use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Str;


class ItemsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function (ItemStock $itemStock) {
                return $itemStock->item->name;
            })
            ->addColumn('category', function (ItemStock $itemStock) {
                return $itemStock->item->category;
            })
            ->addColumn('warehouse_display_name', function (ItemStock $itemStock) {
                return $itemStock->warehouse->name ?? 'N/A';
            })
            ->addColumn('actual_warehouse_id', function (ItemStock $itemStock) {
                 return $itemStock->warehouse_id;
            })
            ->addColumn('item_id', function (ItemStock $itemStock) {
                return $itemStock->item_id;
            })
            ->addColumn('status_raw', function (ItemStock $itemStock) {
                return $itemStock->status;
            })
            ->addColumn('notes', function ($row) {
                return   e(Str::limit($row->notes, 500)) ;
            })
            ->editColumn('status', function (ItemStock $itemStock){
                $status = $itemStock->status;
                $colors = [
                    'available' => 'bg-green-200 text-green-800',
                    'in use'      => 'bg-yellow-200 text-yellow-800',
                    'maintenance' => 'bg-blue-200 text-blue-800',
                    'damaged'     => 'bg-red-200 text-red-800',
                    'unavailable' => 'bg-gray-300 text-black-800',
                ];

                $color = $colors[$status] ?? 'bg-gray-100 text-gray-800';
                
                return '<span class="inline-block min-w-[6rem] text-center px-3 py-1 rounded-full text-xs font-semibold '.$color.'">'.ucwords($status).'</span>';

            })
            ->editColumn('updated_at', function (ItemStock $itemStock) {
                return Carbon::parse($itemStock->updated_at)->format('Y-m-d H:i:s');
            })
            ->rawColumns(['status', 'notes'])
            ->setRowId('id');
    }

    public function query(ItemStock $model): QueryBuilder
    {
        return $model->newQuery()->with(['item', 'warehouse']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('items-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('items.index'))
            ->orderBy(1)
            // ->selectStyleOS()
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
                    ->text('+ New Items')
                    ->addClass('open-create-modal'),
                Button::make('collection')
                    ->text('Export')
                    ->buttons([
                        Button::raw()->text('Excel')->action('alert("Excel button")'),
                        Button::raw()->text('CSV')->action('alert("CSV button")'),
                    ]),
            ])
            ->addScript('datatables::functions.batch_remove');
            
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::make('id')
                ->addClass('text-center'),
            Column::make('SKU')
                ->tilte('SKU'),
            Column::make('name')
                ->title('Item Name'),
            Column::make('category')
                ->title('Category')
                ->addClass('text-center'),
            Column::make('status')
                ->addClass('text-center'),
            Column::make('warehouse_display_name')
                ->title('Warehouse')
                ->data('warehouse_display_name')
                ->headerClass('text-center')
                ->addClass('text-center'),
            Column::make('warehouse_id')
                ->title('Warehouse ID')
                ->visible(false)
                ->searchable(false)
                ->addClass('text-center'),
            Column::make('actual_warehouse_id')
                ->visible(false)
                ->searchable(false)
                ->addClass('text-center'),
            Column::make('item_id')
                ->visible(false)
                ->searchable(false)
                ->addClass('text-center'),
            Column::make('notes')
                ->title('Notes')
                ->addClass('whitespace-normal break-words max-w-md text-justfiy'),   
            Column::make('updated_at'),
        ];
    }

    protected function filename(): string
    {
        return 'Items_' . date('YmdHis');
    }
}
