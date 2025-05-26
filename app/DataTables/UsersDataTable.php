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
            ->addColumn('role_raw', function (User $user) {
                return $user->role;
            })
            ->editColumn('created_at', function (User $user) {
                return $user->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function (User $user) {
                return $user->updated_at->format('Y-m-d H:i:s');
            })
            ->editColumn('role', function (User $user) {
                $role = $user->role;
                $colors = [
                    'admin' => 'bg-green-200 text-green-800',
                    'member' => 'bg-yellow-200 text-yellow-800',
                ];

                $color = $colors[$role] ?? 'bg-gray-100 text-gray-800';
                return '<span class="inline-block min-w-[6rem] text-center px-3 py-1 rounded-full text-xs font-semibold '.$color.'">'.ucwords($role).'</span>';
            })
            ->rawColumns(['role'])
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
                    ->text('Create New User')
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
