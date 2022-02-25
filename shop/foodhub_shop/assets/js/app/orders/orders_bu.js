let result;
const base_url = $("body").data('base_url');
let startDate = moment(new Date()).format("YYYY-MM-DD")
let endDate= moment(new Date()).format("YYYY-MM-DD")
let userId;
$(function(){
  // const base_url = $("body").data('base_url');
  // let startDate = moment(new Date()).format("YYYY-MM-DD")
  // let endDate= moment(new Date()).format("YYYY-MM-DD")
  


 $('.dateFilter').on('apply.daterangepicker', function(ev, picker) {
   ev.preventDefault();
   startDate = picker.startDate.format('YYYY-MM-DD');
   endDate = picker.endDate.format('YYYY-MM-DD');
   renderTable()
  }).change();

 
  async function renderTable(){
    if($("#branchSelected").val() == 'ALL'){
       $('#order-table-container').html(
        `<table id="order-table-all" class="table table-striped table-bordered" style="width:100%;">
          <thead>
            <tr>
              <th>DR No.</th>
              <th>Branch Name</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th width = "120">Action</th>
            </tr>
          </thead>
        </table>
        `
      );
      await orderTableAll();
    }else{
      $('#order-table-container').html(
        `<table id="order-table-branch" class="table table-striped table-bordered" style="width:100%;">
          <thead>
            <tr>
              <th>DR No.</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th width = "120">Action</th>
            </tr>
          </thead>
        </table>
        `
      );
      await orderTableBranch();
    }
  }

  $("#order-table-branch").on('click', '.viewOrderDetailsBtn', (event) => {
    alert();
        // if ($.fn.dataTable.isDataTable("#orderDetailsTable")) {
        //   let table = $("#orderDetailsTable").DataTable();
        //   table.destroy();
        // }
        orderDetailsTable(event);

        // $("#orderDetailsTable").DataTable({
        //   serverSide:true,
        //   searching: false,
        //   "bLengthChange": false,
        //   // "columnDefs": [{ "orderable": false, "targets": [ 4 ], "className": "dt-center" }],
        //   "ajax": {
        //     "url": `${base_url}api/orders/getOrderDetails`,
        //     "data": (parameters) => {
        //       parameters.orderId = event.currentTarget.dataset.id
        //     },
        //     complete: (res) => {
        //       console.log(res.responseJSON)
        //     }
        //   },
        //   columns: [
        //     {data: 'itemname'},
        //     {data: 'quantity'},
        //     {data: 'amount'},
        //     {data: 'total_amount'},
        //   ]
        // })
  })

  $("#order-table-all").on('click', '.viewOrderDetailsBtn', (event) => {
    orderDetailsTable(event);
  })
  
  $("#branchSelected").change(() => {
    // orderTable.ajax.reload(null, false);
    renderTable()
  }).change();

  $('#searchDateBtn').click(() => {
    let date = $('.dateFilter').val().split('-');
  })


  function orderDetailsTable(event) {

    console.log(event)
    $("#orderDetailsTable").DataTable({
      serverSide:true,
      searching: false,
      "bLengthChange": false,
      // "columnDefs": [{ "orderable": false, "targets": [ 4 ], "className": "dt-center" }],
      "ajax": {
        "url": `${base_url}api/orders/getOrderDetails`,
        "data": (parameters) => {
          parameters.orderId = event.currentTarget.dataset.id
        },
        complete: (res) => {
          console.log(res.responseJSON)
        }
      },
      columns: [
        {data: 'itemname'},
        {data: 'quantity'},
        {data: 'amount'},
        {data: 'total_amount'},
      ]
    })
  }

  function orderTableBranch(){
    $('#order-table-branch').DataTable({
      serverSide:true,
      searching: false,
      destroy: true,
      // "columnDefs": [{ "orderable": false, "targets": [ 4 ], "className": "dt-center" }],
      "ajax": {
        "url": `${base_url}api/Orders/getOrders`,
        "data": (parameters) => {
          console.log(parameters)
          parameters.userId = $("#branchSelected").val();
          parameters.startDate = startDate;
          parameters.endDate = endDate;
        },
        complete: function(res) {
          
        }, 
      },
      columns: [
        {data: 'drno'},
        {data: 'date_created'},
        {data: 'total_amount'},
        {
          data: 'status',
          render: (data, row, type, meta) => {
            switch(parseInt(data)){
              case 0: 
                return '<h5 class="d-flex"><span class="badge badge-pill badge-warning">Pending</span></h5>';
              case 1: 
                return '<h5 class="d-flex "><span class="badge badge-pill badge-success">Paid</span></h5>'
              case 2: 
                return '<h5 class="d-flex "><span class="badge badge-pill badge-danger">Unpaid</span></h5>';
              default: 
                return '<h5 class="d-flex"><span class="badge badge-pill badge-warning">Pending</span></h5>';
            }
          }
        },
        {
          data: 'order_id',
          render: (data, row, type, meta) => {
            return '<button class = "btn btn-primary viewOrderDetailsBtn" data-id = '+data+' data-toggle="modal" data-target="#viewOrderDetailsModal">View Order Details </button>'
          }
        }
      ]
    });
  }
  
  function orderTableAll(){
    $('#order-table-all').DataTable({
      serverSide:true,
      searching: false,
      destroy: true,
      // "columnDefs": [{ "orderable": false, "targets": [ 4 ], "className": "dt-center" }],
      "ajax": {
        "url": `${base_url}api/Orders/getOrders`,
        "data": (parameters) => {
          console.log(parameters)
          parameters.userId = $("#branchSelected").val();
          parameters.startDate = startDate;
          parameters.endDate = endDate;
        },
        complete: function(res) {
          
        }, 
      },
      columns: [
        {data: 'drno'},
        {data: 'branchname'},
        {data: 'date_created'},
        {data: 'total_amount'},
        {
          data: 'status',
          render: (data, row, type, meta) => {
            switch(parseInt(data)){
              case 0: 
                return '<h5 class="d-flex"><span class="badge badge-pill badge-warning">Pending</span></h5>';
              case 1: 
                return '<h5 class="d-flex "><span class="badge badge-pill badge-success">Paid</span></h5>'
              case 2: 
                return '<h5 class="d-flex "><span class="badge badge-pill badge-danger">Unpaid</span></h5>';
              default: 
                return '<h5 class="d-flex"><span class="badge badge-pill badge-warning">Pending</span></h5>';
            }
          }
        },
        {
          data: 'order_id',
          render: (data, row, type, meta) => {
            return '<button class = "btn btn-primary viewOrderDetailsBtn" data-id = '+data+' data-toggle="modal" data-target="#viewOrderDetailsModal">View Order Details </button>'
          }
        }
      ]
    });
  }
  

  
});


function getOrders(){
  $.ajax({
    url: base_url + "api/Orders/getOrders",
    method: "GET",
    data: {
    },
    beforeSend: () => {
      $("#portal-table__container").empty()
    },
    success: (response) => {
      // console.log("get items")
      result = JSON.parse(response)
      console.log(result)
      $("#portal-table__container").empty()
    },
    error: (error) => {
      console.log(error)
    }
  })
}

function renderTableHeader (){
  return `
    <div class="col-12">
        <div class="portal-table__titles">
            <div class="col-1">SO_no</div>
            <div class="col-1">DRNO</div>
            <div class="col-3">Branch</div>
            <div class="col-2">Date of Purchase</div>
            <div class="col-2">Total Amount</div>
            <div class="col-1">Status</div>
            <div class="col-1">Action</div>
        </div>
    </div>
  `;
}



function renderItems(item) {
  console.log(item)
  let orderTotalAmt  = 0;
  let renderItem = "";
  renderItem += `<h3><strong> ${item.description.toUpperCase()} </strong></h3><br/>`;

  renderItem += '<table class="table  p-4">';
  renderItem += '<thead>';
  renderItem += '<tr>';
  renderItem += '<th>Name</th>';
  renderItem += '<th>Quantity</th>';
  renderItem += '<th>Amount</th>';
  renderItem += '<th>Total Amount</th>';
  renderItem += '</tr>';
  renderItem += '</thead>';
  renderItem += '<tbody>';
  item.forEach(i => {
    //table items
    renderItem += '<tr>';
    renderItem += '<td>'+i.itemname+'</td>';
    renderItem += '<td>'+i.quantity+'</td>';
    renderItem += '<td>'+i.amount+'</td>';
    renderItem += '<td><strong>'+i.total_amount+'</strong></td>';
    renderItem += '</tr>';
    //end of table items
  })
  renderItem += '</tbody>';
  renderItem += '<tfoot>';
  renderItem += '<td colspan = "3" class = "text-right" >Total Amount : </td>';
  renderItem += '<td class = "text-bold">TOTAL AMOUNT</td>';
  renderItem += '<tfoot>';
  renderItem += '</table>';
  return renderItem;
}