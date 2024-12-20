<div class="modal-dialog" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action('ReceiveBalanceBankController@store'), 'method' => 'post', 'id' => 'receive_balance_bank_add_form' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Receive Balance From Bank</h4>
    </div>
    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('bank_name', __( 'expense.bank_name' ) . ':*') !!}
           <select name="bank_name" class="chosen-select-member form-control"  data-placeholder="Choose bank...">
                @foreach($banks as $bank) 
                  <option value="{{$bank->bank_name}}" > {{$bank->bank_name}}</option>
                @endforeach  
            </select>
      </div>
      <div class="form-group">
        {!! Form::label('branch', __( 'expense.branch' ) . ':') !!}
        {!! Form::text('branch', null, ['class' => 'form-control', 'placeholder' => __( 'expense.branch' )]); !!}
      </div>
      <div class="form-group">
        {!! Form::label('account_no', __( 'expense.account_no' ) . ':') !!}
        {!! Form::text('account_no', null, ['class' => 'form-control', 'placeholder' => __( 'expense.account_no' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('amount', __( 'expense.amount' ) . ':') !!}
        {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __( 'expense.amount' )]); !!}
      </div>
      <div class="form-group">
        <input type="hidden" name="receiver" id="receiver" value={{auth()->user()->id}}>
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
    {!! Form::close() !!}
  </div>
</div>