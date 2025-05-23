<?php

namespace App\DataTables;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;


class ProjectsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('creator_name', function (Project $project) {
                return $project->creator->first_name . ' ' . $project->creator->last_name; 
            })
            ->editColumn('created_at', function (Project $project) {
                return Carbon::parse($project->created_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function (Project $project) {
                return Carbon::parse($project->updated_at)->format('Y-m-d H:i:s');
            })
            ->setRowId('id');
    }

    public function query(Project $model): QueryBuilder
    {
        return $model->newQuery()->with('creator');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('projects-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('projects.index'))
            ->orderBy(1)
            ->selectStyleOS()
            ->buttons([
                Button::make('selectAll'),
                Button::make('selectNone'),
                Button::make('edit')
                    ->text('Edit')
                    ->attr(['id' => 'edit-selected-btn']),
                Button::make('remove')
                    ->text('Delete')
                    ->attr(['id' => 'delete-selected-btn']),
                Button::make('create')
                    ->text('Add')
                    ->addClass('open-create-modal'),
            ])
            ->addScript('datatables::functions.batch_remove');
    }

    public function getColumns(): array
    {
        return [
            Column::Checkbox(),
            Column::make('id'),
            Column::make('name'),
            Column::make('client_name'),
            Column::make('start_range')->title('Start Date'),
            Column::make('end_range') ->title('End Date'),
            Column::make('location'),
            Column::make('description'),
            Column::make('creator_name')->title('Created By'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }

    protected function filename(): string
    {
        return 'Projects_' . date('YmdHis');
    }
}
