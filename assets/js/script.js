jQuery(document).ready(function ($) {
  

  $("body").on("click", ".tubecatcher-card-btn", function (e) {
    e.preventDefault();
    $.ajax({
      url: tubecatcher_ajax.admin_ajax_url,
      method: 'POST',
      data: {
        action: 'tubecathcer_ajax_form_action'
      },
      success: (data) => {
        console.log(data);
      }
    });

  });


});
