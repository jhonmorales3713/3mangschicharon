<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Refund_order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('orders/model_order_logs');
        $this->load->model('orders/model_refund_orders');
        $this->load->model('inventory/model_inventory');
        // $this->load->library('Validate_Refund_Order');
        // $this->load->model('adhoc_resize/Model_adhoc_resize');
        // $this->load->library('uuid');
        // $this->load->library('Paypanda');
        
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
        }
    }

    public function views_restriction($content_url)
    {
        //this code is for destroying session and page if they access restricted page
        $access_content_nav = $this->session->userdata('access_content_nav');
        $arr_ = explode(', ', $access_content_nav); //string comma separated to array
        $get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();
        $url_content_arr = array();
        foreach ($get_url_content_db as $cun) {
            $url_content_arr[] = $cun['cn_url'];
        }

        if (in_array($content_url, $url_content_arr) == false) {
            header("location:" . base_url('Main/logout'));
        } else {
            return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

    public function index($token = '')
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order'])) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                // 'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'task'                => 'Create',
                'formAction'          => 'createOrderRefund', 
                'can'                 => array_merge(
                    $this->loginstate->get_access()['refund_order'],
                    $this->loginstate->get_access()['refund_order_approval']
                ),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/refund_order', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function get_suggestions()
    {
        $refnum = sanitize($this->input->post('refnum'));
        $result = $this->model_order_logs->get_referenceNumsLike($refnum);
        echo json_encode($result);
    }
    
    public function getOrderDetail()
    {
        $type = sanitize($this->input->post('type'));
        $refnum = sanitize($this->input->post('refnum'));
        $summary = []; $is_in_db = false;
        if ($type == 'create') {
            // refnum is refnum`
            $is_in_db = $this->model_refund_orders->is_in_db($refnum);
            $order_summary = $this->model_order_logs->getOrderDetailByRefNum($refnum);
            if ($order_summary) {
                $details = ($is_in_db) ? $this->model_refund_orders->getRefundOrdersByRefNum($refnum):$this->model_order_logs->getOrderDetailsPerShopByOrderIdAndRefNum($refnum);
            } else {
                $result = [
                    'success' => false,
                    'message' => "<p>Selected order with Reference No. $refnum can't be refunded.</p>",
                ];
                echo json_encode($result);
                exit();
            }
        } else {
            // refnum is summary id
            $summary = $this->model_refund_orders->getRefundOrderById($refnum);
            $order_summary = json_decode($summary['summary']);
            array_forget($summary, 'summary');
            $details = $this->model_refund_orders->getRefundOrders($refnum);
        }
        $table = [];
        foreach ($details as $key => $value) {
            // checkbox is not disabled @ view
            $is_chkbx_disabled = ($type == 'view') ? 'disabled':'';
            // is item already checked
            $is_checked = ($value['is_checked'] == 1) ? 'checked':'';
            // don't show row @ view if not checked in create/edit
            if ($type == 'view') {
                if ($value['is_checked'] == 0) continue;
            }

            if ($type == 'create') {
                // auto check data in create
                $is_checked = 'checked';
                // if already in refund summary tbl
                if ($is_in_db) {
                    if ($value['is_checked'] == 1) {
                        $details[$key]['is_refunded'] = true;
                        $is_chkbx_disabled = ($value['quantity'] < $value['maxqty']) ? '':'disabled';
                        // max qty will be less than the refund item count
                        $value['maxqty'] -= $value['quantity'];
                    }
                }
            }

            $table[] = [
                ($is_chkbx_disabled) ? 
                '<div class="d-flex justify-content-center">
                    <i class="fa fa-check text-green-400 ml-2 m-md-0" aria-hidden="true"></i>
                </div>':
                '<div class="d-flex justify-content-center">
                    <input type="checkbox" class="form-check-input tbl-items-chkbx ml-2 m-md-0" onchange="addval()" data-key="'.$key.'" id="'.$value['id'].'" '.$is_checked.'>
                </div>',
                $value['shopname'],
                $value['branchname'],
                $value['itemname'],
                $value['itemprice'],
                ($is_chkbx_disabled) ? $value['quantity']:
                '<div class="form-group">
                    <input type="number" class="form-control quantity-roller" name="quantity-'.$value['id'].'" onkeyup="this.value = this.value <= this.max ? this.value:this.max" onchange="addval()" id="quantity-'.$value['id'].'" value="'.$value['quantity'].'" min="0" max="'.$value['maxqty'].'" >
                </div>',
                '<div class="text-right">'.$value['amount'].'</div>'
            ];
        }
        // print_r($order_summary);
        // print_r($details);
        // exit();
        $result = [
            $order_summary,
            'success' => true,
            'summary' => $summary,
            'details' => $details,
            'table' => $table,
        ];
        echo json_encode($result);
    }

    public function createOrderRefund()
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['refund_order']['create'] == 1) {
            $summary = $this->input->post('summary_tbl');
            $refund = json_decode($this->input->post('refund_tbl'));
            $mode = sanitize($this->input->post('ref_mode'));
            $total_amount = json_decode($summary)->total_amount;
            // set validations
            $acc_num_validation = '';
            if ($mode == 'cash') {
                $acc_num_validation = 'required';
            } elseif ($mode == 'gcash') {
                $acc_num_validation = 'required|numeric|max_length[11]';
            } else {
                $acc_num_validation = 'required|alpha_numeric';
            }
            $validate = [
                ['refnum','Reference Number',"required|alpha_numeric|is_allordersrefunded[$total_amount]|is_refundnew"],
                ['ref_amt','Refund Amount',"required|numeric|greater_than[0]|less_than_equal_to[$total_amount]"],
                ['ref_mode','Refund Mode','required'],
                ['acc_num','Account Number',$acc_num_validation],
                ['remarks','Notes','required'],
            ];
            
            //initial validation
            foreach ($validate as $value) {
                $this->form_validation->set_rules($value[0],$value[1],$value[2]);
                // $this->validate_refund_order->set_rules($value[0],$value[1],$value[2]);
            }

            if ($this->form_validation->run() == FALSE){
                $response = array(
                    'success'      => false,
                    'message'     => validation_errors(),
                    'data'     => [
                        'ref_amt' => [(form_error('ref_amt')) ? true:false, form_error('ref_amt')],
                        'ref_mode' => [(form_error('ref_mode')) ? true:false, form_error('ref_mode')],
                        'acc_num' => [(form_error('acc_num')) ? true:false, form_error('acc_num')],
                        'remarks' => [(form_error('remarks')) ? true:false, form_error('remarks')],
                    ],
                );

                echo json_encode($response);
            } else {
                $actionRefund = $this->input->post('actionRefund');
                if($actionRefund=="void"){
                    $response = array(
                                'success'      => false,
                                'message'     => "You are trying to refund the whole transaction, please use the void module instead.",
                            );
                    
                    echo json_encode($response);
                }else{
                    $shopid            = $this->session->sys_shop;
                    $has_access = ($shopid > 0) ? '2':'1';
                    $summary = [
                        'refnum' => $this->input->post('refnum'),
                        'summary' => $summary,
                        'total_amount' => $this->input->post('ref_amt'),
                        'mode' => $this->input->post('ref_mode'),
                        'acc_num' => $this->input->post('acc_num'),
                        'remarks' => $this->input->post('remarks'),
                        'created_by' => $shopid,
                        'has_access' => $has_access,
                    ];

                    $resp = $this->model_refund_orders->createRefundSummary($summary);
                    $reference_num = $this->model_refund_orders->getRefundOrder_refnum_ById($resp)['refnum'];
                    $remarks = "New Refund Order for Order #$reference_num has been created.";
                    $this->audittrail->logActivity('Refund Orders', $remarks, 'add', $this->session->userdata('username'));
                    if ($resp) {
                        $is_in_db = $this->model_refund_orders->is_in_db($reference_num);
                        foreach ($refund as $key => $value) {
                            $value = (array) $value;
                            if (!$is_in_db) array_forget($value,'id');
                            $value['branchid'] = is_null($value['branchid']) ? 0:$value['branchid'];
                            $value['refnum'] = $reference_num;
                            $value['summary_id'] = $resp;
                            $refund[$key] = $value;
                        }
                        $ref_resp = ($is_in_db) ? $this->model_refund_orders->updateRefundDetails($refund):$this->model_refund_orders->createRefundDetails($refund);
                        if ($ref_resp) {
                            $response = array(
                                'success'      => true,
                                'message'     => "Order refund details has been saved.",
                            );
            
                            echo json_encode($response);
                        }
                    }
                }
            }
        } else {
            $this->load->view('error_404');
        }
    }

    public function approval_index($token = '')
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order'])) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                // 'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'setDate'             => [
                    'fromdate' => null,
                    'todate' => null,
                ],
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/refund_order_approval', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function refund_orders_approval()
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order_approval']) && $this->loginstate->get_access()['refund_order_approval']['view'] == 1) {
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $refnum = sanitize($this->input->post('refnum'));
            $status = sanitize($this->input->post('status'));

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_refund_orders->get_refund_orders($fromdate,$todate,$refnum,$status,$_REQUEST);
            // print_r($data);
            if(count($data['data']) > 0){
                $data = array_merge(array("success" => 1), $data);
                echo json_encode($data);
            }else{
                $data = array("success" => 0, "data" => array(), "draw" => 1, "recordsFiltered" => 0, "recordsTotal" => 0);
                echo json_encode($data);
            }
        } else {
            $this->load->view('error_404');
        }
    }

    public function export_refund_orders_approval()
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order_approval']) && $this->loginstate->get_access()['refund_order_approval']['view'] == 1) {
            $requestData = url_decode(json_decode($this->input->post('_search')));
            $filters = (array) json_decode($this->input->post('_filter'));
            $fromdate = sanitize($filters['fromdate']);
            $todate = sanitize($filters['todate']);
            $refnum = sanitize($filters['refnum']);
            $status = sanitize($filters['status']);

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_refund_orders->get_refund_orders($fromdate,$todate,$refnum,$status,$requestData,true);
            $fil_arr = [
                'Reference Number' => $refnum,
                'Record Status' => array(
                    'all' => 'All Status', 1 => 'Enabled', 2 => 'Disabled', 0 => 'For Review'
                )[$status],
            ];
            extract($this->audittrail->get_ReportExportRemarks("", "", $fromdate, $fromdate, "Refund Orders Approval", $fil_arr));
            $this->audittrail->logActivity('Refund Orders Approval', $remarks, 'export', $this->session->userdata('username'));
    
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('B1', "Refund Orders Approval");
            $sheet->setCellValue('B2', "Filters: $_filters");
            $sheet->setCellValue('B3', "Date: $fromdate to $todate");
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(25);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(25);
            $sheet->getColumnDimension('H')->setWidth(30);
            $sheet->getColumnDimension('I')->setWidth(20);
            $sheet->getColumnDimension('J')->setWidth(20);
    
            $sheet->setCellValue("A6", 'Date Created');
            $sheet->setCellValue("B6", 'Date Reviewed');
            $sheet->setCellValue('C6', 'Reference Num');
            $sheet->setCellValue('D6', 'Amount');
            $sheet->setCellValue('E6', 'Refund Mode');
            $sheet->setCellValue('F6', 'Address/Account Number');
            $sheet->setCellValue('G6', 'Notes');
            $sheet->setCellValue('H6', 'Reviewer');
            $sheet->setCellValue('I6', 'Remarks');
            $sheet->setCellValue('J6', 'Status');
    
            $sheet->getStyle('B1')->getFont()->setBold(true);
            $sheet->getStyle('A6:J6')->getFont()->setBold(true);
            $exceldata= array();
            foreach ($data['data'] as $key => $row) {
                $resultArray = array(
                    '1' => $row[0],
                    '2' => $row[1],
                    '3' => $row[2],
                    '4' => $row[3],
                    '5' => $row[4],
                    '6' => $row[5],
                    '7' => $row[6],
                    '8' => $row[7],
                    '9' => $row[8],
                    '10' => array(
                        1 => 'Approved', 2 => 'Rejected', 0 => 'For Review'
                    )[$row[9]]
                );
                
                $exceldata[] = $resultArray;
            }
            $sheet->fromArray($exceldata, null, 'A7');
            $last_row = count($exceldata)+7;
            for ($i=7; $i < $last_row; $i++) {
                $sheet->getStyle("A$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("J$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }
            $writer = new Xlsx($spreadsheet);
            $filename = 'Refund Orders Approval ' . date('Y/m/d');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
            header('Cache-Control: max-age=0');
            ob_end_clean();
    
            return $writer->save('php://output');
            exit();
        } else {
            $this->load->view('error_404');
        }
    }

    public function refund_order($token = '', $reforder_id = '')
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order'])) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            $member_id = $this->session->userdata('sys_users_id');
            $refnum = $this->model_refund_orders->getRefundOrder_refnum_ById($reforder_id)['refnum'];
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                // 'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'reforder_id'         => $reforder_id,
                'refnum'              => $refnum,
                'task'                => 'View',
                'parent_url'          => $this->uri->segment(2),
                'formAction'          => 'approveOrderRefund',
                'can'                 => array_merge(
                    $this->loginstate->get_access()['refund_order'],
                    $this->loginstate->get_access()['refund_order_approval']
                ),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/refund_order', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function approveOrderRefund()
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['refund_order_approval']['approve'] == 1 || $this->loginstate->get_access()['refund_order_approval']['reject'] == 1) {
            $validate = [
                ['refnum','Summary Id','required|numeric'],
                ['review_remarks','Review Remarks','required'],
                ['status','Status','required|numeric|max_length[1]'],
            ];
            // print_r($this->input->post());

            //initial validation
            foreach ($validate as $value) {
                $this->form_validation->set_rules($value[0],$value[1],$value[2]);
            }

            if ($this->form_validation->run() == FALSE){
                $response = array(
                    'success'      => false,
                    'message'     => validation_errors(),
                    'data'     => [
                        'refnum' => [(form_error('refnum')) ? true:false, form_error('refnum')],
                        'review_remarks' => [(form_error('review_remarks')) ? true:false, form_error('review_remarks')],
                    ],
                );

                echo json_encode($response);
            } else {
                $id = $this->input->post('refnum');
                $review = [
                    'review_remarks' => sanitize($this->input->post('review_remarks')),
                    'reviewer'       => $this->session->userdata('sys_users_id'),
                    'name'           => implode(' ', [
                        $this->session->userdata('fname'),
                        $this->session->userdata('mname'),
                        $this->session->userdata('lname'),
                    ]),
                    'status'         => $this->input->post('status'),
                ];
                if ($this->model_refund_orders->reviewRefundSummary($review, $id)) {
                    $reference_num = $this->model_refund_orders->getRefundOrder_refnum_ById($id)['refnum'];
                    $status = array(
                        1 => 'Approved', 2 => 'Rejected'
                    )[$this->input->post('status')];
                    if (strtolower($status) == 'approved') {
                        $refund_items = $this->model_refund_orders->getRefundOrdersByRefNumAndSummaryId($reference_num, $id);
                        foreach ($refund_items as $key => $item) {
                            $data = [
                                'branchid' => $item['branchid'],
                                'product_id' => $item['product_id'],
                                'quantity' => $item['quantity'],
                                'type' => 'Refund Order',
                                'username' => $this->session->userdata('username'),
                                'enabled' => 1,
                            ];
                            if ($this->model_inventory->createInventoryLog($data, $item['sys_shop'])) continue;
                        }
                    }
                    $remarks = "Refund Order for Order #$reference_num has been $status.";
                    $this->audittrail->logActivity('Refund Orders', $remarks, ($status == 'Approved') ? 'approve':'reject', $this->session->userdata('username'));
                    $response = array(
                        'success'      => true,
                        'message'     => "Order refund status has been updated.",
                    );
    
                    echo json_encode($response);
                }
            }
        } else {
            $this->load->view('error_404');
        }
    }

    public function edit_refund_order($token = '', $reforder_id = '')
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order_approval'])) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);
            $member_id = $this->session->userdata('sys_users_id');
            $refnum = $this->model_refund_orders->getRefundOrder_refnum_ById($reforder_id)['refnum'];
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                // 'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'reforder_id'         => $reforder_id,
                'refnum'              => $refnum,
                'task'                => 'Edit',
                'parent_url'          => $this->uri->segment(2),
                'formAction'          => 'updateOrderRefund',
                'can'                 => array_merge(
                    $this->loginstate->get_access()['refund_order'],
                    $this->loginstate->get_access()['refund_order_approval']
                ),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/refund_order', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function updateOrderRefund()
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['refund_order_approval']['update'] == 1) {
            $refund = json_decode($this->input->post('refund_tbl'));
            $mode = sanitize($this->input->post('ref_mode'));
            // set validations
            $acc_num_validation = '';
            if ($mode == 'cash') {
                $acc_num_validation = 'required';
            } elseif ($mode == 'gcash') {
                $acc_num_validation = 'required|numeric|max_length[11]';
            } else {
                $acc_num_validation = 'required|alpha_numeric';
            }
            $validate = [
                ['refnum','Reference Number','required|alpha_numeric|is_unique[app_refund_orders_summary.refnum]'],
                ['ref_amt','Refund Amount','required|numeric|greater_than[0]'],
                ['ref_mode','Refund Mode','required'],
                ['acc_num','Account Number',$acc_num_validation],
                ['remarks','Notes','required'],
            ];
            
            //initial validation
            foreach ($validate as $value) {
                $this->form_validation->set_rules($value[0],$value[1],$value[2]);
            }

            if ($this->form_validation->run() == FALSE){
                $refnum_err_msg = "<p>Selected Reference Number already have an refund order record.</p>";
                $response = array(
                    'success'      => false,
                    'message'     => (form_error('refnum')) ? $refnum_err_msg:validation_errors(),
                    'data'     => [
                        'ref_amt' => [(form_error('ref_amt')) ? true:false, form_error('ref_amt')],
                        'ref_mode' => [(form_error('ref_mode')) ? true:false, form_error('ref_mode')],
                        'acc_num' => [(form_error('acc_num')) ? true:false, form_error('acc_num')],
                        'remarks' => [(form_error('remarks')) ? true:false, form_error('remarks')],
                    ],
                );

                echo json_encode($response);
            } else {
                // refnum is summary_id
                $id = $this->input->post('refnum');
                $summary = [
                    'total_amount' => $this->input->post('ref_amt'),
                    'mode' => $this->input->post('ref_mode'),
                    'acc_num' => $this->input->post('acc_num'),
                    'remarks' => $this->input->post('remarks'),
                ];

                // get old refund order summary value
                $summary_cur_val = $this->model_refund_orders->getRefundOrderByIdForUpdateCompare($id);
                // update summary
                $resp = $this->model_refund_orders->updateRefundSummary($summary, $id);
                $reference_num = $this->model_refund_orders->getRefundOrder_refnum_ById($id)['refnum'];
                // audit trail
                $changes = $this->audittrail->createUpdateFormat(array_diff_assoc($summary, $summary_cur_val),$summary_cur_val);
                $remarks = "Refund Order for Order #$reference_num has been updated successfully. \nChanges: \n$changes";	
                $this->audittrail->logActivity('Refund Orders', $remarks, 'update', $this->session->userdata('username'));
                
                if ($resp) {
                    foreach ($refund as $key => $value) {
                        $value = (array) $value;
                        $value['summary_id'] = $id;
                        $refund[$key] = $value;
                    }
                    $ref_resp = $this->model_refund_orders->updateRefundDetails($refund);
                   
                    // if ($ref_resp) {
                        $response = array(
                            'success'      => true,
                            'message'     => "Order refund details has been saved.",
                        );
        
                        echo json_encode($response);
                    // }
                }
            }
        } else {
            $this->load->view('error_404');
        }
    }

    public function transaction_index($token = '')
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order_trans'])) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                // 'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'setDate'             => [
                    'fromdate' => null,
                    'todate' => null,
                ],
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/refund_order_transactions', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function transactions()
    {
        $this->isLoggedIn();
        if (isset($this->loginstate->get_access()['refund_order_trans']) && $this->loginstate->get_access()['refund_order_trans']['view'] == 1) {
            $fromdate = sanitize($this->input->post('fromdate'));
            $todate = sanitize($this->input->post('todate'));
            $refnum = sanitize($this->input->post('refnum'));
            $status = sanitize($this->input->post('status'));

            $fromdate = date("Y-m-d", strtotime($fromdate));
            $todate = date("Y-m-d", strtotime($todate));

            $data = $this->model_refund_orders->get_refund_orders($fromdate,$todate,$refnum,$status,$_REQUEST);
            // print_r($data);
            if(count($data['data']) > 0){
                $data = array_merge(array("success" => 1), $data);
                echo json_encode($data);
            }else{
                $data = array("success" => 0, "data" => array(), "draw" => 1, "recordsFiltered" => 0, "recordsTotal" => 0);
                echo json_encode($data);
            }
        } else {
            $this->load->view('error_404');
        }
    }
}

?>