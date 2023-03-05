@extends('layouts.app')

@section('title') Cats @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">CAT</h5>
        <div class="card">
            <div class="card-body">
                <a href="#cat_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD CAT</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "name", "action"],
                'data_url' => 'admin/cats/cat/list',
                'base_tbl' => 'cats'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'cat_modal',
                    'modal_title' => 'CAT FORM',
                    'modal_content' => autoForm(\App\Models\Core\Cat::class, "admin/cats/cat")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

