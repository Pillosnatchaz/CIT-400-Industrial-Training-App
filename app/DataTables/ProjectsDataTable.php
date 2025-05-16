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
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Project> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return dataTables()
            ->eloquent($query)
            ->addColumn('creator_name', function (Project $project) {
                return $project->creator->first_name . ' ' . $project->creator->last_name; 
            })
            ->addColumn('created_at', function (Project $project) {
                return Carbon::parse($project->created_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('updated_at', function (Project $project) {
                return Carbon::parse($project->updated_at)->format('Y-m-d H:i:s');
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Project>
     */
    public function query(Project $model): QueryBuilder
    {
        return $model->newQuery()->with('creator');
    }

    /**
     * Optional method if you want to use the html builder.
     */
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
                Button::make('create')->editor('create'),
                Button::make('edit')->editor('editor'),
                Button::make('remove')->editor('editor'),
                Button::make('collection')
                    ->text('Export')
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                    ]),
            ])
            ->addScript('datatables::functions.batch_remove')
            ->editors([
                Editor::make('create')
                    ->fields([
                        Fields\Text::make('name'),
                        Fields\Text::make('client_name'),
                        Fields\Text::make('start_range'),
                        Fields\Date::make('end_range'),
                        Fields\Text::make('location'),
                        Fields\TextArea::make('description'),
                        // Fields\Hidden::make('created_by'),
                    ]),
                Editor::make('editor') 
                    ->fields([
                        Fields\Text::make('name'),
                        Fields\Text::make('client_name'),
                        Fields\Text::make('start_range'),
                        Fields\Date::make('end_range'),
                        Fields\Text::make('location'),
                        Fields\TextArea::make('description'),
                        // Fields\Hidden::make('created_by'),
                    ]),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
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

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Projects_' . date('YmdHis');
    }
}
