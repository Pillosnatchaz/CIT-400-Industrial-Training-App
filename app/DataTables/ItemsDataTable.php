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

class ItemsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Item> $query Results from query() method.
     */
    // public function dataTable(QueryBuilder $query): EloquentDataTable
    // {
    //     return datatables()
    //         ->eloquent($query)
    //         ->addColumn('name', function (ItemStock $itemStock) {
    //             return $itemStock->item->name;
    //         })
    //         ->addColumn('category', function (ItemStock $itemStock) {
    //             return $itemStock->item->category;
    //         })
    //         ->addColumn('warehouse_id', function (ItemStock $itemStock) {
    //             return $itemStock->warehouse->name ?? 'N/A'; // Assuming your Warehouse model has a 'name' attribute
    //         })
    //         ->rawColumns(['action']);
    // }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('name', function (ItemStock $itemStock) {
                return $itemStock->item->name;
            })
            ->addColumn('category', function (ItemStock $itemStock) {
                return $itemStock->item->category;
            })
            ->addColumn('warehouse_id', function (ItemStock $itemStock) {
                return $itemStock->warehouse->name ?? 'N/A'; // Assuming your Warehouse model has a 'name' attribute
            })
            ->addColumn('status', function (ItemStock $itemStock) {
                return $itemStock->status;
            })
            ->addColumn('notes', function (ItemStock $itemStock) {
                return $itemStock->notes;
            })
            ->addColumn('created_at', function (ItemStock $itemStock) {
                return $itemStock->created_at;
            })
            ->addColumn('updated_at', function (ItemStock $itemStock) {
                return $itemStock->updated_at;
            })
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Item>
     */
    public function query(ItemStock $model): QueryBuilder
    {
        return $model->newQuery()->with(['item', 'warehouse']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('items-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('items.index'))
            ->orderBy(1)
            ->selectStyleOS()
            ->buttons([
                Button::make('selectAll'),
                Button::make('selectNone'),
                Button::make('create')->editor('create')->text('Input Items'),
                // Button::make('create')->editor('create_category')->text('New Category'),
                Button::make('edit')->editor('editor'),
                Button::make('remove')->editor('editor'),
                Button::make('collection')
                    ->text('Others')
                    ->buttons([
                        Button::make('create')->editor('create_item_name')->text('New Item Name'),
                        Button::make('create')->editor('create_category')->text('New Category'),
                    ]),
                Button::make('collection')
                    ->text('Export')
                    ->buttons([
                        Button::raw()->text('Excel')->action('alert("Excel button")'),
                        Button::raw()->text('CSV')->action('alert("CSV button")'),
                    ]),
            ])
            ->addScript('datatables::functions.batch_remove')
            ->editors([
                Editor::make('create', new ItemsDataTableEditor())
                    ->fields([
                        Fields\Text::make('name'),
                        Fields\Select::make('category')->label('Category')->options(
                            Item::distinct('category')->orderBy('category')->pluck('category', 'category')
                        ),
                        Fields\Select::make('warehouse_id')->options($warehouses = Warehouse::all()->pluck('id', 'name')),
                        Fields\Select::make('status')->options([
                            'Available' => 'available',
                            'In use' => 'in use',
                            'Maintenance' => 'maintenance',
                            'Damaged' => 'damaged',
                            'Unavailable' => 'unavailable',
                        ]),
                        Fields\Number::make('quantity')
                            ->label('Quantity')
                            ->default(1)
                            ->attr('min', '1'),

                        Fields\Text::make('notes'),
                    ]),
                Editor::make('editor')
                    ->fields([
                        Fields\Hidden::make('item_id'),
                        Fields\Select::make('warehouse_id')->options($warehouses = Warehouse::all()->pluck('id', 'name')),
                        Fields\Select::make('status')->options([
                            'Available' => 'available',
                            'In use' => 'in use',
                            'Maintenance' => 'maintenance',
                            'Damaged' => 'damaged',
                            'Unavailable' => 'unavailable',
                        ]),
                        Fields\Text::make('notes'),
                    ]),
                Editor::make('create_item_name')
                    ->fields([
                        Fields\Text::make('name'),
                        Fields\Select::make('category')->label('Category')->options(
                            Item::distinct('category')->orderBy('category')->pluck('category', 'category')
                        ),
                    ]),
                Editor::make('create_category')
                    ->fields([
                        Fields\Text::make('name'),
                        Fields\Text::make('email')->multiEditable(false),
                    ]),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            // Column::computed('action')
            //       ->exportable(false)
            //       ->printable(false)
            //       ->width(60)
            //       ->addClass('text-center'),
            Column::checkbox(),
            Column::make('id'), // This will be the id from the items_stocks table
            Column::make('name')->title('Item Name'), // Define the title for clarity
            // Column::make('item_id'),
            Column::make('category')->title('Category'),
            Column::make('warehouse_id')->title('Warehouse'),
            Column::make('status'),
            Column::make('notes')->title('Notes'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Items_' . date('YmdHis');
    }
}
