jQuery(document).ready(function ($) {
  

  $("body").on("submit", "form[name=tubecatcher-ajax-form]", function (e) {
    
    e.preventDefault();
    
    // get the serialize the data
    const data = $(this).serializeArray();
    

    // sending ajax request
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
