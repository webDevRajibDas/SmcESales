<?php //pr($data);
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
        width: 12.5%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .columns_box legend {
        width: 25% !important;
    }

    #market_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #market_list .checkbox {
        width: 30%;
        float: left;
        margin: 1px 0;
    }

    body .td_rank_list .checkbox {
        width: auto !important;
        padding-left: 20px !important;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
    }

    .search label {
        width: 25%;
    }

    #market_list {
        padding-top: 5px;
    }

    .market_list2 .checkbox {
        width: 21% !important;
    }

    .market_list3 .checkbox {
        width: 20% !important;
    }




    .outlet_category {
        float: right;
        width: 85%;
        padding-left: 10%;
        border: #c7c7c7 solid 1px;
        height: 100px;
        overflow: auto;
        margin-right: 5%;
        padding-top: 5px;
    }

    .outlet_category2 {
        padding-left: 3%;
        height: 118px;
    }

    .outlet_category3 {
        width: 92%;
        margin-right: 3%;
        height: 115px;
    }

    .outlet_category .checkbox {
        float: left;
        width: 25% !important;
    }

    .outlet_category3 .checkbox {
        float: left;
        width: 25% !important;
    }

    .label_title {
        float: right;
        width: 85%;
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
        width: 92%;
        margin-right: 3%;
    }

    .outlet_category label {
        width: auto;
    }


    .outlet_category .form-control {
        width: 55% !important;
    }

    .outlet_category2 .input {
        padding-bottom: 3px;
        float: left;
        width: 100%;
    }

    .outlet_category2 label {
        width: 32%;
    }

    .selection_box {
        display: none;
    }

    .search .form-control {
        width: 50%;
    }
</style>

<style>
    #divLoading {
        display: none;
    }

    #divLoading.show {
        display: block;
        position: fixed;
        z-index: 100;
        background-image: url('<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif');
        background-color: #666;
        opacity: 0.4;
        background-repeat: no-repeat;
        background-position: center;
        left: 0;
        bottom: 0;
        right: 0;
        top: 0;
    }

    #loadinggif.show {
        left: 50%;
        top: 50%;
        position: absolute;
        z-index: 101;
        width: 32px;
        height: 32px;
        margin-left: -16px;
        margin-top: -16px;
    }

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
        width: 12.5%;
        display: inline-block;
        font-weight: 700;
        font-size: 14px;
        border-bottom: none;
    }

    .columns_box legend {
        width: 25% !important;
    }

    #market_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #market_list .checkbox {
        width: 30%;
        float: left;
        margin: 1px 0;
    }

    body .td_rank_list .checkbox {
        width: auto !important;
        padding-left: 20px !important;
    }

    .radio input[type="radio"],
    .radio-inline input[type="radio"] {
        margin-left: 0px;
        position: relative;
        margin-top: 8px;
    }

    .search label {
        width: 25%;
    }

    #market_list {
        padding-top: 5px;
    }

    .market_list2 .checkbox {
        width: 21% !important;
    }

    .market_list3 .checkbox {
        width: 20% !important;
    }
</style>

