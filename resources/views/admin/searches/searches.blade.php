@extends('layouts.app')

@section('title') Searches @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">SEARCH</h5>
        <div class="card">
            <div class="card-body">
                <a href="#search_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD SEARCH</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "action"],
                'data_url' => 'admin/searches/index/list',
                'base_tbl' => 'searches'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'search_modal',
                    'modal_title' => 'SEARCH FORM',
                    'modal_content' => autoForm(\App\Models\Core\Search::class, "admin/searches/index")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

