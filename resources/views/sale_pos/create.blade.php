@extends('layouts.app')

@section('title', 'POS')

@section('content')

<!-- Content Header (Page header) -->
<!-- <section class="content-header">
    <h1>Add Purchase</h1> -->
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
<!-- </section> -->
<input type="hidden" id="__precision" value="{{config('constants.currency_precision')}}">

<!-- Main content -->
<section class="content no-print">
	<div class="row">
		<div class="@if(!empty($pos_settings['hide_product_suggestion']) && !empty($pos_settings['hide_recent_trans'])) col-md-10 col-md-offset-1 @else col-md-9 @endif col-sm-12">
			<div class="box box-success">

				<div class="box-header with-border">
					<div class="col-sm-4">
						<h3 class="box-title">POS Terminal <i class="fa fa-keyboard-o hover-q text-muted" aria-hidden="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')" data-html="true" data-trigger="hover" data-original-title="" title=""></i></h3>
					</div>
					<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
					@if(is_null($default_location))
						<div class="col-sm-4">
							<div class="form-group" style="margin-bottom: 0px;">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-map-marker"></i>
									</span>
								{!! Form::select('select_location_id', $business_locations, null, ['class' => 'form-control input-sm mousetrap', 
								'placeholder' => __('lang_v1.select_location'),
								'id' => 'select_location_id', 
								'required', 'autofocus']); !!}
								<span class="input-group-addon">
										@show_tooltip(__('tooltip.sale_location'))
									</span> 
								</div>
							</div>
						</div>
					@endif

