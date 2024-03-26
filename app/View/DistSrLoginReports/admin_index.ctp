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
</style>

<div id="divLoading" class=""> </div>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">



            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title ?></h3>

            </div>


            <div class="box-body">

                <div class="search-box">
                    <?php echo $this->Form->create('DistSrLoginReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date', array('class' => 'form-control datepicker date_from', 'required' => true, 'readonly' => true)); ?></td>
                            <td>
                                <?php echo $this->Form->input('present_status', array('default' => 'absent', 'class' => 'form-control', 'options' => $present_status_array, 'empty' => '--- All ---'));  ?></td>
                        </tr>


                        <tr>
                            <?php if ($office_parent_id == 0) { ?>
                                <td class="required" width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
                            <?php } ?>
                            <?php if ($office_parent_id == 14) { ?>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices,)); ?></td>
                            <?php } ?>
                            <?php if ($office_parent_id == 0 || $office_parent_id == 14) { ?>
                                <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'empty' => '---- All ----')); ?></td>

                        </tr>
                    <?php } else { ?>

                        <td class="required" width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id')); ?></td>

                    <?php } ?>
                    </tr>

                    <tr>
                        <td>
                            <?php echo $this->Form->input('tso_id', array('label' => 'TSO :', 'id' => 'tso_id', 'class' => 'form-control db_id', 'empty' => '--- All ---')); ?>
                        </td>
                        <td>
                            <?php echo $this->Form->input('db_id', array('label' => 'Distributor :', 'id' => 'db_id', 'class' => 'form-control db_id', 'empty' => '--- All ---')); ?>

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






                <?php if (!empty($result)) { ?>

                    <div id="content" style="width:90%; margin:0 5%;">
                        <div class="table-responsive">

                            <div class="pull-right csv_btn" style="padding-top:20px;">
                                <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'id' => 'download_xl', 'escape' => false)); ?>
                            </div>

                            <div id="xls_body">

                                <div style="float:left; width:100%; height:430px; overflow:scroll;">
                                    <table class="table table-bordered table-condensed" cellspacing="0" cellpadding="10px" border="1px solid black" align="center">
                                        <tr>
                                            <th>Area Office</th>
                                            <th>TSO</th>
                                            <th>TSO Mobile Number</th>
                                            <th>DB</th>
                                            <th>SR</th>
                                            <th>SR Mobile Number</th>
                                            <th>Last Sync Time</th>
                                            <?php if ($request_data['DistSrLoginReports']['present_status'] == 'present' || $request_data['DistSrLoginReports']['present_status'] == '') { ?>
                                                <th>Check In</th>
                                                <th>Check Out</th>
                                            <?php } ?>
                                            <th>Total Order</th>
                                            <th>Order Value</th>
                                        </tr>
                                        <?php foreach ($result as $data) { ?>
                                            <tr>
                                                <td><?= $data['Office']['office_name'] ?></td>
                                                <td><?= $data['DistTso']['name'] ?></td>
                                                <td style="mso-number-format:\@;"><?= $data['DistTso']['mobile_number'] ?></td>
                                                <td><?= $data['DistDistributor']['name'] ?></td>
                                                <td><?= $data['DistSalesRepresentatives']['name'] ?></td>
                                                <td style="mso-number-format:\@;"><?= $data['DistSalesRepresentatives']['mobile_number'] ?></td>
                                                <td><?= isset($data['SalesPerson']['last_data_push_time']) ? date('Y-m-d h:i a', strtotime($data['SalesPerson']['last_data_push_time'])) : '' ?></td>
                                                <?php if ($request_data['DistSrLoginReports']['present_status'] == 'present' || $request_data['DistSrLoginReports']['present_status'] == '') { ?>
                                                    <td><?= isset($data['DistSrCheckInOut']['check_in_time']) ? date('d M Y h:i a', strtotime($data['DistSrCheckInOut']['check_in_time'])) : '' ?></td>
                                                    <td><?= isset($data['DistSrCheckInOut']['check_out_time']) ? date('d M Y h:i a', strtotime($data['DistSrCheckInOut']['check_out_time'])) : '' ?></td>
                                                <?php } ?>
                                                <td><?= isset($sr_wise_order[$data['DistSalesRepresentatives']['id']]['total_order']) ? $sr_wise_order[$data['DistSalesRepresentatives']['id']]['total_order'] : 0 ?></td>
                                                <td><?= sprintf("%0.2f", isset($sr_wise_order[$data['DistSalesRepresentatives']['id']]['order_value']) ? $sr_wise_order[$data['DistSalesRepresentatives']['id']]['order_value'] : 0) ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>

                <?php } else { ?>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<script>
    $('.region_office_id').selectChain({
        target: $('.office_id'),
        value: 'name',
        url: '<?= BASE_URL . 'dist_market_characteristic_reports/get_office_list'; ?>',
        type: 'post',
        data: {
            'region_office_id': 'region_office_id'
        }
    });
    $('.region_office_id').change(function() {
        $('#territory_id').html('<option value="">---- All ----');
    });
</script>


<script>
    $(document).ready(function() {


        if ($('#office_id').val()) {
            get_tso_list();
        }
        $('#office_id').change(function() {

            get_tso_list();

        });

        if ($('#tso_id').val()) {
            get_db_list();
        }
        $('#tso_id').change(function() {

            get_db_list();

        });

        function get_tso_list() {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>dist_sr_login_reports/get_tso_list',
                data: 'office_id=' + $('#office_id').val(),
                cache: false,
                success: function(response) {
                    //alert(response);                      
                    $('#tso_id').html(response);
                    <?php if (isset($this->request->data['DistSrLoginReports']['tso_id'])) { ?>
                        if ($('#tso_id').val(<?= $this->request->data['DistSrLoginReports']['tso_id'] ?>)) {
                            get_db_list();
                        }
                    <?php } ?>
                }
            });
        }

        function get_db_list() {
            $.ajax({
                type: "POST",
                url: '<?= BASE_URL ?>dist_sr_login_reports/get_db_list',
                data: 'tso_id=' + $('#tso_id').val(),
                cache: false,
                success: function(response) {
                    //alert(response);                      
                    $('#db_id').html(response);
                    <?php if (isset($this->request->data['DistSrLoginReports']['db_id'])) { ?>
                        $('#db_id').val(<?= $this->request->data['DistSrLoginReports']['db_id'] ?>);
                    <?php } ?>
                }
            });
        }

    });
</script>

<script>
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=1000');

        //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('<html><head><title></title><style>.csv_btn{display:none;}</style>');
        mywindow.document.write('</head><body>');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        //mywindow.close();

        return true;
    }

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

            a.download = "market_characteristic_reports.xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>