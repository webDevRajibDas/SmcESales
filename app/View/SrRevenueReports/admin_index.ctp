<?php
App::import('Controller', 'SrRevenueReportsController');
$SrRevenueReportsController = new SrRevenueReportsController;

// if (!empty($this->request->data)) {
//     $row = $this->request->data['DistRevenueReport']['col_id'];
// }

//pr($this->request->data); die();
?>



<style>
    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 15px 0px 5px;
        margin: 0px;
    }

    .search .radio legend {
        float: left;
        margin: 5px 20px 0 0;
        text-align: right;
        width: 15%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
        min-width: 25px;
    }

    #market_list .checkbox label {
        padding-left: 10px;
        width: auto;
    }

    #market_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    #checkbox_list .checkbox label {
        padding-left: 10px;
        width: auto;
        float: none;
        text-align: left;
        margin: 0px;
    }

    #checkbox_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    .custom_design {
        width: 200px;
    }

    .office_search_table tr td {
        padding: 0px 10px;
    }

    #checkbox_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #checkbox_list .checkbox {
        width: 20%;
        float: left;
        margin: 1px 0;
    }

    .outlet_category {
        float: right;
        width: 98%;
        padding-left: 5%;
        border: #c7c7c7 solid 1px;
        height: 100px;
        overflow: auto;
        margin-right: 3%;
        padding-top: 5px;
    }

    .outlet_category3 {
        width: 97%;
        margin-right: 3%;
        height: 115px;
    }

    .outlet_category .checkbox {
        float: left;
        width: 100% !important;
    }

    .outlet_category3 .checkbox {
        float: left;
        width: 50% !important;
    }

    .label_title {
        float: right;
        width: 95%;
        background: #c7c7c7;
        margin: 0px;
        padding: 1px 0px;
        text-align: center;
        color: #000;
        margin-right: 5%;
        font-weight: bold;
        font-size: 90%;
    }

    .pro_label_title {
        width: 97%;
        margin-right: 3%;
    }

    .outlet_category label {
        width: auto;
    }

    .radio label,
    .checkbox label {
        padding-left: 5px;
        float: none;
        width: 23%;
        text-align: left;
    }
</style>

