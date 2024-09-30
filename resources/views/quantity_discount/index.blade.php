@extends('layouts.app')
@section('title', __('role.add_role'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Quantity Discount</h1>
</section>

<section class="content">

	<div class="box">
    <div class="box-header">
      <h3 class="box-title">Quantity Discount</h3>
        
      <div class="box-tools">
        <button type="button" class="btn btn-block btn-primary btn-modal" 
           data-href="{{action('ProductQuantityDiscountController@create')}}" 
           data-container=".product_quantity_modal">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
        </div>
        
    </div>
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="quantity_discount_table">
        		<thead>
        			<tr>
                  <th>Product Name</th>
                  <th>Sell Quantity</th>
                  <th>Discount Quantity</th>
                  <th>Actions</th>
        			</tr>
        		</thead>
        	</table>
            </div>
        </div>
    </div>
    <div class="modal fade product_quantity_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
</section>


@endsection


@section('javascript')
<script>
  // $(document).ready( function () {
	// //Add products
  //   if($( "#product_sugession" ).length > 0){
  //       //Add Product
	// 	$( "#product_sugession" ).autocomplete({
	// 		source: function(request, response) {
	//     		$.getJSON("/products/list", { location_id: $('#location_id').val(), term: request.term }, response);
	//   			},
	// 		minLength: 2,
	// 		response: function(event,ui) {
	// 			if (ui.content.length == 1)
	// 			{
	// 				ui.item = ui.content[0];
	// 				if(ui.item.qty_available > 0 && ui.item.enable_stock == 1){
	// 					$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
	// 					$(this).autocomplete('close');
	// 				}
	// 			} else if (ui.content.length == 0)
	// 	            {
	// 	                swal(LANG.no_products_found)
	// 	            }
	// 		},
	// 		focus: function( event, ui ) {
	// 			if(ui.item.qty_available <= 0){
	// 				return false;
	// 			}
	// 		},
	// 		select: function( event, ui ) {
	// 			if(ui.item.qty_available > 0){
	// 				$(this).val(null);
	//     			stock_adjustment_product_row(ui.item.variation_id);
	// 			} else{
	// 				alert(LANG.out_of_stock);
	// 			}
	// 		}
	// 	})
	// 	.autocomplete( "instance" )._renderItem = function( ul, item ) {
	// 		if(item.qty_available <= 0){
				
	// 			var string = '<li class="ui-state-disabled">'+ item.name;
	// 			if(item.type == 'variable'){
	//         		string += '-' + item.variation;
	//         	}
	//         	string += ' (' + item.sub_sku + ') (Out of stock) </li>';
	//             return $(string).appendTo(ul);
	//         } else if(item.enable_stock != 1){
	//         	return ul;
	//         } 
	//         else {
	//         	var string =  "<div>" + item.name;
	//         	if(item.type == 'variable'){
	//         		string += '-' + item.variation;
	//         	}
	//         	string += ' (' + item.sub_sku + ') </div>';
	//     		return $( "<li>" )
	//         		.append(string)
	//         		.appendTo( ul );
	//         }
	//     }
  //   };
  // });

  var quantity_discount = $("#quantity_discount_table").DataTable({
    processing: true,
    serverSide: true,
    ajax: "/quantity-discount",
    columnDefs: [
      {
        targets: 3,
        orderable: false,
        searchable: false,
      },
    ],
  });


$(document).on("submit", "form#quantity_discount_add", function (e) {
  e.preventDefault();
  $(this).find('button[type="submit"]').attr("disabled", true);
  var data = $(this).serialize();
  $.ajax({
    method: "POST",
    url: $(this).attr("action"),
    dataType: "json",
    data: data,
    success: function (result) {
      
      if (result.success == true) {
        $("div.product_quantity_modal").modal("hide");
        toastr.success(result.msg);
        quantity_discount.ajax.reload();
      } else {
        toastr.error(result.msg);
      }
    },
  });
});

$(document).on("click", "button.edit_quantity_discount", function () {
  $("div.product_quantity_modal").load($(this).data("href"), function () {
    $(this).modal("show");
    $("form#quantity_discount_update").submit(function (e) {
      e.preventDefault();
      $(this).find('button[type="submit"]').attr("disabled", true);
      var data = $(this).serialize();
      $.ajax({
        method: "POST",
        url: $(this).attr("action"),
        dataType: "json",
        data: data,
        success: function (result) {
          if (result.success == true) {
            $("div.product_quantity_modal").modal("hide");
            toastr.success(result.msg);
            quantity_discount.ajax.reload();
          } else {
            toastr.error(result.msg);
          }
        },
      });
    });
  });
});

$(document).on("click", "button.delete_quantity_discount", function () {
  swal({
    title: LANG.sure,
    text: 'Quantity Discount Will Deleted',
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      var href = $(this).data("href");
      var data = $(this).serialize();
      $.ajax({
        method: "GET",
        url: href,
        dataType: "json",
        data: data,
        success: function (result) {
          if (result.success == true) {
            toastr.success(result.msg);
            quantity_discount.ajax.reload();
          } else {
            toastr.error(result.msg);
          }
        },
      });
    }
  });
});

</script>
@endsection