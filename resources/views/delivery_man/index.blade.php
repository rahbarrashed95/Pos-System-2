@extends('layouts.app')
@section('title', __('role.add_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Delivery Man </h1>
</section>

<section class="content">

	<div class="box">
    <div class="box-header">
      <h3 class="box-title">Delivery Man</h3>
        
      <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" 
           data-href="{{action('DeliveryManController@create')}}" 
           data-container=".delivery_man_add">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
        </div>
        
    </div>
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="delivery_man_table">
        		<thead>
        			<tr>
                  <th> Name</th>
                  <th> Contact</th>
                  <th>Actions</th>
        			</tr>
        		</thead>
            <tbody>
              
            </tbody>
        	</table>
            </div>
        </div>
    </div>
    <div class="modal fade delivery_man_add" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
</section>


@endsection

@section('javascript')
  <script src="{{ asset('js/delivery.js') }}"></script>
@endsection