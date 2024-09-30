   

  var sr_table = $("#sr_table").DataTable({
    processing: true,
    serverSide: true,
    ajax: "/sr",
    columnDefs: [{
      "targets":2,
      "orderable": false,
      "searchable": false,
    }],
    columns: [
      { data: "name", name: "name"  },
      { data: "contact", name: "contact"}, 
      { data: "action", name: "action"}
  ]
  }); 

  


    $(document).on('submit', 'form#sr_create_form', function(e){
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
					$('div.sr-add').modal('hide');
					toastr.success(result.msg);
					sr_table.ajax.reload();
				} else {
					toastr.error(result.msg);
				}
			}
		});
	});
 
 
  $(document).on('click', 'button.edit_sr', function(){

    $( "div.sr-add" ).load( $(this).data('href'), function(){
      $(this).modal('show');
      $('form#sr_update_form').submit(function(e){
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
              $('div.sr-add').modal('hide');
              toastr.success(result.msg);
              sr_table.ajax.reload();
            } else {
              toastr.error(result.msg);
            }
          }
        });
      });
    });
  });

  $(document).on('click', 'button.delete_sr', function(){
    	swal({
          title: LANG.sure,
          text: LANG.confirm_delete_sr,
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
                  sr_table.ajax.reload();
                } else {
                  toastr.error(result.msg);
                }
              }
            });
          }
        });
    });

