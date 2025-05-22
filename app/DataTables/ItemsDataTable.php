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
            ->editColumn('updated_at', function (ItemStock $itemStock) {
                return Carbon::parse($itemStock->updated_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('item_id', function (ItemStock $itemStock) {
                return $itemStock->item_id;
            })
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
                Button::make('create')
                    ->text('Add')
                    ->addClass('open-create-modal'),
                Button::make('edit')
                    ->text('Edit')
                    ->attr(['id' => 'edit-selected-btn']),
                Button::make('remove')
                    ->text('Delete')
                    ->attr(['id' => 'delete-selected-btn']),
                Button::make('collection')
                    ->text('Others')
                    ->buttons([
                        Button::make('create')->text('Add Category')
                                       ->attr(['id' => 'open-create-modal']),
                        Button::raw('')->text('Edit Category')
                                       ->attr(['id' => 'edit-selected-btn']), //dont make it select rows
                        Button::raw('')->text('Delete Category')
                                       ->attr(['id' => 'delete-selected-btn']), //dont make it select rows
                    ])
            ])
            ->addScript('datatables::functions.batch_remove');
            
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::make('id')->title('Stock ID'),
            Column::make('name')->title('Item Name'),
            Column::make('category')->title('Category')->addClass('text-center'),
            Column::make('status')->addClass('text-center'),
            Column::make('warehouse_display_name')->title('Warehouse')->data('warehouse_display_name'),
            Column::make('warehouse_id')->title('Warehouse ID')->visible(false)->addClass('text-center'),
            Column::make('actual_warehouse_id')->visible(false)->searchable(false)->addClass('text-center'),
            Column::make('item_id')->visible(false)->searchable(false)->addClass('text-center'),
            Column::make('notes')->title('Description')->addClass('text-center'),
            Column::make('updated_at')->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Items_' . date('YmdHis');
    }
}
