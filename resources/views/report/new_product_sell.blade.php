@extends('layouts.app')
@section('title', __('lang_v1.product_sell_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('Product Wise Sell ')}}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                  </a>
                </h3>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                  {!! Form::open(['url' => action('ReportController@getStockReport'), 'method' => 'get', 'id' => 'product_sell_report_form' ]) !!}
                    <div class="col-md-3">
                        <div class="form-group">
                        {!! Form::label('search_product', __('lang_v1.search_product') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="hidden" value="" id="variation_id">
                                {!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'), 'autofocus']); !!}
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sr_id', __('SR').':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                {!! Form::select('sr_id', $sells_representatives, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('category.category') . ':') !!}
                            {!! Form::select('category_id', $categories, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'category_id']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sub_category_id', __('product.sub_category') . ':') !!}
                            {!! Form::select('sub_category_id', array(), null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'sub_category_id']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('brand_id', __('product.brand') . ':') !!}
                            {!! Form::select('brand_id', $brands, null, ['placeholder' => __('messages.all'), 'class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">

                            {!! Form::label('product_sr_date_filter', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'product_sr_date_filter', 'readonly']); !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="new_product_sell_report_table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>@lang('sale.product')</th>
                                <th>@lang('sale.qty')</th>
                                <th>@lang('sale.subtotal')</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="2"><strong>@lang('sale.total'):</strong></td>
                                <td id="footer_total_sold"></td>
                                <td><span class="display_currency" id="footer_subtotal" data-currency_symbol ="true"></span></td>
                            </tr>
                        </tfoot>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade view_register" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

@endsection

@section('javascript')
    
    <script>
        
        $(document).ready(function(){
            
            //Product Sell Report
        if($('#product_sr_date_filter').length == 1){
            $('#product_sr_date_filter').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#product_sr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    product_sell_report.ajax.reload();
                }
            );
            $('#product_sr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
                product_sell_report.ajax.reload();
            });
            $('#product_sr_date_filter').data('daterangepicker').setStartDate(moment());
            $('#product_sr_date_filter').data('daterangepicker').setEndDate(moment());
        }
        product_sell_report = $('table#new_product_sell_report_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[3, 'desc']],
            "ajax": {
                "url": "/reports/product-wise-sell",
                "data": function ( d ) {
                    var start = '';
                    var end = '';
                    if($('#product_sr_date_filter').val()){
                        start = $('input#product_sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        end = $('input#product_sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }
                    d.start_date = start;
                    d.end_date = end;
    
                    d.variation_id = $('#variation_id').val();
                    d.customer_id = $('select#customer_id').val();
                    d.location_id = $('select#location_id').val();
                    d.category_id = $('select#category_id').val();
                    d.sub_category_id = $('select#sub_category_id').val();
                    d.brand_id = $('select#brand_id').val();
                    d.sr_id = $('select#sr_id').val();
                }
            },
            columns: [
                { data: 'sku', name: 'p.sku'  },
                { data: 'product_name', name: 'p.name'  },
                { data: 'sell_qty', name: 'transaction_sell_lines.quantity'},
                { data: 'subtotal', name: 'subtotal', searchable: false}
            
            ],
            "fnDrawCallback": function (oSettings) {
                $('#footer_subtotal').text(sum_table_col($('#new_product_sell_report_table'), 'row_subtotal'));
                $('#footer_total_sold').html(__sum_stock($('#new_product_sell_report_table'), 'sell_qty'));
                __currency_convert_recursively($('#new_product_sell_report_table'));
            }
        });
    
        $('#product_sell_report_form #variation_id,#product_sell_report_form #sr_id, #product_sell_report_form #location_id, #product_sell_report_form #customer_id, #product_sell_report_form #category_id, #product_sell_report_form #sub_category_id, #product_sell_report_form #brand_id, #product_sell_report_form #product_sr_date_filter').change( function(){
            product_sell_report.ajax.reload();
        });
    
        })
    </script>
@endsection