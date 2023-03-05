@extends('layouts.app')

@section('title') Modests @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">MODEST</h5>
        <div class="card">
            <div class="card-body">
                <a href="#modest_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD MODEST</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "name", "number", "action"],
                'data_url' => 'admin/modest/list',
                'base_tbl' => 'modests'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'modest_modal',
                    'modal_title' => 'MODEST FORM',
                    'modal_content' => autoForm(\App\Models\Core\Modest::class, "admin/modest")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

