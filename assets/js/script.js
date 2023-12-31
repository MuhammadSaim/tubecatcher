jQuery(document).ready(function ($) {
  

  $("body").on("submit", "form[name=tubecatcher-ajax-form]", function (e) {
    
    e.preventDefault();
    
    // get the serialize the data
    const data = $(this).serializeArray();
    

    data.push({
      name: 'action',
      value: 'tubecathcer_ajax_form_action'
    });

    console.log(data);

    // sending ajax request
    $.ajax({
      url: tubecatcher_ajax.admin_ajax_url,
      method: 'POST',
      data: data,
      success: (data) => {
        console.log(data);
      }
    });

  });


});