{{-- 					
						<div class="col-sm-4">
							<div class="form-group" style="margin-bottom: 0px;">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-map-marker"></i>
									</span>
								{!! Form::select('select_location_id',  null, ['class' => 'form-control input-sm mousetrap', 
								'placeholder' => __('lang_v1.select_location'),
								'id' => 'select_location_id', 
								'required', 'autofocus']); !!}
								<span class="input-group-addon">
										@show_tooltip(__('tooltip.sale_location'))
									</span> 
								</div>
							</div>
						</div> --}}


					<div class="col-sm-4">
						<div class="form-group" style="margin-bottom: 0px;">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-map-marker"></i>
								</span>
								<select id="unit_select_id" name="unit_select" class="form-control">
									{{-- <option value="pk">Packet</option> --}}
									<option value="pc">Pieces</option>
									<option value="cr">Carton</option>
									{{-- <option value="bx">Box</option> --}}
								</select>
								<span class="input-group-addon">
									@show_tooltip('Selling unit')
								</span> 
							</div>
						</div>
					</div>
				</div>

				{!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' => 'add_pos_sell_form' ]) !!}
				{!! Form::hidden('location_id', $default_location, ['id' => 'location_id', 'data-receipt_printer_type' => isset($bl_attributes[$default_location]['data-receipt_printer_type']) ? $bl_attributes[$default_location]['data-receipt_printer_type'] : 'browser']); !!}

				<!-- /.box-header -->
				<div class="box-body">
					<div class="row">
						{{-- @if(config('constants.enable_sell_in_diff_currency') == true)
								<div class="col-md-4 col-sm-6">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-exchange"></i>
											</span>
											{!! Form::text('exchange_rate', config('constants.currency_exchange_rate'), ['class' => 'form-control input-sm input_number', 'placeholder' => __('lang_v1.currency_exchange_rate'), 'id' => 'exchange_rate']); !!}
										</div>
									</div>
								</div>
								@endif
								@if(!empty($price_groups))
								@if(count($price_groups) > 1)
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-money"></i>
										</span>
										@php
											reset($price_groups);
										@endphp
										{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
										{!! Form::select('price_group', $price_groups, null, ['class' => 'form-control select2', 'id' => 'price_group', 'style' => 'width: 100%;']); !!}
										<span class="input-group-addon">
											@show_tooltip(__('lang_v1.price_group_help_text'))
										</span> 
									</div>
								</div>
							</div>
							@else
								@php
									reset($price_groups);
								@endphp
								{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
							@endif
						@endif --}}
							
							<div class="col-md-4 col-sm-6">
								<div class="form-group">
									{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</span>
										{!! Form::text('transaction_date', 'Select Date',  ['class' => 'form-control', 'readonly', 'required']); !!}
									</div>
								</div>
							</div>
								
							<div class="col-md-4 col-sm-6">
								<div class="form-group" style="margin-bottom: 0px;">
									<label for="">Sr Name </label>
									<div class="input-group">
										<span class="input-group-addon">
											<i class="fa fa-briefcase"></i>
										</span>
										
										<select id="sr_id" name="sr_id" class="form-control">
											<option value="">Select SR</option>
											@foreach ($sells_representatives as $sells_representative )
											<option  value="{{ $sells_representative->id }}">{{ $sells_representative->name }}</option>
											@endforeach
										</select>
										<span class="input-group-addon">
											@show_tooltip('Sr Select')
										</span> 
									</div>
								</div>
							</div>
						<div class="col-md-4 col-sm-6">
							<div class="form-group" style="margin-bottom: 0px;">
								<label for="">Delivery Man name</label>
								<div class="input-group">
									
									<span class="input-group-addon">
										<i class="fa fa-briefcase"></i>
									</span>
									<select id="delivery_man_id" name="delivery_man" class="form-control">
										<option value="">Select Delivery Man</option>
										@foreach ($delivery_men as $delivery_man )
										<option  value="{{ $delivery_man->id }}">{{ $delivery_man->name }}</option>
										@endforeach
									</select>
									<span class="input-group-addon">
										@show_tooltip('Delovery man')
									</span> 
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group"style="margin-bottom: 0px;">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-barcode"></i>
									</span>
									<input id="unit_select_id" name="invoice_no" class="form-control" placeholder="Inovice Number" />
									<span class="input-group-addon">
										@show_tooltip('Invoice Number')
									</span> 
								</div>
							</div>
						</div>
						<div class="@if(!empty($commission_agent)) col-sm-4 @else col-md-3 @endif">
							<div class="form-group" style="width: 100% !important">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-user"></i>
									</span>
									<input type="hidden" id="default_customer_id" 
									value="{{ $walk_in_customer['id']}}" >
									<input type="hidden" id="default_customer_name" 
									value="{{ $walk_in_customer['name']}}" >
									{!! Form::select('contact_id', 
										[], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required', 'style' => 'max-width: 100%;']); !!}
									<span class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""  @if(!auth()->user()->can('customer.create')) disabled @endif><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
									</span>
								</div>
							</div>
						</div>
						<input type="hidden" name="pay_term_number" id="pay_term_number" value="{{$walk_in_customer['pay_term_number']}}">
						<input type="hidden" name="pay_term_type" id="pay_term_type" value="{{$walk_in_customer['pay_term_type']}}">
						
						@if(!empty($commission_agent))
							<div class="col-sm-4">
								<div class="form-group">
								{!! Form::select('commission_agent', 
											$commission_agent, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.commission_agent')]); !!}
								</div>
							</div>
						@endif

						<div class="@if(!empty($commission_agent)) col-sm-4 @else col-sm-6 @endif">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-barcode"></i>
									</span>
									{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
									'disabled' => is_null($default_location)? true : false,
									'autofocus' => is_null($default_location)? false : true,
									]); !!}
									<span class="input-group-btn">
										<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action('ProductController@quickAdd')}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
									</span>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>

						<!-- Call restaurant module if defined -->
				        @if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
				        	<span id="restaurant_module_span">
				          		<div class="col-md-3"></div>
				        	</span>
				        @endif
			        </div>

					<div class="row col-sm-12 pos_product_div">

						<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

						<!-- Keeps count of product rows -->
						<input type="hidden" id="product_row_count" 
							value="0">
						@php
							$hide_tax = '';
							if( session()->get('business.enable_inline_tax') == 0){
								$hide_tax = 'hide';
							}
						@endphp
						<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
							<thead>
								<tr>
								<th class="tex-center col-md-4">	
										@lang('sale.product') @show_tooltip(__('lang_v1.tooltip_sell_product_column'))
									</th>
									<th class="text-center col-md-2">
										@lang('sale.qty')
									</th>
									<th class="text-center col-md-2 {{$hide_tax}}">
										@lang('sale.price_inc_tax')
									</th>
									<th class="text-center col-md-2 ">
										Sell price
									</th>
									<th class="text-center col-2">
										Unit Price
									</th>
									<th class="text-center col-md-1">
										@lang('sale.subtotal')
									</th>
									<th class="text-center">Bouns QTY</th>
									<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					@include('sale_pos.partials.pos_details')

					@include('sale_pos.partials.payment_modal')

					@if(empty($pos_settings['disable_suspend']))
						@include('sale_pos.partials.suspend_note_modal')
					@endif
				</div>
				<!-- /.box-body -->
				{!! Form::close() !!}
			</div>
			<!-- /.box -->
		</div>

		<div class="col-md-3 col-sm-12">
			@include('sale_pos.partials.right_div')
		</div>
		
	</div>
</section>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	@include('sale_pos.partials.keyboard_shortcuts')

	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
@endsection
