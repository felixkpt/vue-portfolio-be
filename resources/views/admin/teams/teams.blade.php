@extends('layouts.app')

@section('title') Teams @endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <h5 class="page-title">TEAM</h5>
        <div class="card">
            <div class="card-body">
                <a href="#team_modal" class="btn btn-info btn-sm clear-form float-right" data-toggle="modal"><i class="fa fa-plus"></i> ADD TEAM</a>
                @include('common.bootstrap_table_ajax',[
                'table_headers' => ["id", "name", "status", "slug", "action"],
                'data_url' => 'admin/teams/index/list',
                'base_tbl' => 'teams'
                ])

                @include('common.auto_modal',[
                    'modal_id' => 'team_modal',
                    'modal_title' => 'TEAM FORM',
                    'modal_content' => autoForm(\App\Models\Core\Team::class, "admin/teams/index")
                ])
            </div>
        </div>
    </div>
</div>
@endsection

