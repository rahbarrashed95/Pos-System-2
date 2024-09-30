@extends('layouts.app')
@section('title', __('role.add_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>SR </h1>
</section>

<section class="content">

	<div class="box">
    <div class="box-header">
      <h3 class="box-title">Sr</h3>
        
      <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" 
           data-href="{{action('SRController@create')}}" 
           data-container=".sr-add">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
        </div>
        
    </div>
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="sr_table">
        		<thead>
                    <tr>
                        <th>Sr Name</th>
                        <th>Sr Contact</th>
                        <th>Actions</th>
        			</tr>
        		</thead>

        	</table>
            </div>
        </div>
    </div>
    <div class="modal fade sr-add" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
</section>


@endsection


@section('javascript')
<script src="{{ asset('js/sr.js?v=' . $asset_v) }}"></script>
@endsection