<div id="divLoading" class=""> </div>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="float:left;">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Weekly Bank Deposition Information'); ?></h3>
                <div class="box-tools pull-right">
                    <?php //if($this->App->menu_permission('deposits','admin_add')){ echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Deposit'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); } 
                    ?>
                </div>
            </div>



            <div class="box-body">
                <div class="search-box">
                    <?php echo $this->Form->create('WeeklyBankDepositionInformation', array('role' => 'form')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>

                            <td class="required" width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker date_to', 'required' => true, 'readonly' => true)); ?></td>
                        </tr>

                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => true, 'empty' => '---- Head Office ----', 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false, 'empty' => '---- All ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('type', array('legend' => 'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'territory', 'onClick' => 'typeChange(this.value)', 'options' => $types, 'required' => true));  ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div id="territory_html">
                                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id office_t_so', 'required' => false, 'empty' => '---- All ----')); ?>
                                </div>

                                <div id="so_html">
                                    <?php echo $this->Form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----')); ?>
                                </div>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>
                                <div class="checkbox" style="margin-left:24%;padding-left:0px;width:auto">
                                    <label class="checkbox-inline"><input name="data[WeeklyBankDepositionInformation][qumulative]" type="checkbox" value="1" <?php if (isset($this->request->data['WeeklyBankDepositionInformation']['qumulative'])) echo 'checked'; ?>> Qumulative</label>
                                </div>
                            </td>
                            <td width="50%"></td>
                        </tr>



                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->button('<i class="fa fa-search"></i> Search', array('type' => 'submit', 'id' => 'search_button', 'class' => 'btn btn-large btn-primary', 'escape' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                            </td>
                        </tr>

                    </table>
                    <?php echo $this->Form->end(); ?>
                </div>




                <?php if (!empty($request_data)) { ?>

                    <div class="pull-right csv_btn" style="padding-top:20px;">
                        <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'escape' => false, 'id' => 'download_xl')); ?>
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
                            font-weight: bold;
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

                        <div style="float:left; width:100%; text-align:center; padding:20px 0; clear:both;">
                            <h2 style="margin:2px 0;">SMC Enterprise Limited</h2>
                            <h3 style="margin:2px 0;">Weekly Bank Deposition Information</h3>
                            <p>
                                Time Frame : <b><?= date('d M, Y', strtotime($date_from)) ?></b> to <b><?= date('d M, Y', strtotime($date_to)) ?></b>
                            </p>
                            <p">Area : <?php if (!empty($office_id)) echo $offices[$office_id]; ?></p>
                        </div>




                        <div style="float:left; width:100%; height:450px; overflow:scroll;">
                            <table class="print-table table table-bordered table-responsive" style="width:100%" border="1px solid black" cellpadding="2px" cellspacing="0">
                                <thead>
                                    <tr class="titlerow">
                                        <td>Instrument</td>
                                        <td>Region</td>
                                        <td>Area Office</td>
                                        <td><?= ($type == 'territory') ? 'Territory' : 'SO Name' ?></td>
                                        <td style="text-align:right;">Total</td>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($cash_results as $region_id => $region_data) { ?>
                                        <?php foreach ($region_data as $area_id => $area_data) { ?>

                                            <?php
                                            if (@$area_data['child_results']) {

                                            ?>
                                                <?php
                                                foreach ($area_data['child_results'] as $id => $datas) {
                                                    $tso_total = 0;
                                                    $tso_total += $datas['Cash']['deposit_amount'];
                                                ?>
                                                    <tr class="view_data" style="cursor:pointer;" data-href="<?= BASE_URL . 'weekly_bank_deposition_information/details_report?region_id=' . $region_id . '&office_id=' . $area_id . '&territory_id=' . ($type == 'territory' ? $id : 0) . '&so_id=' . ($type == 'so' ? $id : 0) . '&date_from=' . $date_from . '&date_to=' . $date_to; ?>">
                                                        <td>Cash</td>
                                                        <td><?= $region_offices[$region_id] ?></td>
                                                        <td><?= $offices[$area_id] ?></td>
                                                        <td><?= @($type == 'territory') ? $territories[$id] : $so_list[$id] ?></td>
                                                        <td style="text-align:right;"><?= sprintf("%01.2f", $datas['Cash']['deposit_amount']) ?></td>
                                                    </tr>

                                                    <?php if (@$ins_results[$region_id][$area_id]['child_results'][$id]) { ?>
                                                        <?php
                                                        foreach ($ins_results[$region_id][$area_id]['child_results'][$id] as $key => $datas) {
                                                            $tso_total += $datas['deposit_amount'];
                                                        ?>
                                                            <tr class="view_data" style="cursor:pointer;" data-href="<?= BASE_URL . 'weekly_bank_deposition_information/details_report?region_id=' . $region_id . '&office_id=' . $area_id . '&territory_id=' . ($type == 'territory' ? $id : 0) . '&so_id=' . ($type == 'so' ? $id : 0) . '&date_from=' . $date_from . '&date_to=' . $date_to; ?>">
                                                                <td><?= $key ?></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td><?php /*?><?=@($type=='territory')?$territories[$id]:$so_list[$id]?><?php */ ?></td>
                                                                <td style="text-align:right;"><?= sprintf("%01.2f", $datas['deposit_amount']) ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <tr class="totalColumn">
                                                        <td colspan="4" style="text-align:right;">Total :</td>
                                                        <td style="text-align:right;"><?= $tso_total ?></td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>



                                            <?php
                                            } else {
                                                $id = 0;
                                            ?>

                                                <tr class="view_data" style="cursor:pointer;" data-href="<?= BASE_URL . 'weekly_bank_deposition_information/details_report?region_id=' . $region_id . '&office_id=' . $area_id . '&territory_id=' . ($type == 'territory' ? $id : 0) . '&so_id=' . ($type == 'so' ? $id : 0) . '&date_from=' . $date_from . '&date_to=' . $date_to; ?>">
                                                    <td><?= $area_data['type'] ?></td>
                                                    <td><?= $region_offices[$region_id] ?></td>
                                                    <td><?= @$offices[$area_id] ?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?= sprintf("%01.2f", $area_data['deposit_amount']) ?></td>
                                                </tr>

                                                <?php $a_total = $area_data['deposit_amount']; ?>

                                                <?php if (@$ins_results[$region_id][$area_id]) { ?>
                                                    <?php
                                                    foreach ($ins_results[$region_id][$area_id] as $key => $datas) {
                                                        $a_total += $datas['deposit_amount'];
                                                    ?>
                                                        <tr class="view_data" style="cursor:pointer;" data-href="<?= BASE_URL . 'weekly_bank_deposition_information/details_report?region_id=' . $region_id . '&office_id=' . $area_id . '&territory_id=' . ($type == 'territory' ? $id : 0) . '&so_id=' . ($type == 'so' ? $id : 0) . '&date_from=' . $date_from . '&date_to=' . $date_to; ?>">
                                                            <td><?= $key ?></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td style="text-align:right;"><?= sprintf("%01.2f", $datas['deposit_amount']) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>

                                                <tr style="font-weight:bold;">
                                                    <td colspan="4" style="text-align:right;">Total :</td>
                                                    <td style="text-align:right;"><?= $a_total ?></td>
                                                </tr>
                                            <?php } ?>



                                        <?php } ?>
                                    <?php } ?>

                                </tbody>

                            </table>
                        </div>

                        <?php /*?><div style="float:left; width:100%; padding:100px 0 50px;; font-size:13px;">
                <div style="width:33%;text-align:left;float:left">
                  Prepared by:______________ 
                </div>
                <div style="width:33%;text-align:center;float:left">
                  Checked by:______________ 
                </div>
                <div style="width:33%;text-align:right;float:left">
                  Signed by:______________
                </div>		  
              </div><?php */ ?>



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
        url: '<?= BASE_URL . 'market_characteristic_reports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('.region_office_id').change(function() {
        $('#territory_id').html('<option value="">---- All ----');
    });
    $('.office_id').selectChain({
        target: $('.territory_id'),
        value: 'name',
        url: '<?= BASE_URL . 'sales_people/get_territory_list' ?>',
        type: 'post',
        data: {
            'office_id': 'office_id'
        }
    });
</script>

<script>
    $(document).ready(function() {
        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');

        $('#checkall1').click(function() {
            var checked = $(this).prop('checked');
            $('.selection10').find('input:checkbox').prop('checked', checked);

        });


        $('#checkall').click(function() {
            var checked = $(this).prop('checked');
            $('.product_selection').find('input:checkbox').prop('checked', checked);
        });

        $('#checkall2').click(function() {
            var checked = $(this).prop('checked');
            $('.brand_selection').find('input:checkbox').prop('checked', checked);
        });

        $('#checkall3').click(function() {
            var checked = $(this).prop('checked');
            $('.category_selection').find('input:checkbox').prop('checked', checked);
        });


        $('.view_data').click(function() {
            var url = $(this).data('href');
            //window.open(url, 'details', 'titlebar=no, status=no, menubar=no, resizable=yes, scrollbars=yes, toolbar=no,location=no, height=1000, width=1000, top=50, left=50');
            var win = window.open(url, '_blank');
            win.focus();
        });

    });

    $('#office_id').change(function() {
        //alert($(this).val());
        date_from = $('.date_from').val();
        date_to = $('.date_to').val();
        if (date_from && date_to) {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>market_characteristic_reports/get_office_so_list',
                data: 'office_id=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
                cache: false,
                success: function(response) {
                    //alert(response);						
                    $('#so_id').html(response);
                }
            });
        } else {
            $('#office_id option:nth-child(1)').prop("selected", true);
            alert('Please select date range!');
        }
    });


    $(document).ready(function() {
        typeChange();



    });


    function typeChange() {
        var type = $('.type:checked').val();

        //for territory and so 
        $('#so_html').hide();
        $('#territory_html').hide();


        if (type == 'so') {
            $('#so_html').show();
        } else {
            $('#territory_html').show();
        }

        if (type == 'so') {
            $('.office_t_so option:nth-child(1)').prop("selected", true).change();
        } else if (type == 'territory') {
            $('#so_id option:nth-child(1)').prop("selected", true).change();
        } else {
            <?php if (!@$request_data['WeeklyBankDepositionInformation']['territory_id']) { ?>
                $('.office_t_so option:nth-child(1)').prop("selected", true).change();
            <?php } ?>

            <?php if (!@$request_data['WeeklyBankDepositionInformation']['so_id']) { ?>
                $('#so_id option:nth-child(1)').prop("selected", true).change();
            <?php } ?>
        }


    }
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
            a.download = "weekly_bank_deposition_report.xls";
            document.body.appendChild(a);
            a.click();
        });
    });
</script>