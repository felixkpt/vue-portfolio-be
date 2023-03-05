@extends('layouts.app')

@section('title') Cravings @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">CRAVING</h5>
        <div class="card">
            <div class="card-body">
                <a href="#craving_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD CRAVING</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "action"],
                'data_url' => 'admin/cravings/craving/list',
                'base_tbl' => 'cravings'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'craving_modal',
                    'modal_title' => 'CRAVING FORM',
                    'modal_content' => autoForm(\App\Models\Core\Craving::class, "admin/cravings/craving")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

