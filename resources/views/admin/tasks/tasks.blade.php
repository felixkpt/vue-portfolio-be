@extends('layouts.app')

@section('title') Tasks @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">TASK</h5>
        <div class="card">
            <div class="card-body">
                <a href="#task_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD TASK</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "action"],
                'data_url' => 'admin/tasks/index/list',
                'base_tbl' => 'tasks'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'task_modal',
                    'modal_title' => 'TASK FORM',
                    'modal_content' => autoForm(\App\Models\Core\Task::class, "admin/tasks/index")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

