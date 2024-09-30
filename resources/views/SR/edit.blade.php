<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('SRController@update'), 'method' => 'post', 'id' => 'sr_update_form' ]) !!}
    {!! Form::hidden('id', $sr->id, ['class' => '', ]); !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Sr Add</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'sr.name' ) . ':*') !!}
        {!! Form::text('name', $sr->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'sr.name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('contact', __( 'sr.contact' ) . ':*') !!}
        {!! Form::text('contact', $sr->contact, ['class' => 'form-control input_number', 'required']); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

{{-- @extends('layouts.app')
@section('title', 'Sr')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>SR Edit </h1>
</section>
<section class="content">
      <div class="box">
        <div class="box-body">
            {!! Form::open(['url' => action('SRController@update'), 'method' => 'post', 'id' => 'sr_update_form' ]) !!}
            {!! Form::hidden('id', $sr->id, ['class' => '', ]); !!}
            <div class="form-group d-flex">
                <div class="col-md-6">
                    {!! Form::label('name', __( 'sr.name' ) . ':*') !!}
                      {!! Form::text('name', $sr->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'sr.name' )]); !!}
                  </div>
                  <div class="col-md-6">
                    {!! Form::label('contact', __( 'sr.contact' ) . ':*') !!}
                      {!! Form::text('contact', $sr->contact, ['class' => 'form-control input_number', 'required']); !!}
                  </div>
            </div>
            <div class="" style="margin-left: 1%">
                <button style="margin-top: 2%"  class="btn btn-success" type="submit">Save</button>
            </div>       
        </div>
      </div>
</section>

@endsection --}}