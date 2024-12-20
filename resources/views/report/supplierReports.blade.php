@extends('layouts.app')
@section('title', __('report.reports'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.supplier')}} {{ __('report.reports')}}</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="report_supplier_tbl">
        		<thead>
        			<tr>
        				<th>Supplier</th>
        				<th>@lang('report.total_purchase')</th>
                        <th>@lang('lang_v1.total_purchase_return')</th>
        				<th>@lang('report.total_sell')</th>
                        <th>@lang('lang_v1.total_sell_return')</th>
                        <th>@lang('lang_v1.opening_balance_due')</th>
                        <th>@lang('report.total_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.due_tooltip')}}" aria-hidden="true"></i></th>
        			</tr>
        		</thead>
        	</table>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection