
$(function(){
  $(document).on('click',function(){
    $('.collapse').collapse('hide');
  })


  $('.report-order_btn').click((e) => {
    $(".report-order__container").addClass(
      "report-order__container--display"
    );
  });
 
  $(".report-order__close-icon, .report-order--overlay").click(function() {
    $(".report-order__container").removeClass(
      "report-order__container--display"
    );
  });

  $('.send-report_btn').click((e) => {
    e.preventDefault();
    let ref = $('.reference_num').val();
    let reason = $('.reason').val();
    if(reason != ''){
      $.ajax({
        url: '',
        data: {
          reference_num,
          reason
        },
        success: (res) => {
          console.log(res)
        },
        error: (err) => {
          console.log(err)
        }
      })
    }
  })

  $(".next-btn").click(() => {
    paginate("next");
    draw = draw + 1;
  })
  $(".prev-btn").click(() => {
    paginate("prev");
    draw = draw + 1;
  })
  $(".last-btn").click(() => {
    paginate("last");
    draw = draw + 1;
  })
  $(".first-btn").click(() => {
    paginate("first");
    draw = draw + 1;
  })

  //COLLAPSE

  $('.panel-collapse').on('show.bs.collapse', function () {
    $(this).siblings('.panel-heading').addClass('active');
  });

  $('.panel-collapse').on('hide.bs.collapse', function () {
    $(this).siblings('.panel-heading').removeClass('active');
  });
});


