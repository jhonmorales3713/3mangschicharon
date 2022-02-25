$(function(){
  $("#jcrefNum").keyup(() => {
    $("#pprefNum").val("");
  })

  $(document).ready(() => {
    $(".bottom-nav").css("display", "none");
    $(".cartHolder").css("display", "none");
  })


  $("#checkOrder").click(() => {
    var ref = $("#refno").val()
    $.ajax({
      url: base_url + "api/checkRef",
      method: "POST",
      data: {
        "ref": ref,
      },
      beforeSend: () => {
          showCover("Checking order details...");
      },
      success: (res) => {
        var response = JSON.parse(res);
        if(response.status == 1){
          window.location = base_url + "check_order_details?refno=" + ref;
        }else if(response.status == 4){
          window.open('http://35.173.0.77/dev/jc_fulfillment/main_shop_order_details/track_order/'+ref);
        }else{
          showToast({
            type: "warning",
            css: "toast-top-full-width mt-5",
            msg: "Order Reference Number does not exist."
          })
        }
        hideCover();
      },
      error: (err) => {
        showToast({
          type: "warning",
          css: "toast-top-full-width mt-5",
          msg: "Order Reference Number does not exist."
        })
      }
    })
  })
})
