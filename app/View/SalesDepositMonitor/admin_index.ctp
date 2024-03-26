<?php //pr($offices);die();
?>


<style>
    .search .radio label {
        width: auto;
        float: none;
        padding-left: 5px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 30%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 33%;
        float: left;
        margin: 1px 0;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="float:left;">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Sales Deposition Monitoring'); ?></h3>
                <div class="box-tools pull-right">
                    <?php //if($this->App->menu_permission('deposits','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Deposit'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } 
                    ?>
                </div>
            </div>



            <div class="box-body">
                <div class="search-box" style="float:left; width:100%;">
                    <?php echo $this->Form->create('SalesDepositMonitor', array('role' => 'form')); ?>
                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('max_amount_retain', array('class' => 'form-control', 'required' => true)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true)); ?>
                            </td>
                            <td class="required">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true)); ?>
                            </td>
                        </tr>
                        <tr>
                            <?php if (isset($region_offices)) { ?>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- All ----', 'options' => $region_offices,)); ?></td>
                            <?php } ?>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'required' => true, 'empty' => '---- Select Office ----')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Sales and SPO Officers : </label>
                                <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="checkall2" />
                                        <label for="checkall2" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                    </div>
                                    <div class="selection2 so_list">
                                        <?php echo $this->Form->input('so_id', array('label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>



                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'id' => 'search_button', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                <?php
                                /*if (!empty($office_id)) {
									?>
										<button onclick="PrintElem('content')" class="btn btn-primary"><i class="fa fa-print"></i> Print</button>
									<?php
                             }*/
                                ?>
                                <?php if ($data) { ?>
                                    <a class="btn btn-success" id="download_xl">Download XL</a>
                                <?php } ?>
                            </td>
                        </tr>
                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>

                <script>
                    $(document).ready(function() {
                        $("input[type='checkbox']").iCheck('destroy');
                        $('#checkall2').click(function() {
                            var checked = $(this).prop('checked');
                            $('.selection2').find('input:checkbox').prop('checked', checked);
                        });
                        $('#checkall').click(function() {
                            var checked = $(this).prop('checked');
                            $('.selection').find('input:checkbox').prop('checked', checked);
                        });
                    });
                </script>

                <div class='row' style="float:left; width:100%;">
                    <div class='col-xs-6'>
                        <div id='Users_info' class='dataTables_info'>
                            <?php    //echo $this->Paginator->counter(array("format" => __("Page {:page} of {:pages}, showing {:current} records out of {:count} total"))); 
                            ?>
                        </div>
                    </div>
                    <div class='col-xs-6'>
                        <div class='dataTables_paginate paging_bootstrap'>
                            <ul class='pagination'>
                                <?php
                                //echo $this->Paginator->prev(__("prev"), array("tag" => "li"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
                                //echo $this->Paginator->numbers(array("separator" => "","currentTag" => "a", "currentClass" => "active","tag" => "li","first" => 1));
                                //echo $this->Paginator->next(__("next"), array("tag" => "li","currentClass" => "disabled"), null, array("tag" => "li","class" => "disabled","disabledTag" => "a"));
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>


                <?php if ($data) { ?>

                    <div class="pull-right csv_btn" style="padding-top:20px; display:none;">
                        <?= $this->Html->link(__('Download XLS'), array('action' => 'dwonload_xls?data=' . serialize($request_data)), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                    </div>
                    <style type="text/css">
                        .table-responsive {
                            color: #333;
                            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                            line-height: 1.42857;
                        }

                        .print-table {
                            font-size: 11px;
                        }

                        .qty_val {
                            width: 125px;
                            margin: 0;
                            float: left;
                            text-transform: capitalize;
                        }

                        .val {
                            border-right: none;
                        }

                        p {
                            margin: 2px 0px;
                        }

                        .bottom_box {
                            float: left;
                            width: 33.3%;
                            text-align: center;
                        }

                        td,
                        th {
                            padding: 5px;
                        }

                        table {
                            border-collapse: collapse;
                            border-spacing: 0;
                        }

                        .titlerow,
                        .totalColumn {
                            background: #f1f1f1;
                        }

                        .report_table {
                            margin-bottom: 18px;
                            max-width: 100%;
                            width: 100%;
                        }

                        .table-responsive {
                            min-height: 0.01%;
                            overflow-x: auto;
                        }

                        .titlerow th div {
                            text-transform: capitalize;
                            min-width: 100px;
                            float: left;
                            position: relative;
                        }

                        .titlerow th {
                            text-align: center;
                        }
                    </style>
                    <div id="content" style="width:98%; height:100%;margin-left:1%;margin-right:1%;">

                        <div style="width:100%; text-align:center; font-size:12px;">
                            <div style="font-size:20px;">SMC Enterprise Limited</div>
                            <div style="font-size:14px;"><strong>SALES/DEPOSITION MONITORING</strong></div>
                            <div style="font-size:11px;">Between:&nbsp;&nbsp;<?php if (!empty($date_from)) echo date('d-M-Y', strtotime($date_from)); ?>&nbsp;to&nbsp;<?php if (!empty($date_to)) echo date('d-M-Y', strtotime($date_to)); ?>&nbsp;&nbsp;&nbsp;&nbsp;Reporting Date:&nbsp;&nbsp;<?php echo date('d-M-Y'); ?></div>
                            <div style="font-size:11px;padding-bottom:40px;">Area : <?php if (!empty($office_id)) echo $offices[$office_id]; ?></div>
                        </div>


                        <div style="float:left; width:100%; height:450px; overflow:scroll;">
                            <?php foreach ($data as $rpt_data) { ?>
                                <table class="print-table table table-bordered table-responsive" style="width:100%" border="1px solid black" cellpadding="2px" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <td colspan="10">Sales Officer: <?= $rpt_data['so_name'] ?></td>
                                            <td colspan="2">Maximum Amount Allowed to Retain : <?= $this->request->data['SalesDepositMonitor']['max_amount_retain'] ?></td>
                                        </tr>
                                        <tr>
                                            <td>Date</td>
                                            <td>Day</td>
                                            <td>Cash Sales</td>
                                            <td>Credit Sales</td>
                                            <td>Total Sales</td>
                                            <td>Credit Collection</td>
                                            <td>Amount Collectd</td>
                                            <td>Amount Deposited/DD Purchase</td>
                                            <td>Balance</td>
                                            <td>Exceed/Within The Limit</td>
                                            <td>Retained The process No of Days</td>
                                            <td>Remarks</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td colspan="2">Opening Balance </td>
                                            <td><?= $rpt_data['opening_balance'] ?> </td>
                                            <td colspan="4"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rpt_data['rpt_data'] as $key => $value) { ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($key)) ?></td> <!-- date -->
                                                <td><?= date('l', strtotime($key)) ?></td> <!-- day -->
                                                <td><?= $value['cash_sales'] ? $value['cash_sales'] : '0.0' ?></td> <!-- cash sales -->
                                                <td><?= $value['credit_sales'] ? $value['credit_sales'] : '0.0' ?></td> <!-- credit sales -->
                                                <td><?= $value['total_sales'] ? $value['total_sales'] : '0.0' ?></td> <!-- total sales -->
                                                <td><?= $value['credit_collection'] ? $value['credit_collection'] : '0.0' ?></td> <!-- credit collection -->
                                                <td><?= $value['amount_collected'] ? $value['amount_collected'] : '0.0' ?></td> <!-- Amount collected -->
                                                <td><?= $value['deposited'] ? $value['deposited'] : '0.0' ?></td> <!-- amount Deposited -->
                                                <td><?= $value['balance'] ? $value['balance'] : '0.0' ?></td> <!-- Balance -->
                                                <td><?= $value['exceed']; ?></td> <!-- exceed -->
                                                <td><?= $value['process_retained']; ?></td> <!-- process retained -->
                                                <td><?= $value['remarks']; ?></td> <!-- remarks -->
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>

                        <div style="float:left; width:100%; padding:100px 0 50px;; font-size:13px;">
                            <div style="width:33%;text-align:left;float:left">
                                Prepared by:______________
                            </div>
                            <div style="width:33%;text-align:center;float:left">
                                Checked by:______________
                            </div>
                            <div style="width:33%;text-align:right;float:left">
                                Signed by:______________
                            </div>
                        </div>

                        <?php /*?><div style="width:100%;padding-top:0px; font-size:13px;">
                            <div style="width:33%;text-align:left;float:left">
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(I.T.O)</span>
                            </div>
                            <div style="width:33%;text-align:center;float:left">
                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(A.O)</span> 
                            </div>
                            <div style="width:33%;text-align:right;float:left">
                                <span>(S.M)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            </div>		  
                        </div><br><br><br><br><br>
                
                        <footer style="width:100%;text-align:center;">
                        "This Report has been generated from SMC Automated Sales System at [<?php if(!empty($office_id)) echo $offices[$office_id].' Area'; ?>]. This information is confidential and for internal use only."
                    </footer><?php */ ?>

                    </div>
                <?php } ?>

            </div>



        </div>
    </div>
</div>

<script>
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_deposit_monitor/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('#office_id').change(function() {
        //alert($(this).val());
        $.ajax({
            type: "POST",
            url: '<?= BASE_URL ?>sales_deposit_monitor/get_territory_so_list',
            data: {
                'office_id': $(this).val(),
                'date_from': $('#SalesDepositMonitorDateFrom').val()
            },
            cache: false,
            success: function(response) {
                //alert(response);		
                $('#checkall2').prop('checked', false);
                $('.so_list').html(response);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.date_from').change(function() {
            //getdate();
        });

        /*$('.date_to').change(function(){
        	var date_to_hidden = $('#date_to_hidden').val();
        	var get_date_to = $('#date_to').val();
        	//console.log(date_to_hidden);
        	//console.log(get_date_to);
            if(get_date_to<date_to_hidden){
            	alert('Minimum date range 7 days required');
            	$('#date_to').val('');
            }
        });*/

        function getdate() {
            var get_date_from = document.getElementById('date_from').value;
            var datearray = get_date_from.split("-");
            var date_from = datearray[1] + '-' + datearray[0] + '-' + datearray[2];

            var date = new Date(date_from);
            var newdate = new Date(date);
            newdate.setDate(newdate.getDate() + 6);

            var dd = newdate.getDate();
            var mm = newdate.getMonth() + 1;
            var y = newdate.getFullYear();

            var len = dd.toString().length;
            if (len == 1) {
                dd = '0' + dd;
            }

            var someFormattedDate = mm + '-' + dd + '-' + y;
            var datearray2 = someFormattedDate.split("-");
            var new_date_to = datearray2[1] + '-' + datearray2[0] + '-' + datearray2[2];

            document.getElementById('date_to_hidden').value = new_date_to;

        }


    });
</script>


<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('<html><head><title></title>');
        mywindow.document.write('</head><body >');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
    $(document).ready(function() {
        $("#download_xl").click(function(e) {
            e.preventDefault();
            var html = $("#content").html();
            // console.log(html);
            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });
            var downloadUrl = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = downloadUrl;
            a.download = "downloadFile.xls";
            document.body.appendChild(a);
            a.click();
        });
    });
</script>