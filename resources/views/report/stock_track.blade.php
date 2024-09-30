@extends('layouts.app')
@section('title', __('report.reports'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Stock Tracking Report</h1>
</section>

<!-- Main content -->
<section class="content">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tr_location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('tr_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tr_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'tr_date_range', 'readonly']); !!}
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>


	<div class="box">
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="stock_track_table">
        		<thead>
        			<tr>
        				<th>Product Name</th>
                        <th>Product Sku</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <!-- <th>Action</th> -->
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
   <script type="text/javascript">

    // daterangepicker




$(document).ready( function () {

    if($('#tr_date_range').length == 1){
                $('#tr_date_range').daterangepicker({
                    ranges: ranges,
                    autoUpdateInput: false,
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                    locale: {
                        format: moment_date_format
                    }
                });
                $('#tr_date_range').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
                    track_report.ajax.reload();
                });

                $('#tr_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    track_report.ajax.reload();
                });
            }

   track_report=$('#stock_track_table').DataTable({
            processing: true,
            serverSide: true,
            ajax:{
                "url":"{{action('ReportController@getStockTrackReport')}}",
                "data": function ( d ) {
                        d.location_id = $('#tr_location_id').val();
                        d.start_date = $('#tr_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        d.end_date = $('#tr_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }

            },
            columns:
            [
              {
                 data:'name',
                 name: 'name',
              },

              {
                 data:'sku',
                 name: 'sku',
              },
              

              {
                 data:'qty',
                 name: 'qty',
              },
              {
                 data:'created_at',
                 name: 'created_at',
              },
              // {
              //    data:'action',
              //    name: 'action',
              //    orderable:false
              // },
            ]
        });

    $('select#tr_location_id, #tr_date_range').change( function(){
                track_report.ajax.reload();
    });
} );


   </script>
@endsection