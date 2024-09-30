
<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('DeliveryManController@update'), 'method' => 'post', 'id' => 'delivery_man_update_form' ]) !!}
    {!! Form::hidden('id', $delivery_man->id, ['class' => '', ]); !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Delivery Man Add</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'delivery.name' ) . ':*') !!}
        {!! Form::text('name', $delivery_man->name, ['class' => 'form-control', 'required', 'placeholder' => __( 'delivery.name' )]); !!}
      </div>
      <div class="form-group">
        {!! Form::label('contact', __( 'delivery.contact' ) . ':*') !!}
        {!! Form::text('contact', $delivery_man->contact, ['class' => 'form-control input_number', 'required']); !!}
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

