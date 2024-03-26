<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:750px">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?= $page_title; ?></h3>
            </div>

            <div class="box-body">
                <!-- <div class="search-box">


                </div> -->
            </div>


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

                            <!-- <p>
                                <?php if ($region_office_id) { ?>
                                    <span>Region Office: <?= $region_offices[$region_office_id] ?></span>
                                <?php } ?>
                                <?php if ($office_id) { ?>
                                    <span><?= ($region_office_id) ? ', ' : '' ?>Area Office: <?= $offices[$office_id] ?></span>
                                <?php } ?>
                            </p> -->
                        </div>



                        <?php if (@$memos) { ?>
                            <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
                            <div style="float:left; width:100%; height:500px; overflow:scroll;">
                                <table class="text-center report_table" border="1px solid black" cellpadding="10px" cellspacing="0" align="center">
                                    <thead>
                                        <tr>
                                            <th>Office</th>
                                            <th>Area Executive</th>
                                            <th>TSO</th>
                                            <th>Distributor</th>
                                            <th>ORS-N QTY(Cartoon)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($memos as $data) { ?>
                                            <tr>
                                                <td><?= $data['0']['office'] ?></td>
                                                <td><?= $data['0']['ae'] ?></td>
                                                <td><?= $data['0']['tso'] ?></td>
                                                <td><?= $data['0']['db'] ?></td>

                                                <td><?= sprintf('%0.2f', $data['0']['qty']) ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
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



        </div>
    </div>
</div>


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

            a.download = "crash_trade_program_reports(12may to 31may 2022).xls";

            document.body.appendChild(a);

            a.click();

        });

    });
</script>