<div class="row">
    <div class="col-xs-12">

        <div class="box box-primary" style="min-height: 210px;">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title; ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">

                    <?php echo $this->Form->create('DistRevenueReport', array('role' => 'form')); ?>
                    
                    
                        
                            <table class='search' >
                                <tr>
                                    

                                    <td width="50%">
                                        
                                        <label for="date_from" >Month</label>
                                        <?php echo $this->Form->input('date_from', array('id' => 'datepicker', 'class' => 'form-control', 'required' => true,'label' => false, 'autocomplete' => 'off')); ?>
                                        
                                    </td>

                                    <td width="50%">
                                        <?php if ($office_parent_id == 0) { ?>
                                            <label for="region_office_id" >Region Office</label>
                                            <?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices, 'label' => false, 'required' => false,)); ?>
                                        <?php } elseif ($office_parent_id == 14) { ?>
                                            <label for="region_office_id" >Region Office</label>
                                            <?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'options' => $region_offices, 'label' => false, 'required' => false,)); ?>
                                        <?php } ?>
                                    </td>

                                    
                                </tr>

                                <tr>
                                    

                                    <td width="50%">
                                        <label for="office_id" >Area Office</label>
                                        <?php
                                        if ($office_parent_id == 0 || $office_parent_id == 14) {
                                            echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- All ----', 'label' => false, 'required' => false,));
                                        } else {
                                            echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'form-control office_id', 'label' => false, 'required' => false, 'empty' => '---- Select ----'));
                                        }
                                        ?>
                                    </td>

                                    <td width="50%">
                                        <label for="ae_id" >Area Executive</label>
                                        <?php echo $this->Form->input('ae_id', array('id' => 'ae_id', 'class' => 'form-control ae_id', 'required' => false, 'empty' => '---- All ----', 'label' => false)); ?>
                                    </td>
                                </tr>

                                <tr>
                                    
                                    <td width="50%">
                                        <label for="tso_id" >TSO</label>
                                        <?php echo $this->Form->input('tso_id', array('id' => 'tso_id', 'class' => 'form-control tso_id', 'required' => false, 'empty' => '---- All ----', 'label' => false)); ?>
                                    </td>
                                    <td width="50%">
                                        <label for="sr_id" >SR</label>
                                        <?php echo $this->Form->input('sr_id', array('id' => 'sr_id', 'class' => 'form-control sr_id', 'required' => false, 'empty' => '---- All ----', 'label' => false)); ?>
                                    </td>
                                </tr>

                                <tr>
                                    
                                    <td colspan="2" >
                                    
                                        <label style="float:left; width:15%;">Report Type :</label>
                                        <div id="market_list" class="input select" style="float:left; width:80%; padding-left:20px;">
                                            <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                                <input style="margin:0px 5px 0px 0px;" type="checkbox"  class="checkall" />
                                                <label for="report_for" style="float:none; width:auto;  cursor:pointer;">Select / Unselect All</label>
                                            </div>
                                            <div class="selection">
                                                <?php echo $this->Form->input('report_for', array('id' => 'report_for', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $report_for)); ?>
                                            </div>
                                        </div>
                            
                                    </td>
                                    
                                </tr>

                                <tr align="center">
                                    <td colspan="2">

                                        <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>
                                        <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>

                                    </td>
                                </tr>
                                

                            </table>
                            <?php echo $this->Form->end(); ?> 
                        
                </div>


                <?php if ($request_data) { ?>
                    <!-- Report Print -->
                    <div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%;">
                        <!-- <div style="text-align:right;width:100%;">Page No :1 of 1</div>
                <div style="text-align:right;width:100%;">Print Date :19 Jul 2017</div> -->

                        <style type="text/css">
                            .table-responsive {
                                color: #333;
                                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                                line-height: 1.42857;
                            }

                            .report_table {
                                font-size: 12px;
                            }

                            .qty_val {
                                width: 90px;
                                margin: 0;
                                float: left;
                                text-transform: capitalize;
                            }

                            /*.qty, .val{ width:49%; float:left; border-right:#333 solid 1px; text-align:center; padding:5px 0;}*/
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

                            .titlerow th,
                            .titlerow td {
                                background: #f1f1f1;
                                font-size: 12px;
                                font-weight: bold;
                            }

                            #sum_table {
                                font-size: 75%;
                            }

                            .report_table {
                                margin-bottom: 18px;
                                max-width: 100%;
                                width: 100%;
                            }

                            .table-responsive {
                                min-height: 0.01%;
                                overflow-y: auto;
                            }

                            #sum_table td {
                                padding: 5px;
                                text-align: center;
                            }
                        </style>

                        <div class="table-responsive">
                            <div class="pull-right csv_btn" style="padding-top:20px;">
                                <?php /*?><?=$this->Html->link(__('Download XLS'), array('action' => 'Download_xls?data='.serialize($request_data)), array('class' => 'btn btn-primary', 'escape' => false)); ?><?php */ ?>
                                <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

                            <div id="xls_body">
                                <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both; font-weight:bold;">
                                    <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
                                    <h3 style="margin:2px 0;"><?= $page_title; ?></h3>
                                    <p>
                                        <b> Time Frame : <?= @date('d M, Y', strtotime($date_from)) ?> to <?= @date('d M, Y', strtotime($date_to)) ?></b>
                                    </p>
                                    <p>
                                        <?php if ($region_office_id) { ?>
                                            <span>Region Office: <?= $region_offices[$region_office_id] ?></span>
                                        <?php } ?>

                                    <?php if($index_area_office_show==1){ ?>
                                        <?php if ($office_id) { ?>
                                            <span><?= ($region_office_id) ? ', ' : '' ?>Area Office: <?= $offices[$office_id] ?></span>
                                        <?php } }?>
                                    </p>
                                </div>



                                <?php if (@$data_all_sr) { ?>
                                    <!-- product quantity get-->



                                    <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                        <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">


                                            <?php echo $html; ?>




                                        </table>
                                    </div>




                                <?php } else { ?>

                                    <div style="clear:both;"></div>
                                    <div class="alert alert-warning">No Report Found!</div>

                                <?php } ?>
                            </div>


                            <!--<div style="float:left; width:100%; padding-top:100px;">
                        <div style="width:33%;text-align:left;float:left">
                            Prepared by:______________ 
                        </div>
                        <div style="width:33%;text-align:center;float:left">
                            Checked by:______________ 
                        </div>
                        <div style="width:33%;text-align:right;float:left">
                            Signed by:______________
                        </div>		  
                    </div>-->

                        </div>

                    </div>
                <?php } ?>

            </div>

        </div>

    </div>
