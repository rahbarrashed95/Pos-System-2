
<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('ProductDiscountController@store'), 'method' => 'post', 'id' => 'discount_add' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Discount Product Add</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            <label for="product">Product name or Sku</label>
            <input id="product" required name="product" class="form-control" type="text" placeholder="Product name or Sku">
        </div>
  
        <div class="form-group">
            <label for="amount">Amount %</label>
            <input id="amount" name="amount" required class="form-control" type="number" placeholder="Amount %">
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select class="form-control" required name="type" id="type">
                <option value="">Select One</option>
                <option value="fixed">Fixed</option>
                <option value="percentage">Percentage</option>
            </select>
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

