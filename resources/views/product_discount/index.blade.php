@extends('layouts.app')
@section('title', __('role.add_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Discount Products </h1>
</section>

<section class="content">

	<div class="box">
    <div class="box-header">
      <h3 class="box-title">Discount Products</h3>
        
      <<div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" 
           data-href="{{action('ProductDiscountController@create')}}" 
           data-container=".product_discount">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
        </div>
        
    </div>
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="discount_table">
        		<thead>
        			<tr>
                  <th>Product Name</th>
                  <th>Pre discount Price</th>
                  <th>Discount Amount</th>
                  <th>Discount Type</th>
                  <th>Discount Price</th>
                  <th>Actions</th>
        			</tr>
        		</thead>
        	</table>
            </div>
        </div>
    </div>
    <div class="modal fade product_discount" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
</section>


@endsection

@section('javascript')
<script>
  var discount_table = $('#discount_table').DataTable({
					processing: true,
					serverSide: true,
					ajax: '/discount',
					columnDefs: [ {
						"targets":  5,
						"orderable": false,
						"searchable": false
					} ]
			    });


    $(document).on('submit', 'form#discount_add', function(e){
		e.preventDefault();
		$(this).find('button[type="submit"]').attr('disabled', true);
		var data = $(this).serialize();
		$.ajax({
			method: "POST",
			url: $(this).attr("action"),
			dataType: "json",
			data: data,
			success: function(result){
				if(result.success == true){
					$('div.product_discount').modal('hide');
					toastr.success(result.msg);
					discount_table.ajax.reload();
				} else {
					toastr.error(result.msg);
				}
			}
		});
	});

$(document).on('click', 'button.edit_product_discount', function(){

$( "div.product_discount" ).load( $(this).data('href'), function(){
  $(this).modal('show');
  $('form#discount_update_form').submit(function(e){
    e.preventDefault();
    $(this).find('button[type="submit"]').attr('disabled', true);
    var data = $(this).serialize();
      $.ajax({
      method: "POST",
      url: $(this).attr("action"),
      dataType: "json",
      data: data,
      success: function(result){
        if(result.success == true){
          $('div.product_discount').modal('hide');
          toastr.success(result.msg);
          discount_table.ajax.reload();
        } else {
          toastr.error(result.msg);
        }
      }
    });
  });
});
});

$(document).on('click', 'button.delete_product_discount', function(){
  swal({
      title: LANG.sure,
      text: LANG.confirm_delete_discount,
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
              var href = $(this).data('href');
          var data = $(this).serialize();
          $.ajax({
          method: "GET",
          url: href,
          dataType: "json",
          data: data,
          success: function(result){
            if(result.success == true){
              toastr.success(result.msg);
              discount_table.ajax.reload();
            } else {
              toastr.error(result.msg);
            }
          }
        });
      }
    });
});
</script>
@endsection