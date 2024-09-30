@extends('layouts.app')
@section('title', __( 'Customer Wise Due Sell Report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>Customer Wise Due @lang( 'sale.sells') Report
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
	<div class="box">
        <div class="box-header">
        	<!--<h3 class="box-title">@lang( 'lang_v1.all_sales')</h3>-->
        	<h3 class="box-title">Customer Wise Due Sale Report</h3>
            @can('sell.create')
            	<div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('SellController@create')}}">
    				<i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('direct_sell.access')
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('date', __('Filter By Date').':') !!}
                                <div class="input-group">
                                  <button type="button" class="btn btn-primary" id="sell_date_filter">
                                    <span>
                                      <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                                    </span>
                                    <i class="fa fa-caret-down"></i>
                                  </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <div class="form-group">
                                {!! Form::label('customer_id', __('CUSTOMER').':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
            	<table class="table table-bordered table-striped ajax_view" id="due_sell_table">
            		<thead>
            			<tr>
            				<th>Invoice Date</th>
                            <th>@lang('sale.invoice_no')</th>
                            <th>Invoice Value</th>
                            <th>Collection Amount</th>
                            <th>Current Dues Amount</th>
                            <th>@lang('sale.location')</th>
            			</tr>
            		</thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                            <td id="footer_payment_status_count"></td>
                            <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                            
                        </tr>
                    </tfoot>
            	</table>
                </div>
            @endcan
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop

@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    //Date range as a button
    $('#sell_date_filter').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    $('#sell_date_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_date_filter').html('<i class="fa fa-calendar"></i> {{ __("messages.filter_by_date") }}');
        sell_table.ajax.reload();
    });
    
    $('#customer_id').on('change', function() {
        sell_table.ajax.reload();
    });

    sell_table = $('#due_sell_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        "ajax": {
            "url": "/reports/customer-wise-due-sell",
            "data": function ( d ) {
                var start = $('#sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                d.start_date = start;
                d.end_date = end;
                d.is_direct_sale = 1;
                d.sr_id = $('#sr_id').val();
                d.customer_id = $('#customer_id').val();
            }
        },
        columnDefs: [ {
            "targets": 10,
            "orderable": false,
            "searchable": false
        } ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'invoice_no', name: 'invoice_no'},
            { data: 'final_total', name: 'final_total'},
            { data: 'total_paid', name: 'total_paid'},
            { data: 'total_remaining', name: 'total_remaining'},
  
            { data: 'business_location', name: 'bl.name'}
        ],
        columnDefs: [
                {
                    'searchable'    : false, 
                    'targets'       : [5] 
                },
            ],
        "fnDrawCallback": function (oSettings) {

            $('#footer_sale_total').text(sum_table_col($('#due_sell_table'), 'final-total'));
            
            $('#footer_total_paid').text(sum_table_col($('#due_sell_table'), 'total-paid'));

            $('#footer_total_remaining').text(sum_table_col($('#due_sell_table'), 'payment_due'));

            $('#footer_total_sell_return_due').text(sum_table_col($('#due_sell_table'), 'sell_return_due'));

            $('#footer_payment_status_count').html(__sum_status_html($('#due_sell_table'), 'payment-status-label'));

            __currency_convert_recursively($('#due_sell_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(4)').attr('class', 'clickable_td');
        }
    });
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection