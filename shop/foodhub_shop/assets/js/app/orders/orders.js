
  let draw = 1;
  let loadScrolled = true;
  let page = 1;

$(function(){
  let result;
  let base_url = $("body").data('base_url');
  let startDate = moment(new Date()).format("YYYY-MM-DD")
  let endDate= moment(new Date()).format("YYYY-MM-DD")
  let userId = $('#active_franchise_userId').val();
  let reference_num;
  let status;

  $('.date__filter').on('apply.daterangepicker', function(ev, picker) {
    ev.preventDefault();
    startDate = picker.startDate.format('YYYY-MM-DD');
    endDate = picker.endDate.format('YYYY-MM-DD');
   }).change();

   $(window).scroll(function () {
		var lastPage = $("#lastPage").val()
		// console.log({scroll: Math.round($(window).scrollTop()) , window: $(window).height(), doc: $(document).height()})
		if (Math.round($(window).scrollTop()) + $(window).height() >= ($(document).height() - 150) && loadScrolled && $(document).width() <= 992 && page < lastPage) {
			loadScrolled = false;
      page = page + 1;
      getOrders()
		}
	});

   $(document).ready(() => {
     draw = 1;
     getOrders();
    $("#page_number").html(page)
   })

  
  $('.search__button2').click(() => {
    alert('orders');
    reference_num = $('#search_ref').val()
    status = $('#search_status').val()
    getOrders();
    page = 1;
    draw = 1;
  });

  $(".next-btn").click(() => {
    paginate("next");
    draw = draw + 1;
    getOrders();

  })
  $(".prev-btn").click(() => {
    paginate("prev");
    draw = draw + 1;
    getOrders();

  })
  $(".last-btn").click(() => {
    paginate("last");
    draw = draw + 1;
    getOrders();

  })
  $(".first-btn").click(() => {
    paginate("first");
    draw = draw + 1;
    getOrders();
  })

  function getOrders(){
    console.log($("#search_selected_branch option:selected").val())
    var filter = {
      userId: $("#search_selected_branch option:selected").val(),
      startDate,
      endDate,
      reference_num,
      status,
      page,
    };
    console.log(filter)
    $.ajax({
      url: base_url + "api/Orders/getOrders",
      method: "GET",
      data: filter,
      beforeSend: () => {
        $("#order-table__container").empty()
        $("#pagination__container").empty()
				$("#order-table__container").append(renderLoading())
      },
      success: (response) => {
        // console.log("get items")
        result = JSON.parse(response)
        console.log(result)
        $("#pagination__container").empty()
        $("#order-table__container").empty()
        $("#lastPage").val(result.totalRecords);
        paginate("default")
        loadScrolled = true;
        renderOrders();
      },
      error: (error) => {
        console.log(error)
      }
    })
  }

  
  function renderOrders(){
    if(result.data.length > 0){
      $("#order-table__container").append(renderTableHeader())
      var records = result.data.slice((parseInt(page) - 1) * 10, 10 * page);
      $.each(records,(i, data) => {
        $("#order-table__container").append(renderOrderItems(data))
      })
    }else{
      $("#pagination__container").empty();
      $("#order-table__container").append(renderEmpty())
    }
  }

  function renderOrderItems(data){
    return `
    <div class="col-12 col-md-6 col-lg-12">
          <div class="portal-table__item">
              <div class="portal-table__column col-6 col-lg-1 portal-table__srno">${data.order_so_no}</div>
              <div class="portal-table__column col-6 col-lg-2 portal-table__refno mb-2 mb-lg-0">${data.reference_num}</div>
              <div class="portal-table__column col-6 col-lg-3 portal-table__branch mb-2 mb-lg-0">${data.branchname}</div> 
              <div class="portal-table__column col-12 col-lg-2 portal-table__date">${moment(data.date_ordered).format("MM-DD-YYYY")}</div> 
              <div class="portal-table__column col-6 col-lg-2 portal-table__total mb-2 mb-lg-0 text-right text-lg-left">${parseFloat(data.total_amount).toFixed(2)}</div> 
              <div class="portal-table__column col-6 col-lg-1 portal-table__status mb-2 mb-lg-0 text-right text-lg-left"><span class="d-lg-none">Status:</span> ${renderStatus(data.order_status)} </div>
              <div class="portal-table__column portal-table__button col-6 col-lg-1 mb-2 mb-lg-0">
                  <a href="${base_url}order_items/${data.order_id}" class="btn portal-primary-btn">Manage</a>
              </div>
          </div>
      </div> 
    `
  }

  function renderStatus(status){
    // console.log(status)
    switch (status) {
      case 'p':
        return  'Processed'
      case 's':
        return  'Shipped'
      case 's':
        return  'Delivered'
      case 'r':
        return  'Received'
      default:
        return 'Processed'
    }
  }

  function renderEmpty () {
    return `
    <div class="col-12">
          <div class="portal-table__column">
              <div class="col-12 text-center"><h4>No records found</h4></div>
          </div>
      </div>
    `
  }

  function renderTableHeader (){
    return `
      <div class="col-12">
          <div class="portal-table__titles">
              <div class="col-1">SO No.</div>
              <div class="col-2">Reference No.</div>
              <div class="col-3">Branch</div>
              <div class="col-2">Date of Purchase</div>
              <div class="col-2">Total Amount</div>
              <div class="col-1">Status</div>
              <div class="col-1">Action</div>
          </div>
      </div>
    `
  }
});





