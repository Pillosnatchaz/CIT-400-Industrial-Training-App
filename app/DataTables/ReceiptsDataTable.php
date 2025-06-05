<?php

namespace App\DataTables;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;

class ReceiptsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('creator_name', function (Receipt $receipt) {
            return $receipt->project->creator->name;
        })
        ->addColumn('created_for', function (Receipt $receipt) {
            return $receipt->borrower ? $receipt->borrower->name : '-';
        })
        ->addColumn('project_name', function (Receipt $receipt) {
            return $receipt->project->name; // Access the project's name
        })
        ->addColumn('status_item', function (Receipt $receipt) {
            $firstItem = $receipt->receipt_items->first();
            return $firstItem ? $firstItem->status : '-';
        })
        ->addColumn('detail', function ($row) {
            // return '<button type="button" class="text-blue-600 hover:underline open-detail-modal" data-id="' . $row->id . '">Details</button>';
            return '<button type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded open-detail-modal" data-id="' . $row->id . '">Details</button>';
        })
        ->editColumn('expected_return_date', function (Receipt $receipt){
            return Carbon::parse($receipt->expected_return_date)->format('Y-m-d H:i:s');
        })
        ->editColumn('actual_return_date', function (Receipt $receipt) {
            return $receipt->actual_return_date 
                ? Carbon::parse($receipt->actual_return_date)->format('Y-m-d H:i:s') : '-';
        })
        ->editColumn('created_at', function (Receipt $receipt){
            return Carbon::parse($receipt->created_at)->format('Y-m-d H:i:s');
        })
        ->editColumn('status', function (Receipt $receipt){
            $status=$receipt->status;
            $colors= [
                'approved'    => 'bg-green-200 text-green-800',
                'checked_out'      => 'bg-yellow-200 text-yellow-800',
                'completed' => 'bg-blue-200 text-blue-800',
                'overdue'     => 'bg-red-200 text-red-800',
                'pending' => 'bg-gray-300 text-black-800',
                'draft'         => 'bg-gray-200 text-gray-800',
                'partially_returned' => 'bg-indigo-100 text-indigo-800',
            ];

            $color = $colors[$status] ?? 'bg-gray-100 text-gray-800';
            $displayText = ucwords(str_replace('_', ' ', $status));

            // return '<span class="inline-block min-w-[6rem] text-center px-3 py-1 rounded-full text-xs font-semibold '.$color.'">'.ucwords($status).'</span>';
            return '<span class="inline-block min-w-[6rem] text-center px-3 py-1 rounded-full text-xs font-semibold ' . $color . '">' . $displayText . '</span>';
        })
        ->setRowId('id')
        ->rawColumns(['detail', 'status']);
    }

    public function query(Receipt $model): QueryBuilder
    {
        $type = $this->type ?? 'checkout';

        return $model->newQuery()
            ->where('type', $type)
            ->with(['user', 'project', 'receipt_items', 'borrower']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('receipts-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(9)
            ->select([
                'style' => 'multi',
                'selector' => 'td:first-child',
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
                    ->text('+ New Receipt')
                    ->addClass('open-create-modal'),
                Button::make('collection')
                    ->text('Export')
                    ->buttons([
                        Button::raw()->text('Excel'),
                        Button::raw()->text('CSV'),
                    ]),
            ])
            // ->addScript('datatables::functions.batch_remove')
            ->parameters([
                'responsive' => true,
                'autoWidth' => true,
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::checkbox(),
            Column::make('receipt_number')
                ->addClass('text-center'),
            Column::make('project_name')
                ->addClass('text-center'),
            Column::make('creator_name')
                ->title('created by')
                ->addClass('text-center'),
            Column::make('created_for')
                ->title('created for')
                ->addClass('text-center'),
            Column::make('status')
                    ->addClass('text-center'),
            Column::make('expected_return_date')
                ->addClass('text-center'),
            Column::make('actual_return_date')
                ->addClass('text-center'),
            Column::make('notes')
                ->addClass('text-center'),
            Column::make('created_at')
                ->addClass('text-center'),
            Column::make('detail') // Add the new column here
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Receipts_' . date('YmdHis');
    }

    public function with(array|string $key, mixed $value = null): static
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->$k = $v;
            }
        } else {
            $this->$key = $value;
        }

        return $this;
    }
}
