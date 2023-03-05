@extends('layouts.app')

@section('title') Admins @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">ADMIN</h5>
        <div class="card">
            <div class="card-body">
                <a href="#admin_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD ADMIN</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "action"],
                'data_url' => 'admin/list',
                'base_tbl' => 'admins'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'admin_modal',
                    'modal_title' => 'ADMIN FORM',
                    'modal_content' => autoForm(\App\Models\Core\Admin::class, "admin")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

