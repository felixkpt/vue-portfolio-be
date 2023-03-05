@extends('layouts.app')

@section('title') Demos @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">DEMO</h5>
        <div class="card">
            <div class="card-body">
                <a href="#demo_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD DEMO</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "action"],
                'data_url' => 'admin/demos/list',
                'base_tbl' => 'demos'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'demo_modal',
                    'modal_title' => 'DEMO FORM',
                    'modal_content' => autoForm(\App\Models\Core\Demo::class, "admin/demos")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