</div>




<script>
    //$(input[type='checkbox']).iCheck(false); 
    $(document).ready(function() {


        // $('#report_for').attr('required', true);

        // $("form").submit(function( event ) {

        //     if($("#report_for").val().length == 0){
        //         alert("You must select at least one option");
        //         return false;
                
        //         // $("#divLoading_default").hide();
        //         $("#divLoading_default").removeClass("show");
        //     }else{
        //         return true;
        //     }
            
        //     event.preventDefault();
        //     $("#divLoading_default").removeClass("show");
        // });

        

        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');
        $('.check_all').click(function() {
            var checked = $(this).prop('checked');
            $(this).parent().parent().find('.selection input:checkbox').prop('checked', checked);
        });
    });
</script>

<script>
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'SrRevenueReports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    
    $('.office_id').selectChain({
        
        target: $('.ae_id'),
        value: 'name',
        url: '<?= BASE_URL . 'SrRevenueReports/get_ae_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id',
            'month':'datepicker'
        }
    });
    $('.ae_id').selectChain({
        target: $('.tso_id'),
        value: 'name',
        url: '<?= BASE_URL . 'SrRevenueReports/get_tso_list' ?>',
        type: 'post',
        data: {
            'ae_id': 'ae_id',
            'month':'datepicker'
        }
    });
    $('.tso_id').selectChain({
        target: $('.sr_id'),
        value: 'name',
        url: '<?= BASE_URL . 'SrRevenueReports/get_sr_list' ?>',
        type: 'post',
        data: {
            'tso_id': 'tso_id',
            'month':'datepicker'
        }
    });

    $('#datepicker').change(function() {
        
        
        $('.ae_id').html('<option value="">---- All ----</option>');
        $('.tso_id').html('<option value="">---- All ----</option>');
        $('.sr_id').html('<option value="">---- All ----</option>');
    });

    $('.region_office_id').change(function() {
        $('.ae_id').html('<option value="">---- All ----</option>');
        $('.tso_id').html('<option value="">---- All ----</option>');
        $('.sr_id').html('<option value="">---- All ----</option>');
    });
    $('.office_id').change(function() {
        $('.tso_id').html('<option value="">---- All ----</option>');
        $('.sr_id').html('<option value="">---- All ----</option>');
    });
    $('.ae_id').change(function() {
        $('.sr_id').html('<option value="">---- All ----</option>');
    });
</script>



<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

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
</script>
<script>
    $(document).ready(function() {

        $('.checkall').click(function (e) {
            var checked = $(this).prop('checked');
            $(this).closest('.select').find('.selection').find('input:checkbox').prop('checked', checked);
        });

        $("#download_xl").click(function(e) {

            e.preventDefault();

            var html = $("#xls_body").html();

            // console.log(html);

            var blob = new Blob([html], {
                type: 'data:application/vnd.ms-excel'
            });

            var downloadUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");

            a.href = downloadUrl;

            a.download = "Sr_wise_Revenue_and_EC_Report.xls";

            document.body.appendChild(a);

            a.click();

        });
        get_product_list($(".product_type:checked").serializeArray());
        $(".product_type").change(function() {
            product_type = $(".product_type:checked").serializeArray();
            console.log(product_type);
            get_product_list(product_type);
        });
        var product_check = <?php echo @json_encode($this->request->data['Memo']['product_id']); ?>;
        console.log(product_check);

        function get_product_list(product_type) {
            $.ajax({
                type: "POST",
                //url: '<?= BASE_URL ?>sales_analysis_reports/get_office_so_list',
                url: '<?= BASE_URL ?>sales_reports/get_product_list',
                data: product_type,
                cache: false,
                success: function(response) {
                    $(".product").html(response);
                    if (product_check) {
                        $.each(product_check, function(i, val) {

                            $(".product_id>input[value='" + val + "']").prop('checked', true);

                        });
                    }
                }
            });
        }

    });

    $("#datepicker").datepicker({
		format: "mm-yyyy",
		viewMode: "years",
		minViewMode: "months",
        autoclose: true
	});
</script>