@extends('layouts.app')
@section('title','Update Stock Adjustment')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
<br>
    <h1>Stock Adjustment Update</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content no-print">
	{!! Form::open(['url' => action('StockAdjustmentController@update',['id'=>$stock_adjustment->id]), 'method' => 'PUT', 'id' => 'stock_adjustment_form' ]) !!}
	<input type="hidden" name="_token" value="{{csrf_token()}}">
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('location_id', __('purchase.business_location').':*') !!}
						{!! Form::select('location_id', $business_locations, $stock_adjustment->location_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
						{!! Form::text('ref_no', $stock_adjustment->ref_no, ['class' => 'form-control']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('transaction_date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date',$stock_adjustment->transaction_date, ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('adjustment_type', __('stock_adjustment.adjustment_type') . ':*') !!} @show_tooltip(__('tooltip.adjustment_type'))
						{!! Form::select('adjustment_type', [ 'Excess' =>  'Store Gain', 'Short' =>  'Store Short'], $stock_adjustment->adjustment_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid">
		<div class="box-header">
        	<h3 class="box-title">{{ __('stock_adjustment.search_products') }}</h3>
       	</div>
		<div class="box-body">
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-search"></i>
							</span>
							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product_for_srock_adjustment', 'placeholder' => __('stock_adjustment.search_product')]); !!}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<input type="hidden" id="product_row_index" value="{{$stock_adjustment->stock_adjustment_lines->count()}}">
					<input type="hidden" id="total_amount" name="final_total" value="{{$stock_adjustment->final_total}}">
					<input name="transaction_id" value="{{ $stock_adjustment->id }}" type="hidden">
					<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed" 
					id="stock_adjustment_product_table">
						<thead>
							<tr>
								<th class="col-sm-4 text-center">	
									@lang('sale.product')
								</th>
								<th class="col-sm-2 text-center">
									@lang('sale.qty')
								</th>
								<th class="col-sm-2 text-center">
									@lang('sale.unit_price')
								</th>
								<th class="col-sm-1 text-center">
									@lang('sale.subtotal')
								</th>

								<th class="col-sm-1 text-center">
									<i class="fa fa-trash" aria-hidden="true"></i>
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($stock_adjustment->stock_adjustment_lines as $key => $line)
							@php
								$stock=get_stock($line->product_id,$line->variation_id);
							@endphp
								<tr>
									
									<input name="products[{{$key}}][product_id]" type="hidden" value="{{ $line->product->id }}">
									<input name="products[{{$key}}][variation_id]" type="hidden" value="{{ $line->variation_id }}">
									<td>{{$line->product->name}} {{$line->product->sku}}</td>
									<td>
											<input type="text" class="form-control product_quantity input_number" value="{{@num_format($line->quantity)}}" name="products[{{$key}}][quantity]" max="{{$stock}}" required>
									</td>
									<td>
										<input type="text" name="products[{{$key}}][unit_price]" class="form-control product_unit_price input_number" value="{{@num_format($line->unit_price)}}">
									</td>
									<td>
										<input type="text" readonly name="products[{{$key}}][price]" class="form-control product_line_total" value="{{@num_format($line->unit_price * $line->quantity)}}">
									</td>
									<td class="text-center">
								        <i class="fa fa-trash remove_product_row cursor-pointer" aria-hidden="true"></i>
								    </td>
									<td>
										@if(!empty($line->id))
											<input type="text" name="products[{{$key}}][adjustment_lines_id]" value="{{$line->id}}">
										@endif

									</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr class="text-center"><td colspan="3"></td><td><div class="pull-right"><b>@lang('stock_adjustment.total_amount'):</b> <span id="total_adjustment">{{$stock_adjustment->final_total}}</span></div></td></tr>
						</tfoot>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div> <!--box end-->
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('total_amount_recovered', __('stock_adjustment.total_amount_recovered') . ':') !!} @show_tooltip(__('tooltip.total_amount_recovered'))
							{!! Form::text('total_amount_recovered', $stock_adjustment->total_amount_recovered, ['class' => 'form-control input_number', 'placeholder' => __('stock_adjustment.total_amount_recovered')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
							{!! Form::label('additional_notes', __('stock_adjustment.reason_for_stock_adjustment') . ':') !!}
							{!! Form::textarea('additional_notes', $stock_adjustment->additional_notes, ['class' => 'form-control', 'placeholder' => __('stock_adjustment.reason_for_stock_adjustment'), 'rows' => 3]); !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div>

		</div>
	</div> <!--box end-->
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
	<script src="{{ asset('js/stock_adjustment.js?v=' . $asset_v) }}"></script>
@endsection
