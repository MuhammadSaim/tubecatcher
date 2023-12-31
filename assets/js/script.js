jQuery(document).ready(function ($) {
  

  const spinner = `<div class="spinner-border spinner-border-sm" role="status">
                      <span class="visually-hidden">Loading...</span>
                   </div>`;

  $("body").on("submit", "form[name=tubecatcher-ajax-form]", function (e) {
    
    e.preventDefault();
    
    // get the serialize the data
    const data = $(this).serializeArray();
    

    data.push({
      name: 'action',
      value: 'tubecathcer_ajax_form_action'
    });

    
    $("form[name=tubecatcher-ajax-form] input, form[name=tubecatcher-ajax-form] button").attr("disabled", true);

    // sending ajax request
    $.ajax({
      url: tubecatcher_ajax.admin_ajax_url,
      method: 'POST',
      data: data,
      beforeSend: () => {
        $("form[name=tubecatcher-ajax-form] button").html(spinner);
        $("form[name=tubecatcher-ajax-form] input").removeClass("is-invalid");
        $(".tubecatcher-error").html('');
      },
      success: (data) => {
        $("form[name=tubecatcher-ajax-form] input, form[name=tubecatcher-ajax-form] button").attr("disabled", false);
        $("form[name=tubecatcher-ajax-form] button").html('Get Video');
        data = JSON.parse(data);

        console.log(data);

        if(data.error){

          if(data.error_type === 'field'){
            $("form[name=tubecatcher-ajax-form] .tubecatcher_video_url_feedback").text(data.message);
            $("form[name=tubecatcher-ajax-form] input[name=tubecatcher_video_url]").addClass("is-invalid");
          }else{
            $(".tubecatcher-error").html(`
                <div class="alert alert-danger" role="alert">
                  ${data.message}
                </div>
              `);
          }

        }else{

          let links = '';


          data.data.download_links.forEach(link => {
            links += `<li><a class="dropdown-item" target="_blank" href="${link.url}">${link.quality}</a></li>`;
          });



          let download_links = `<div class="btn-group">
                                  <button type="button" class="btn btn-danger">Download</button>
                                  <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    ${links}
                                  </ul>
                                </div>`;


          $(".tubecatcher-container-info-box").html(`


            <div class="card shadow mt-5">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <img src="${data.data.thumbnail}" alt="${data.data.title} thumbnail" class="img-fluid rounded-start tubecatcher-video-image"/>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="card-body">
                            <h5 class="text-break fw-bold">${data.data.title}</h3>
                            <div class="d-flex align-items-center">
                                <i class="fab fa-youtube tubecatcher-fa-2x tubecatcher-fa-youtube me-2"></i>
                                <span class="tubecatcher-channel-name">${data.data.channel_name}</span>
                            </div>
                            <div class="mt-3">
                              ${download_links}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


          `);

        }
      }
    });

  });


});
