
<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('ProductQuantityDiscountController@update'), 'method' => 'post', 'id' => 'quantity_discount_update' ]) !!}
        <input type="hidden" name="id" value="{{$quantity_discount->id}}">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Discount Product Add</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            <label for="product">Product name or Sku</label>
            <input id="product" required name="product" class="form-control" type="text" placeholder="Product name or Sku" value="{{ old('product', $quantity_discount->name) }}" >
        </div>
  
        <div class="form-group">
            <label for="sell_quantity">Sell Quantity</label>
            <input id="sell_quantity" name="sell_quantity" required class="form-control" type="number" placeholder="Sell Quantity" value="{{ old('product', $quantity_discount->product_quantity) }}">
        </div>

        <div class="form-group">
            <label for="discount_quantity">Discount Quantity</label>
            <input id="discount_quantity" name="discount_quantity" required class="form-control" type="number" placeholder="Discount Quantity" value="{{ old('product', $quantity_discount->discount_product_quantity) }}">
        </div>
        
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

