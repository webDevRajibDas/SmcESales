<style>
    .search .radio label {
        width: auto;
        float: none;
        padding: 0px 5% 0px 5px;
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

    #market_list .checkbox label {
        padding-left: 0px;
        width: auto;
    }

    #market_list .checkbox {
        width: 33%;
        float: left;
        margin: 1px 0;
    }
    body .td_rank_list .checkbox {
        width: auto !important;
        padding-left: 20px !important;
    }

    .radio input[type='radio'],
    .radio-inline input[type='radio'] {
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
        width: 15% !important;
    }
    .market_list3 .checkbox {
        width: 20% !important;
    }

    .box_area {
        display: none;
    }
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        color: #c7c7c7;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class='box-header'>
                <h3 class='box-title'><i class='glyphicon glyphicon-th-large'></i> <?= $page_title ?></h3>
            </div>

            <div class="box-body">
                <div class='search-box'>
                    <?php echo $this->Form->create('InventoryStatementReports', array('role' => 'form', 'action' => 'index')); ?>
                    <table class="search">

                        <tr>
                            <td class="required" width="50%">
                                <?php echo $this->Form->input('date_from', array('class' => 'form-control datepicker date_from', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?>
                            </td>

                            <td class="required"
                                width="50%"><?php echo $this->Form->input('date_to', array('class' => 'form-control date_to', 'required' => true, 'autocomplete' => 'off', 'readonly' => true)); ?></td>
                        </tr>

                        <?php if ($office_parent_id == 0) { ?>
                            <tr>
                                <td class="required"
                                    width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'empty' => '---- Head Office ----', 'options' => $region_offices)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <?php if ($office_parent_id == 14) { ?>
                            <tr>
                                <td width="50%"><?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'form-control region_office_id', 'required' => false, 'options' => $region_offices)); ?></td>
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
                                <td width="50%"><?php echo $this->Form->input('office_id', array('label' => 'Area Office :', 'id' => 'office_id', 'class' => 'form-control office_id', 'required' => false)); ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>


                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('type', array('legend' => 'Type :', 'class' => 'type', 'type' => 'radio', 'default' => 'territory', 'onClick' => 'typeChange(this.value)', 'options' => $types, 'required' => true)); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div id="territory_html">
                                    <?php echo $this->Form->input('territory_id', array('id' => 'territory_id', 'class' => 'form-control territory_id office_t_so', 'required' => false, 'empty' => '---- All ----')); ?>
                                </div>

                                <div id="so_html">
                                    <?php echo $this->Form->input('so_id', array('label' => 'Sales Officers :', 'id' => 'so_id', 'class' => 'form-control so_id', 'required' => false, 'options' => $so_list, 'empty' => '---- All ----')); ?>
                                </div>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <label style="float:left; width:15%;">Product Type :</label>
                                <div id="market_list" class="input select"
                                     style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="product_type_id"
                                               class="checkall"/>
                                        <label for="product_type_id" style="float:none; width:auto;  cursor:pointer;">Select
                                            / Unselect All</label>
                                    </div>
                                    <div class="selection">
                                        <?php echo $this->Form->input('product_type_id', array('id' => 'product_type_id', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $product_types)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <label for="source" style="float:left; width:15%;">Product Source :</label>
                                <div id="market_list" class="input select"
                                     style="float:left; width:80%; padding-left:20px;">
                                    <div style="margin:auto; width:90%; float:left; margin-left:-20px;">
                                        <input style="margin:0px 5px 0px 0px;" type="checkbox" id="source"
                                               class="checkall2"/>
                                        <label for="source" style="float:none; width:auto;  cursor:pointer;">Select /
                                            Unselect All</label>
                                    </div>
                                    <div class="selection2">
                                        <?php echo $this->Form->input('source', array('id' => 'source', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $product_sources)); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('unit_type', array('legend' => 'Unit Type :', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $unit_types, 'required' => true)); ?>
                            </td>

                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="source" style="float:left; width:15%;"> Day Wise Closing : </label>
                                <div id="market_list" class="input select"
                                     style="float:left; width:80%; padding-left:20px;">

                                    <div class="selection">
                                        <?php echo $this->Form->input('day_wise_closing', array('id' => 'day_wise_closing', 'label' => false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => array('day_wise_closing' => ''))); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php echo $this->Form->input('report_type', array('legend' => ' ', 'class' => 'unit_type', 'type' => 'radio', 'default' => '1', 'options' => $report_type, 'required' => true)); ?>
                            </td>
                        </tr>

                        <tr align="center">
                            <td colspan="2">
                                <?php echo $this->Form->submit('Submit', array('type' => 'submit', 'class' => 'btn btn-large btn-primary', 'escape' => false, 'div' => false)); ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-refresh"></i> Reset'), array('action' => ''), array('class' => 'btn btn-warning', 'escape' => false)); ?>
                            </td>
                        </tr>

                    </table>

                    <?php echo $this->Form->end(); ?>
                </div>
            </div>

        </div>
    </div>
</div>


<script>

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
            $('.office_t_so option:nth-child(1)').prop('selected', true).change();
        } else if (type == 'territory') {
            $('#so_id option:nth-child(1)').prop('selected', true).change();
        } else {
            <?php if (!@$request_data['DcrReports']['territory_id']) { ?>
            $('.office_t_so option:nth-child(1)').prop("selected", true).change();
            <?php } ?>

            <?php if (!@$request_data['DcrReports']['so_id']) { ?>
            $('#so_id option:nth-child(1)').prop("selected", true).change();
            <?php } ?>
        }
    }
    $(document).ready(function() {
        $('.region_office_id').selectChain({
            target: $('.office_id'),
            value: 'name',
            url: '<?= BASE_URL . 'market_characteristic_reports/get_office_list'; ?>',
            type: 'post',
            data: {
                'region_office_id': 'region_office_id'
            }
        });

        typeChange();

        //var date = new Date();
        var yesterday = new Date(new Date().setDate(new Date().getDate() - 1));

        $('.date_to').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            endDate: yesterday
        });


        $("input[type='checkbox']").iCheck('destroy');
        $("input[type='radio']").iCheck('destroy');

        $('.checkall').click(function(e) {
            var checked = $(this).prop('checked');
            $(this).closest('.select').find('.selection').find('input:checkbox').prop('checked', checked);
        });

        $('.checkall2').click(function(e) {
            var checked = $(this).prop('checked');
            $(this).closest('.select').find('.selection2').find('input:checkbox').prop('checked', checked);
        })


        $('#office_id').change(function () {
            //alert($(this).val());
            date_from = $('.date_from').val();
            date_to = $('.date_to').val();
            if (date_from && date_to) {
                $.ajax({
                    type: 'POST',
                    url: '<?= BASE_URL ?>market_characteristic_reports/get_office_so_list',
                    data: 'office_id=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to,
                    cache: false,
                    success: function (response) {
                        //alert(response);
                        $('#so_id').html(response);
                    }
                });
            } else {
                $('#office_id option:nth-child(1)').prop('selected', true);
                alert('Please select date range!');
            }
        });


        remove_zero_value_column();

        function remove_zero_value_column() {
            var column_sum_in = [];
            $('#sum_table_in tr:not(:first) td').each(function (i, value) {
                var value_txt = parseFloat($(this).text());
                if (isNaN(value_txt)) {
                    value_txt = 0
                }
                var column_index = $(this).index();
                column_sum_in[column_index] = column_sum_in[column_index] ? column_sum_in[column_index] + value_txt : 0 + value_txt
            });
            $.each(column_sum_in, function (index, value) {
                if (value == 0) {
                    $('#sum_table_in th:not(:first-child,:last-child):nth-child(' + (index + 1) + ')').addClass('remove_column');
                    $('#sum_table_in td:not(:first-child,:last-child):nth-child(' + (index + 1) + ')').addClass('remove_column');
                }
            });


            var column_sum_out = [];
            $('#sum_table_out tr:not(:first) td').each(function (i, value) {
                var value_txt = parseFloat($(this).text());
                if (isNaN(value_txt)) {
                    value_txt = 0
                }
                var column_index = $(this).index();
                column_sum_out[column_index] = column_sum_out[column_index] ? column_sum_out[column_index] + value_txt : 0 + value_txt
            });
            $.each(column_sum_out, function (index, value) {
                if (value == 0) {
                    $('#sum_table_out th:not(:first-child,:last-child):nth-child(' + (index + 1) + ')').addClass('remove_column');
                    $('#sum_table_out td:not(:first-child,:last-child):nth-child(' + (index + 1) + ')').addClass('remove_column');
                }
            });

            $('.remove_column').remove();

        }

    });
</script>