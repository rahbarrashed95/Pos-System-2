
<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('ProductQuantityDiscountController@store'), 'method' => 'post', 'id' => 'quantity_discount_add' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Discount Product Add</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
            <label for="product">Product name or Sku</label>
            <input id="product" required name="product" id = 'product_sugession' class="form-control" type="text" placeholder="Product name or Sku">
        </div>
  
        <div class="form-group">
            <label for="sell_quantity">Selling Product Quantity</label>
            <input id="sell_quantity" name="sell_quantity" required class="form-control" type="number" placeholder="Selling Product Quantity">
        </div>

        
        <div class="form-group">
            <label for="discount_quantity">Discount Product Quantity</label>
            <input id="discount_quantity" name="discount_quantity" required class="form-control" type="number" placeholder="Discount Product Quantity">
        </div>
        
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

