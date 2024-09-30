

  var delivery_man_table = $("#delivery_man_table").DataTable({
    processing: true,
    serverSide: true,
    ajax: "/delivery_man",
    columnDefs: [
      {
        targets: 2,
        orderable: false,
        searchable: false,
      },
    ],
  });


$(document).on("submit", "form#delivery_man_create_form", function (e) {
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
        $("div.delivery_man_add").modal("hide");
        toastr.success(result.msg);
        delivery_man_table.ajax.reload();
      } else {
        toastr.error(result.msg);
      }
    },
  });
});

$(document).on("click", "button.edit_delivery_man", function () {
  $("div.delivery_man_add").load($(this).data("href"), function () {
    $(this).modal("show");
    $("form#delivery_man_update_form").submit(function (e) {
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
            $("div.delivery_man_add").modal("hide");
            toastr.success(result.msg);
            delivery_man_table.ajax.reload();
          } else {
            toastr.error(result.msg);
          }
        },
      });
    });
  });
});

$(document).on("click", "button.delete_delivery_man", function () {
  swal({
    title: LANG.sure,
    text: LANG.confirm_delete_delivery,
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
            delivery_man_table.ajax.reload();
          } else {
            toastr.error(result.msg);
          }
        },
      });
    }
  });
});
