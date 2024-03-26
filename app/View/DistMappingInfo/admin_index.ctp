<?php //pr($so_info); 
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
        width: 30%;
        float: left;
        margin: 1px 0;
    }

    label {
        float: left;
        width: 30%;
        text-align: right;
        margin: 5px 20px 0 0;
    }

    .form-control {
        float: left;
        width: 60%;
        font-size: 13px;
        height: 28px;
        padding: 0px 4px;
    }

    .search label {
        width: 20%;
    }

    .row_list div.list,
    .input.radio {
        float: left;
        width: 50%;
        margin: 0px;
    }

    .row_list label {
        width: auto;
    }

    .row_list input.form-control {
        width: auto;
        margin: 0 !important;
    }

    .data_box {
        float: left;
        width: 90%;
        position: relative;
        border: #ccc solid 1px;
        padding: 10px 10px;
        margin-bottom: 20px;
        margin-top: 10px;
        margin-left: 10px;
    }

    .data_box .row_list div.list,
    .data_box .input.radio {
        float: left;
        margin: 0;
        width: 48%;
    }

    .data_box .list label,
    .data_box .input.radio label {
        cursor: pointer;
        font-weight: 400;
        margin-bottom: 0;
        min-height: 20px;
        padding-left: 16px;
        width: 75%;
        text-align: left;
    }

    .data_box .input.radio label {
        margin-left: 12px;
    }

    .box_title {
        width: auto;
        margin: -22px 0 0 0;
    }

    .box_title span {
        background: #fff;
        padding: 0 5px;
    }
</style>



