<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('TransferBalanceBankController@update', [$tb_bank->id]), 'method' => 'PUT', 'id' => 'transfer_balance_bank_add_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Update Transfer Balance To Bank</h4>
     
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('bank_name', __( 'expense.bank_name' ) . ':*') !!}
          {!! Form::text('bank_name', $tb_bank->bank_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'expense.bank_name' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('branch', __( 'expense.branch' ) . ':*') !!}
          {!! Form::text('branch', $tb_bank->branch, ['class' => 'form-control', 'required', 'placeholder' => __( 'expense.branch' )]); !!}
      </div>
      <div class="form-group">
        {!! Form::label('account_no', __( 'expense.account_no' ) . ':*') !!}
          {!! Form::text('account_no', $tb_bank->account_no, ['class' => 'form-control', 'required', 'placeholder' => __( 'expense.account_no' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('amount', __( 'expense.amount' ) . ':*') !!}
          {!! Form::text('amount', $tb_bank->amount, ['class' => 'form-control', 'required', 'placeholder' => __( 'expense.amount' )]); !!}
      </div>

      <div class="form-group">
      <input type="hidden" name="sender" id="sender" value={{auth()->user()->id}}>
      </div>

     
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->