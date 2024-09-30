<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('SRController@store'), 'method' => 'post', 'id' => 'sr_create_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Sr Add</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('name', __( 'sr.name' ) . ':*') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'sr.name' )]); !!}
        </div>
  
        <div class="form-group">
          {!! Form::label('contact', __( 'sr.contact' ) . ':*') !!}
            {!! Form::text('contact', null, ['class' => 'form-control input_number', 'required' , 'placeholder' => __( 'sr.contact' )]); !!}
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->