<div class="row">

    <div class="col-xs-12">
        <div class="box box-primary" style="float:left;">

            <div class="box-header">
                <h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('Stock status by SO Reports'); ?></h3>
            </div>

            <div class="box-body" style="float:left; width:100%;">

                <div class="search-box" style="padding-bottom:20px; margin-bottom:30px;">
                    <?php echo $this->Form->create('DistMappingInfo', array('role' => 'form')); ?>
                    <div class="row">
                        <div class="col-xs-12 col-md-6" /*style="border:1px dotted black;" * />
                        <div class="form-group">
                            <?php
                            if ($office_parent_id == 0) {
                                echo $this->Form->input('office_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'required' => false));
                            } else {
                                echo $this->Form->input('office_id', array('class' => 'form-control', 'required' => true, 'disabled' => false));
                            }
                            ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->Form->input('ae_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'required' => false, 'label' => 'AE :')); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->Form->input('tso_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'required' => false, 'label' => 'TSO :')); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->Form->input('db_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'required' => false, 'label' => 'DB :')); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->Form->input('sr_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'required' => false, 'label' => 'SR :')); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->Form->input('route_id', array('class' => 'form-control', 'empty' => '---- Select ----', 'required' => false, 'label' => 'Route/Beat :')); ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="data_box">
                            <h4 class="box_title"><span>List For</span></h4>
                            <div id="market_list" class="input row_list" style="float:left; width:100%; padding-left:0px;">
                                <?php echo $this->Form->input('list_for', array('legend' => false, 'type' => 'radio', 'id' => 'listfor', 'default' => 'route', 'options' => $listFor, 'separator' => '</div><div class="list">', 'class' => 'form-control columns')); ?>
                            </div>
                            <div class="form-group">
                                <?php echo $this->Form->input('search_text', array('class' => 'form-control')) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div style="float:left; width:100%; padding-top:20px; text-align:center;">
                            <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false, 'name' => 'submit')); ?>


                            <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                        </div>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>

        <div class="box-body" style="float:left; width:100%;">
            <?php if (isset($lists) && $lists) { ?>
                <div class="pull-right csv_btn" style="padding-right:15px;">
                    <?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'escape' => false, 'id' => 'download_xl')); ?>
                </div>

                <div id="content" style="width:98%; height:100%;margin-left:1%;margin-right:1%;">
                    <style type="text/css">
                        .table-responsive {
                            color: #333;
                            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                            line-height: 1.42857;
                        }

                        .table-responsive {
                            min-height: 0.01%;
                            overflow-x: auto;
                        }
                    </style>

                    <div style="width:100%; text-align:center; font-size:12px;">
                        <div style="font-size:20px;">SMC Enterprise Limited</div>
                        <div style="font-size:14px;"><strong>LIST FOR <?php echo $listFor[$list_for]; ?></strong></div>
                    </div>

                    <div style="float:left; width:100%; height:450px; overflow:scroll;">
                        <table class="table table-bordered table-responsive table-condensed table-striped table-hover " style="width:100%" border="1px solid black" cellpadding="2px" cellspacing="0">
                            <thead>
                                <tr>
                                    <?php foreach ($column as $column_head) { ?>
                                        <td class="text-center"><?= $column_head ?></td>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lists as $data) { ?>
                                    <tr>
                                        <?php foreach ($column as $column_index => $column_head) { ?>
                                            <td><?= $data[$column_index] ?></td>
                                        <?php } ?>
                                    </tr>
                                <?php  } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

</div>
</div>

<script>
    $(document).ready(function() {
        $('input').iCheck('destroy');
        hide_list_item();
        if ($("#DistMappingInfoOfficeId").val()) {
            var ae_id = $("#DistMappingInfoAeId").val();
            var tso_id = $("#DistMappingInfoTsoId").val();
            var db_id = $("#DistMappingInfoDbId").val();
            var sr_id = $("#DistMappingInfoSrId").val();
            var route_id = $("#DistMappingInfoRouteId").val();

            get_ae_by_office_id(ae_id);
            get_tso_by_office_id_ae_id(tso_id);
            get_db_by_office_id_ae_id_tso_id(db_id);
            get_sr_by_office_id_ae_id_tso_id_db_id(sr_id);
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id(route_id);
        }

        if ($("#DistMappingInfoAeId").val()) {
            var tso_id = $("#DistMappingInfoTsoId").val();
            var db_id = $("#DistMappingInfoDbId").val();
            var sr_id = $("#DistMappingInfoSrId").val();
            var route_id = $("#DistMappingInfoRouteId").val();
            get_tso_by_office_id_ae_id(tso_id);
            get_db_by_office_id_ae_id_tso_id(db_id);
            get_sr_by_office_id_ae_id_tso_id_db_id(sr_id);
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id(route_id);
        }

        if ($("#DistMappingInfoTsoId").val()) {
            var db_id = $("#DistMappingInfoDbId").val();
            var sr_id = $("#DistMappingInfoSrId").val();
            var route_id = $("#DistMappingInfoRouteId").val();
            get_db_by_office_id_ae_id_tso_id(db_id);
            get_sr_by_office_id_ae_id_tso_id_db_id(sr_id);
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id(route_id);
        }

        if ($("#DistMappingInfoDbId").val()) {
            var sr_id = $("#DistMappingInfoSrId").val();
            var route_id = $("#DistMappingInfoRouteId").val();
            get_sr_by_office_id_ae_id_tso_id_db_id(sr_id);
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id(route_id);
        }

        if ($("#DistMappingInfoSrId").val()) {
            var route_id = $("#DistMappingInfoRouteId").val();
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id(route_id);
        }

        $("body").on('change', '#DistMappingInfoOfficeId', function() {
            $("#DistMappingInfoAeId").val('');
            $("#DistMappingInfoTsoId").val('');
            $("#DistMappingInfoDbId").val('');
            $("#DistMappingInfoSrId").val('');
            $("#DistMappingInfoRouteId").val('');
            get_ae_by_office_id();
            get_tso_by_office_id_ae_id();
            get_db_by_office_id_ae_id_tso_id();
            get_sr_by_office_id_ae_id_tso_id_db_id();
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id();
            hide_list_item();
        });
        $("body").on('change', '#DistMappingInfoAeId', function() {
            $("#DistMappingInfoTsoId").val('');
            $("#DistMappingInfoDbId").val('');
            $("#DistMappingInfoSrId").val('');
            $("#DistMappingInfoRouteId").val('');
            get_tso_by_office_id_ae_id();
            get_db_by_office_id_ae_id_tso_id();
            get_sr_by_office_id_ae_id_tso_id_db_id();
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id();
            hide_list_item();
        });
        $("body").on('change', '#DistMappingInfoTsoId', function() {
            $("#DistMappingInfoDbId").val('');
            $("#DistMappingInfoSrId").val('');
            $("#DistMappingInfoRouteId").val('');
            get_db_by_office_id_ae_id_tso_id();
            get_sr_by_office_id_ae_id_tso_id_db_id();
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id();
            hide_list_item();

        });
        $("body").on('change', '#DistMappingInfoDbId', function() {
            ;
            $("#DistMappingInfoSrId").val('');
            $("#DistMappingInfoRouteId").val('');
            get_sr_by_office_id_ae_id_tso_id_db_id();
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id();
            hide_list_item();
        });
        $("body").on('change', '#DistMappingInfoSrId', function() {
            $("#DistMappingInfoRouteId").val('');
            get_route_by_office_id_ae_id_tso_id_db_id_sr_id();
            hide_list_item();
        });
        $("body").on('change', '#DistMappingInfoRouteId', function() {
            hide_list_item();
        });

        function get_ae_by_office_id(prev_selected = 0) {
            var office_id = $("#DistMappingInfoOfficeId").val();
            $.ajax({
                'url': '<?php echo BASE_URL; ?>DistMappingInfo/get_ae_by_office_id',
                'type': 'POST',
                'data': {
                    'office_id': office_id
                },
                'success': function(data) {
                    if ($("#DistMappingInfoAeId").html(data)) {
                        if (prev_selected) {
                            $("#DistMappingInfoAeId").val(prev_selected);
                        }
                    }
                }
            });
            return true;
        }

        function get_tso_by_office_id_ae_id(prev_selected = 0) {
            var office_id = $("#DistMappingInfoOfficeId").val();
            var ae_id = $("#DistMappingInfoAeId").val();
            $.ajax({
                'url': '<?php echo BASE_URL; ?>DistMappingInfo/get_tso_by_office_id_ae_id',
                'type': 'POST',
                'data': {
                    'office_id': office_id,
                    'ae_id': ae_id
                },
                'success': function(data) {
                    if ($("#DistMappingInfoTsoId").html(data)) {
                        if (prev_selected) {
                            $("#DistMappingInfoTsoId").val(prev_selected);
                        }
                    }
                }
            });
        }

        function get_db_by_office_id_ae_id_tso_id(prev_selected = 0) {
            var office_id = $("#DistMappingInfoOfficeId").val();
            var ae_id = $("#DistMappingInfoAeId").val();
            var tso_id = $("#DistMappingInfoTsoId").val();
            $.ajax({
                'url': '<?php echo BASE_URL; ?>DistMappingInfo/get_db_by_office_id_ae_id_tso_id',
                'type': 'POST',
                'data': {
                    'office_id': office_id,
                    'ae_id': ae_id,
                    'tso_id': tso_id
                },
                'success': function(data) {
                    if ($("#DistMappingInfoDbId").html(data)) {
                        if (prev_selected) {
                            $("#DistMappingInfoDbId").val(prev_selected);
                        }
                    }
                }
            });
        }

        function get_sr_by_office_id_ae_id_tso_id_db_id(prev_selected = 0) {
            var office_id = $("#DistMappingInfoOfficeId").val();
            var ae_id = $("#DistMappingInfoAeId").val();
            var tso_id = $("#DistMappingInfoTsoId").val();
            var db_id = $("#DistMappingInfoDbId").val();
            $.ajax({
                'url': '<?php echo BASE_URL; ?>DistMappingInfo/get_sr_by_office_id_ae_id_tso_id_db_id',
                'type': 'POST',
                'data': {
                    'office_id': office_id,
                    'ae_id': ae_id,
                    'tso_id': tso_id,
                    'db_id': db_id
                },
                'success': function(data) {
                    if ($("#DistMappingInfoSrId").html(data)) {
                        if (prev_selected) {
                            $("#DistMappingInfoSrId").val(prev_selected);
                        }
                    }
                }
            });
        }

        function get_route_by_office_id_ae_id_tso_id_db_id_sr_id(prev_selected = 0) {
            var office_id = $("#DistMappingInfoOfficeId").val();
            var ae_id = $("#DistMappingInfoAeId").val();
            var tso_id = $("#DistMappingInfoTsoId").val();
            var db_id = $("#DistMappingInfoDbId").val();
            var sr_id = $("#DistMappingInfoSrId").val();
            $.ajax({
                'url': '<?php echo BASE_URL; ?>DistMappingInfo/get_route_by_office_id_ae_id_tso_id_db_id_sr_id',
                'type': 'POST',
                'data': {
                    'office_id': office_id,
                    'ae_id': ae_id,
                    'tso_id': tso_id,
                    'db_id': db_id,
                    'sr_id': sr_id
                },
                'success': function(data) {
                    if ($("#DistMappingInfoRouteId").html(data)) {
                        if (prev_selected) {
                            $("#DistMappingInfoRouteId").val(prev_selected);
                        }
                    }
                }
            });
        }

        function hide_list_item() {
            /*----------------------- Route part ----------------*/
            if ($("#DistMappingInfoRouteId").val()) {
                $(".data_box input[type='radio'][value='ae']").hide();
                $(".data_box input[type='radio'][value='ae']").next().hide();

                $(".data_box input[type='radio'][value='tso']").hide();
                $(".data_box input[type='radio'][value='tso']").next().hide();

                $(".data_box input[type='radio'][value='db']").hide();
                $(".data_box input[type='radio'][value='db']").next().hide();

                $(".data_box input[type='radio'][value='sr']").hide();
                $(".data_box input[type='radio'][value='sr']").next().hide();
                if (
                    $(".data_box input[type='radio'][value='ae']").prop('checked') == true ||
                    $(".data_box input[type='radio'][value='tso']").prop('checked') == true ||
                    $(".data_box input[type='radio'][value='db']").prop('checked') == true ||
                    $(".data_box input[type='radio'][value='sr']").prop('checked') == true
                ) {
                    $(".data_box input[type='radio'][value='route']").prop('checked', true);

                    $(".data_box input[type='radio'][value='ae']").prop('checked') == false;
                    $(".data_box input[type='radio'][value='tso']").prop('checked') == false;
                    $(".data_box input[type='radio'][value='db']").prop('checked') == false;
                    $(".data_box input[type='radio'][value='sr']").prop('checked') == false;
                }
                return 0;
            } else {
                $(".data_box input[type='radio'][value='ae']").show();
                $(".data_box input[type='radio'][value='ae']").next().show();

                $(".data_box input[type='radio'][value='tso']").show();
                $(".data_box input[type='radio'][value='tso']").next().show();

                $(".data_box input[type='radio'][value='db']").show();
                $(".data_box input[type='radio'][value='db']").next().show();

                $(".data_box input[type='radio'][value='sr']").show();
                $(".data_box input[type='radio'][value='sr']").next().show();
            }
            /*------ ----------sr part ------------------*/
            if ($("#DistMappingInfoSrId").val()) {
                $(".data_box input[type='radio'][value='ae']").hide();
                $(".data_box input[type='radio'][value='ae']").next().hide();

                $(".data_box input[type='radio'][value='tso']").hide();
                $(".data_box input[type='radio'][value='tso']").next().hide();

                $(".data_box input[type='radio'][value='db']").hide();
                $(".data_box input[type='radio'][value='db']").next().hide();

                if (
                    $(".data_box input[type='radio'][value='ae']").prop('checked') == true ||
                    $(".data_box input[type='radio'][value='tso']").prop('checked') == true ||
                    $(".data_box input[type='radio'][value='db']").prop('checked') == true
                ) {
                    $(".data_box input[type='radio'][value='route']").prop('checked', true);

                    $(".data_box input[type='radio'][value='ae']").prop('checked') == false;
                    $(".data_box input[type='radio'][value='tso']").prop('checked') == false;
                    $(".data_box input[type='radio'][value='db']").prop('checked') == false;
                }
                return 0;
            } else {
                $(".data_box input[type='radio'][value='ae']").show();
                $(".data_box input[type='radio'][value='ae']").next().show();

                $(".data_box input[type='radio'][value='tso']").show();
                $(".data_box input[type='radio'][value='tso']").next().show();

                $(".data_box input[type='radio'][value='db']").show();
                $(".data_box input[type='radio'][value='db']").next().show();
            }

            /*--------------------- DB Part ---------------------*/
            if ($("#DistMappingInfoDbId").val()) {
                $(".data_box input[type='radio'][value='ae']").hide();
                $(".data_box input[type='radio'][value='ae']").next().hide();

                $(".data_box input[type='radio'][value='tso']").hide();
                $(".data_box input[type='radio'][value='tso']").next().hide();

                if (
                    $(".data_box input[type='radio'][value='ae']").prop('checked') == true ||
                    $(".data_box input[type='radio'][value='tso']").prop('checked') == true
                ) {
                    $(".data_box input[type='radio'][value='route']").prop('checked', true);

                    $(".data_box input[type='radio'][value='ae']").prop('checked') == false;
                    $(".data_box input[type='radio'][value='tso']").prop('checked') == false;
                }
                return 0;
            } else {
                $(".data_box input[type='radio'][value='ae']").show();
                $(".data_box input[type='radio'][value='ae']").next().show();

                $(".data_box input[type='radio'][value='tso']").show();
                $(".data_box input[type='radio'][value='tso']").next().show();
            }
            /*--------------------------- TSO Part ----------------------------------*/
            if ($("#DistMappingInfoTsoId").val()) {
                $(".data_box input[type='radio'][value='ae']").hide();
                $(".data_box input[type='radio'][value='ae']").next().hide();
                if (
                    $(".data_box input[type='radio'][value='ae']").prop('checked') == true
                ) {
                    $(".data_box input[type='radio'][value='route']").prop('checked', true);

                    $(".data_box input[type='radio'][value='ae']").prop('checked') == false;
                }
                // return 0;
            } else {
                $(".data_box input[type='radio'][value='ae']").show();
                $(".data_box input[type='radio'][value='ae']").next().show();
            }

        }
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
            a.download = "Mapping_info_list.xls";
            document.body.appendChild(a);
            a.click();
        });
    });
</script>