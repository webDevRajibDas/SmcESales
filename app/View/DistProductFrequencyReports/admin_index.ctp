<style>
    .titlerow th {
        padding: 10px;
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
        <div class="box box-primary">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title; ?></h3>
            </div>

            <div class="box-body">
                <div class="search-box">

                    <?php echo $this->Form->create('PFR', array('role' => 'form')); ?>

                    <table class="search">
                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                            <td class="required">
                                <?php echo $this->Form->input('date_to', array('class' => 'form-control datepicker', 'required' => true, 'readonly' => true)); ?>
                            </td>
                        </tr>
                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td class="" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id',  'empty' => '---- Head Office ----', 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td class="" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'options' => $region_offices,)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                            <tr>
                                <td class="" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id',  'empty' => '---- All ----')); ?></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td class="" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id')); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('rows', array('legend' => 'By :', 'class' => 'rows', 'type' => 'radio', 'default' => 'national', 'options' => $rows_array, 'required' => true));  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('report_type', array('legend' => ' ', 'class' => 'report_type', 'type' => 'radio', 'default' => 'details', 'options' => array('details' => 'Details', 'summery' => 'Summery'), 'required' => true));  ?>
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
            </div>

            <script>
                //$(input[type='checkbox']).iCheck(false); 
                $(document).ready(function() {
                    $("input[type='checkbox']").iCheck('destroy');
                    $("input[type='radio']").iCheck('destroy');
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

            <?php if ($request_data) { ?>
                <!-- Report Print -->
                <div id="content" style="width:90%;height:100%;margin-left:5%;margin-right:5%; float:left;">
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
                            padding: 0;
                        }

                        table {
                            border-collapse: collapse;
                            border-spacing: 0;
                        }

                        .titlerow,
                        .totalColumn {
                            background: #f1f1f1;
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
                            overflow-x: auto;
                        }

                        #sum_table td {
                            padding: 5px 0;
                            text-align: center;
                        }

                        .titlerow th {
                            width: 90px;
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


                                    <?php if ($office_id) { ?>
                                        <span><?= ($region_office_id) ? ', ' : '' ?>Area Office: <?= $offices[$office_id] ?></span>

                                    <?php } ?>
                                </p>
                                <p><span>Print time : </span><span><?= date('d-M-y h:i a'); ?></span></p>
                            </div>



                            <?php if (@$result_set_db) { ?>
                                <div style="float:left; width:100%; height:450px; overflow:scroll;">
                                    <table id="sum_table" class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                                        <thead>
                                            <?php if ($request_data['PFR']['report_type'] == 'details') { ?>
                                                <tr class="">
                                                    <?php if ($rows == 'national') : ?>
                                                        <th></th>
                                                    <?php elseif ($rows == 'region') : ?>
                                                        <th></th>
                                                    <?php elseif ($rows == 'area') : ?>
                                                        <th></th>
                                                        <th></th>
                                                    <?php elseif ($rows == 'sr') : ?>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    <?php elseif ($rows == 'tso') : ?>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    <?php elseif ($rows == 'ae') : ?>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    <?php endif ?>
                                                    <th colspan="22">Product number in a memo</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr class="">
                                                    <?php if ($rows == 'national') : ?>
                                                        <th></th>
                                                    <?php elseif ($rows == 'region') : ?>
                                                        <th></th>
                                                    <?php elseif ($rows == 'area') : ?>
                                                        <th></th>
                                                        <th></th>
                                                    <?php elseif ($rows == 'sr') : ?>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    <?php elseif ($rows == 'tso') : ?>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    <?php elseif ($rows == 'ae') : ?>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    <?php endif ?>
                                                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                                                        <th colspan="2"><?= $i ?></th>
                                                    <?php endfor; ?>
                                                    <th colspan="2">Over 10</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            <?php } ?>
                                            <tr class="titlerow">
                                                <?php if ($rows == 'national') : ?>
                                                    <th>National</th>
                                                <?php elseif ($rows == 'region') : ?>
                                                    <th>Region</th>
                                                <?php elseif ($rows == 'area') : ?>
                                                    <th>Region</th>
                                                    <th>Area</th>
                                                <?php elseif ($rows == 'sr') : ?>
                                                    <th>Region</th>
                                                    <th>Area</th>
                                                    <th>Area Executive</th>
                                                    <th>TSO</th>
                                                    <th>SR</th>
                                                    <th>DB</th>
                                                <?php elseif ($rows == 'tso') : ?>
                                                    <th>Region</th>
                                                    <th>Area</th>
                                                    <th>Area Executive</th>
                                                    <th>TSO</th>
                                                <?php elseif ($rows == 'ae') : ?>
                                                    <th>Region</th>
                                                    <th>Area</th>
                                                    <th>Area Executive</th>
                                                <?php endif ?>
                                                <?php if ($request_data['PFR']['report_type'] == 'details') { ?>
                                                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                                                        <th>Number of memo</th>
                                                        <th>Percentage of total memo</th>
                                                    <?php endfor; ?>
                                                    <th>Number of memo</th>
                                                    <th>Percentage of total memo</th>
                                                <?php } ?>
                                                <!-------- LPC --------------->
                                                <th>Total No. Memo</th>
                                                <th>Total No. SKU</th>
                                                <th>Avg. SKU<br>Per Memo(LPC)</th>
                                            </tr>
                                        <tbody>
                                            <?php foreach ($result_set_db as $data) : ?>
                                                <tr>
                                                    <?php if ($rows == 'national') : ?>
                                                        <td>National :</td>
                                                    <?php elseif ($rows == 'region') : ?>
                                                        <td><?= $data[0]['reg_office'] ?></td>
                                                    <?php elseif ($rows == 'area') : ?>
                                                        <td><?= $data[0]['reg_office'] ?></td>
                                                        <td><?= $data[0]['office'] ?></td>
                                                    <?php elseif ($rows == 'sr') : ?>
                                                        <td><?= $data[0]['reg_office'] ?></td>
                                                        <td><?= $data[0]['office'] ?></td>
                                                        <td><?= $data[0]['ae'] ?></td>
                                                        <td><?= $data[0]['tso'] ?></td>
                                                        <td><?= $data[0]['sr'] ?></td>
                                                        <td><?= $data[0]['db'] ?></td>
                                                    <?php elseif ($rows == 'tso') : ?>
                                                        <td><?= $data[0]['reg_office'] ?></td>
                                                        <td><?= $data[0]['office'] ?></td>
                                                        <td><?= $data[0]['ae'] ?></td>
                                                        <td><?= $data[0]['tso'] ?></td>
                                                    <?php elseif ($rows == 'ae') : ?>
                                                        <td><?= $data[0]['reg_office'] ?></td>
                                                        <td><?= $data[0]['office'] ?></td>
                                                        <td><?= $data[0]['ae'] ?></td>
                                                    <?php endif; ?>
                                                    <?php if ($request_data['PFR']['report_type'] == 'details') { ?>
                                                        <?php for ($i = 1; $i <= 10; $i++) : ?>
                                                            <td><?= $data['0']['count_' . $i] ?></td>
                                                            <td><?= sprintf('%0.2f', ($data['0']['count_' . $i] * 100) / $data['0']['total']) ?></td>
                                                        <?php endfor; ?>
                                                        <td><?= $data['0']['count_11'] ?></td>
                                                        <td><?= sprintf('%0.2f', ($data['0']['count_11'] * 100) / $data['0']['total']) ?></td>
                                                    <?php } ?>
                                                    <!-------------- LPC count ------------------>
                                                    <td><?= sprintf('%0.2f', $data['0']['total']) ?></td>
                                                    <td><?= sprintf('%0.2f', $data['0']['total_product']) ?></td>
                                                    <td><?= sprintf('%0.2f', $data['0']['total_product'] / $data['0']['total']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        </thead>
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

            a.download = "product_frequency_report.xls";

            document.body.appendChild(a);

            a.click();

        });
    });
</script>