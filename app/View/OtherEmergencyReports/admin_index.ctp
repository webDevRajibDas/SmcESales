<?php

/* echo $this->params['controller'];
echo $this->App->menu_permission('OtherEmergencyReports', 'crash_trade_program');
exit; */
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary" style="min-height:50px !important;">
            <div class="box-header">
                <h3 class="box-title">Report Name</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <?php
                    if ($this->App->menu_permission($this->params['controller'], 'admin_crash_trade_program')) {
                    ?>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/cash_discount_offer_ors25_np_5_persent_20_may'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">5% Cash Discount on ORSaline-N NP(25pcs) from 20-May-2023</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/cash_discount_offer_joya_4_persent_09_may'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">4% Cash Discount on Joya belt from 09-May-2023</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/cash_discount_offer_joya_13_dec'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">Cash Discount on Joya Belt</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/cash_discount_offer_06_nov'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">10% Cash Discount on Smile belt type baby diaper</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/ors_traders_incentive_offer_june_22_to_july_05_2022'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">(Traders)Tornado offer 2 ORS-N(22 June 2022 TO 05 July 2022)</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/crash_trade_program_22_30june'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">Crash Trade Program(22 June 2022 TO 30 june 2022)</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/crash_trade_program_01_21june'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">Crash Trade Program(01 June 2022 TO 21 june 2022)</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="dashboard_icon">
                                <a href="<?= Router::url('/admin/' . $this->params['controller'] . '/crash_trade_program'); ?>">
                                    <div style="text-align:center;font-size:30px;"><i class="fa fa-bars"></i></div>
                                    <div style="text-align:center;">Crash Trade Program(12 May 2022 TO 31 May 2022)</div>
                                </a>
                            </div>
                        </div>


                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>