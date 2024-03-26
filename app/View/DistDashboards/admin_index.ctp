<?php
echo $this->Html->css("dist/css/AdminLTE.min");
//echo $this->Html->css("dist/css/skins/_all-skins.min");

echo $this->Html->css("bower_components/morris.js/morris");
echo $this->Html->css("bower_components/jvectormap/jquery-jvectormap");
echo $this->Html->css("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min");
echo $this->Html->css("bower_components/bootstrap-daterangepicker/daterangepicker");
echo $this->Html->css("plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min");

echo $this->Html->css("bower_components/Ionicons/css/ionicons.min");
echo $this->Html->css("bower_components/jvectormap/jquery-jvectormap");

?>
<style type="text/css">
	html {
		min-height: 100% !important;
	}

	body {
		min-height: 100% !important;
	}

	body.fixed .wrapper {
		min-height: 100% !important;
		overflow: initial !important;
	}

	.dms_dashboard h1 {
		font-weight: 800;
		color: #11596c;
		font-size: 30px;
		margin-bottom: 20px;
		margin-left: 10px;
	}

	.dms_dashboard_list_title {
		float: left;
	}

	.office_dropdown {
		float: right;
		width: auto;
		margin-top: 20px;
	}

	.office_dropdown .form-control {
		width: 60%;
	}

	.office_dropdown p {
		float: left;
		margin-right: 20px;
	}

	.area_office {
		margin-right: 20px;
		float: left;
	}

	.regional_office {

		float: right;
	}

	.fixed .content-wrapper,
	.fixed .right-side {
		padding-top: 0 !important;
	}

	.dms_dashboard_list_single {
		background: #ffffff;
		border-radius: 10px;
		padding: 30px;
		border: 1px solid #eeeeee;
		margin-bottom: 30px;
	}

	.dms_dashboard_list_single h1 {
		margin-top: 0;
		font-size: 40px;
		margin-left: 0px;
	}

	.dms_dashboard_list {
		position: relative;
	}

	.dms_dashboard_sales_activity {
		position: relative;
	}

	.dms_dashboard_list_single img {
		position: absolute;
		right: 50px;
		top: 27px;
		height: 100px;
	}

	.dms_dashboard_list_single h3 {
		margin-top: 0;
		font-size: 26px;
		color: #7f9fa7;
		font-weight: 600;
		margin-bottom: 0px;
	}

	.nav-tabs .nav-link {
		border: 1px solid #f0f2f4;
		background: #ffffff;
	}

	.nav-tabs {
		border-bottom: none;
	}

	.nav-tabs>li a {
		font-size: 20px;
		color: #7f9fa7;
		margin-right: -5px;
		border-radius: 0;
		transition: .3s;
	}

	.today_nav {
		border-radius: 10px 0 0 0 !important;
	}

	.year_nav {
		border-radius: 0 10px 0 0 !important;
	}

	.nav-tabs>li.active>a,
	.nav-tabs>li.active>a:hover,
	.nav-tabs>li.active>a:focus {
		background: #4043a0;
		color: #ffffff;
		transition: .3s;
	}

	.tab-content {
		background: #ffffff;
		padding: 0px 30px 30px 30px;
		border-radius: 10px 0 10px 10px;
		border: 1px solid #f0f2f4;
		margin-bottom: 50px;
	}

	.nav-tabs-right {
		text-align: right;
		margin-right: 5px;
		margin-top: -50px;
	}

	.nav-tabs-right>li {
		display: inline-block;
		float: none;
	}

	.dms_dashboard_sales_activity .dms_dashboard_list_single {
		background: #f6f6f6;
		margin-bottom: 0 !important;
		margin-top: 30px !important;
		position: relative;
	}

	.dms_dashboard_sales_activity .dms_dashboard_list_single h1 span {
		font-size: 30px;
	}

	.revenue_font_size {
		font-weight: 800;
		color: #11596c;
		font-size: 40px !important;
	}

	.sync_table {
		background: #ffffff;
		padding: 30px;
		border-radius: 10px;
		border: 1px solid #f0f2f4;
	}

	.sync_table thead {
		background: #e9ecf1;
		font-size: 20px;
		color: #11596c;
	}

	.sync_table td {
		font-size: 18px;
		color: #11596c;
		padding: 10px 10px 10px 20px !important;
	}

	.sync_table th {
		font-size: 20px;
		color: #11596c;
		padding: 10px 10px 10px 20px !important;
		border: none;
	}

	.sync_table tr:first-child th:first-child {
		border-top-left-radius: 5px;
	}

	.sync_table tr:first-child th:last-child {
		border-top-right-radius: 5px;
	}

	.sync_table tr:last-child th:first-child {
		border-bottom-left-radius: 5px;
	}

	.sync_table tr:last-child th:last-child {
		border-bottom-right-radius: 5px;
	}

	.sync_table thead tr th {
		border-bottom: none;
	}

	.sync_table thead tr td:first-child {
		border: none;
	}

	@media screen and (max-width: 992px) {
		.office_dropdown {
			width: 100%;
			margin-top: 20px;
			margin-bottom: 20px;
			clear: both;
		}

		.regional_office {
			float: none;
		}

		.office_dropdown .form-control {
			width: 100%;
		}

		.area_office {
			margin: 0;
			float: none;
		}

		.dms_dashboard_list_single img {
			height: 90px;
		}

		.dms_dashboard_sales_activity .dms_dashboard_list_single img {
			position: absolute;
			right: 20px;
			top: 40px;
			height: 70px;
		}

		.nav-tabs-right {
			margin-top: 0;
		}

		.nav-tabs>li a {
			font-size: 16px;
			padding: 10px;
		}
	}

	@media screen and (min-width: 1280px) {
		.dms_dashboard_sales_activity .col-lg-3 {
			width: 50%;
		}
	}

	@media screen and (min-width: 1920px) {

		.dms_dashboard_sales_activity .col-lg-3 {
			width: 25%;
		}
	}

	/*---- Loader icon ------------------ : Start ------ */
	.loader {
		background-color: #666;
		height: 100%;
		width: 100%;
		z-index: 100;
		position: absolute;
		top: 0;
		bottom: 0;
		opacity: 0.4;
	}

	.loader_icon {
		position: absolute;
		top: 46%;
		left: 46%;
		z-index: 101;
	}

	/*---- Loader icon ------------------ : Start ------ */
</style>
<div class="dms_dashboard">
	<div class="dms_dashboard_list">
		<div class="loader top_box_loader hide">
			<img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
		</div>
		<div class="dms_dashboard_list_title">
			<h1>DMS Dashboard</h1>
		</div>
		<div class="office_dropdown">

			<div class="area_office" style="display:<?php if (!$region) {
														echo 'block';
													} else {
														echo 'none';
													} ?>;">
				<p>Regional Office</p>
				<?php echo $this->Form->input('region_office_id', array('id' => 'region_office_id', 'class' => 'region_office_id', 'required' => true, 'empty' => '---- Head Office ----', 'options' => $region_offices, 'default' => $region, 'label' => false, 'div' => false)); ?>
				<div style="clear: both;"></div>
			</div>
			<div class="area_office" style="display:<?php if (!$office_id) {
														echo 'block';
													} else {
														echo 'none';
													} ?>;">
				<p>Area Office</p>
				<?php echo $this->Form->input('office_id', array('id' => 'office_id', 'class' => 'office_id', 'options' => $offices, 'label' => 'Area Office', 'required' => true, 'empty' => '---- All ----', 'default' => $office_id, 'label' => false, 'div' => false)); ?>
			</div>
			<div class="area_office">
				<p>TSO</p>
				<?php echo $this->Form->input('tsos', array('id' => 'tso', 'class' => 'tso', 'options' => '', 'label' => false, 'div' => false, 'required' => true, 'empty' => '---- All ----')); ?>
				<div style="clear: both;"></div>
			</div>
			<div class="area_office">
				<p>Sources</p>
				<?php echo $this->Form->input('source', array('id' => 'source', 'class' => 'source', 'options' => $sources, 'label' => false, 'div' => false, 'required' => true, 'empty' => '---- All ----')); ?>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div style="clear: both;"></div>
		<div class="row">
			<div class="col-sm-4 col-md-4">
				<div class="dms_dashboard_list_single">
					<h1 class="total_db">0</h1>
					<h3>DB</h3>
					<!-- <img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/1.png' ?>"> -->
				</div>
			</div>
			<div class="col-sm-4 col-md-4">
				<div class="dms_dashboard_list_single">
					<h1 class="total_tso">0</h1>
					<h3>TSO</h3>
				<!--	<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/2.png' ?>"> -->
				</div>
			</div>
			<div class="col-sm-4 col-md-4">
				<div class="dms_dashboard_list_single">
					<h1 class="total_sr">0</h1>
					<a href="<?= BASE_URL . 'admin/DistSrLoginReports' ?>" target="_blank">
						<h1 class="total_present_sr">0</h1>
					</a>
					<h3>SR</h3>
				<!--	<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/3.png' ?>"> -->
				</div>
			</div>
		</div>
	</div>
	<div class="dms_dashboard_sales_activity">
		<div class="loader sales_activity_loader hide">
			<img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
		</div>
		<h1>Sales Activity</h1>
		<ul class="nav nav-tabs-right nav-tabs " role="tablist">
			<li class="nav-item active">
				<a class="nav-link today_nav" data-toggle="tab" href="#tabs-1" role="tab">Today</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">This Week</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">This Month</a>
			</li>
			<li class="nav-item">
				<a class="nav-link year_nav" data-toggle="tab" href="#tabs-4" role="tab">This Year</a>
			</li>
		</ul><!-- Tab panes -->
		<div class="tab-content">
			<div class="tab-pane active" id="tabs-1" role="tabpanel">
				<div class="row">
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1><span class="today_revenue revenue_font_size">0</span> <span>Tk</span></h1>
							<h3>Sales Revenue</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/4.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="today_order">0</h1>
							<h3>Order</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/5.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="today_invoice">0</h1>
							<h3>Invoice</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/6.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="today_delivery">0</h1>
							<h3>Delivery</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/7.png' ?>">
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tabs-2" role="tabpanel">
				<div class="row">
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1><span class="week_revenue revenue_font_size">0</span> <span>Tk</span></h1>
							<h3>Sales Revenue</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/4.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="week_order">0</h1>
							<h3>Order</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/5.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="week_invoice">0</h1>
							<h3>Invoice</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/6.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="week_delivery">0</h1>
							<h3>Delivery</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/7.png' ?>">
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tabs-3" role="tabpanel">
				<div class="row">
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1><span class="month_revenue revenue_font_size">0</span> <span>Tk</span></h1>
							<h3>Sales Revenue</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/4.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="month_order">0</h1>
							<h3>Order</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/5.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="month_invoice">0</h1>
							<h3>Invoice</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/6.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="month_delivery">0</h1>
							<h3>Delivery</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/7.png' ?>">
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="tabs-4" role="tabpanel">
				<div class="row">
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1><span class="year_revenue revenue_font_size">0</span> <span>Tk</span></h1>
							<h3>Sales Revenue</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/4.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="year_order">0</h1>
							<h3>Order</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/5.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="year_invoice">0</h1>
							<h3>Invoice</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/6.png' ?>">
						</div>
					</div>
					<div class="col-sm-6 col-md-6 col-lg-3">
						<div class="dms_dashboard_list_single">
							<h1 class="year_delivery">0</h1>
							<h3>Delivery</h3>
							<img src="<?= BASE_URL . 'theme/CakeAdminLTE/img/dist_dash/7.png' ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="dms_dashboard_sales_activity">
		<div class="loader user_sync_status hide">
			<img src="<?php echo $this->webroot; ?>theme/CakeAdminLTE/img/ajax-loader1.gif" alt="" class="loader_icon">
		</div>
		<h1>
			Sync Status
			<div class="pull-right csv_btn">
				<?= $this->Html->link(__('Download XLS'), array('action' => ''), array('class' => 'btn btn-primary', 'escape' => false, 'id' => 'download_xl')); ?>
			</div>
		</h1>

		<div class="sync_table table-responsive" id="sr_list">

			<table class="table">
				<thead>
					<tr>
						<th scope="col">Area Office</th>
						<th scope="col">DB</th>
						<th scope="col">SR</th>
						<th scope="col">Sync Time</th>
						<th scope="col">Total Order</th>
						<th scope="col">Order Value</th>
					</tr>
				</thead>
				<tbody class="user_sync_data">

				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {

		$('#office_id').on('change', function() {
			$('#tso').val('');
			var office_id = $(this).val();
			if (office_id) {
				$.ajax({
					url: "<?= BASE_URL . 'dist_dashboards/get_tso_list'; ?>",
					type: 'post',
					data: {
						'office_id': office_id
					},
					success: function(data) {
						if ($("#tso").html(data)) {
							get_total_db_tso_sr();
							get_sales_activity();
							get_user_sync_data();
						}
					}
				});
			} else {
				var data = '<option value="">--- Select ---</option>';
				$("#tso").html(data)
			}

		});
		if ($('#office_id').val()) {
			$('.office_id').trigger('change');
		} else {
			get_total_db_tso_sr();
			get_sales_activity();
			get_user_sync_data();
		}
		$("#region_office_id").change(function() {
			$('#office_id').val('');
			$('#tso').val('');
			var data = '<option value="">--- Select ---</option>';
			$("#tso").html(data)
			get_total_db_tso_sr();
			get_sales_activity();
			get_user_sync_data();
		});
		$("#source").change(function() {
			get_total_db_tso_sr();
			get_sales_activity();
			get_user_sync_data();
		});
		$("#tso").change(function() {
			get_total_db_tso_sr();
			get_sales_activity();
			get_user_sync_data();
		});

		$('#region_office_id').selectChain({
			target: $('#office_id'),
			value: 'name',
			url: '<?= BASE_URL . 'esales_reports/get_office_list'; ?>',
			type: 'post',
			data: {
				'region_office_id': 'region_office_id'
			}
		});

		function get_total_db_tso_sr() {
			var office_id = $("#office_id option:selected").val() ? $("#office_id option:selected").val() : 0;

			var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

			var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;
			var tso = $("#tso option:selected").val() ? $("#tso option:selected").val() : 0;
			var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source + '&tso=' + tso;
			$.ajax({
				url: '<?= BASE_URL . 'dist_dashboards/get_total_db_tso_sr' ?>',
				type: "POST",
				data: dataString,
				beforeSend: function() {
					$(".top_box_loader").removeClass('hide');
					$(".top_box_loader").addClass('show');
				},
				success: function(data) {
					var response = $.parseJSON(data);
					$(".total_db").text(response.dist);
					$(".total_tso").text(response.tso);
					$(".total_sr").text('Total : ' + response.sr);
					$(".total_present_sr").text('Present : ' + response.present_sr);
					$(".top_box_loader").removeClass('show');
					$(".top_box_loader").addClass('hide');
				}
			});
		}

		function get_sales_activity() {
			var office_id = $("#office_id option:selected").val() ? $("#office_id option:selected").val() : 0;

			var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

			var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;
			var tso = $("#tso option:selected").val() ? $("#tso option:selected").val() : 0;
			var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source + '&tso=' + tso;
			$.ajax({
				url: '<?= BASE_URL . 'dist_dashboards/get_sales_activity' ?>',
				type: "POST",
				data: dataString,
				beforeSend: function() {
					$(".sales_activity_loader").removeClass('hide');
					$(".sales_activity_loader").addClass('show');
				},
				success: function(data) {
					var response = $.parseJSON(data);

					$(".today_revenue").text(response.today_revenue);
					$(".today_order").text(response.today_order);
					$(".today_invoice").text(response.today_invoice);
					$(".today_delivery").text(response.today_delivery);

					$(".week_revenue").text(response.week_revenue);
					$(".week_order").text(response.week_order);
					$(".week_invoice").text(response.week_invoice);
					$(".week_delivery").text(response.week_delivery);

					$(".month_revenue").text(response.month_revenue);
					$(".month_order").text(response.month_order);
					$(".month_invoice").text(response.month_invoice);
					$(".month_delivery").text(response.month_delivery);

					$(".year_revenue").text(response.year_revenue);
					$(".year_order").text(response.year_order);
					$(".year_invoice").text(response.year_invoice);
					$(".year_delivery").text(response.year_delivery);

					$(".sales_activity_loader").removeClass('show');
					$(".sales_activity_loader").addClass('hide');
				}
			});
		}

		function get_user_sync_data() {
			var office_id = $("#office_id option:selected").val() ? $("#office_id option:selected").val() : 0;

			var region_office_id = $("#region_office_id option:selected").val() ? $("#region_office_id option:selected").val() : 0;

			var source = $("#source option:selected").val() ? $("#source option:selected").val() : 0;
			// var dataString = 'office_id='+ office_id + '&region_office_id=' + region_office_id + '&source=' + source;
			var tso = $("#tso option:selected").val() ? $("#tso option:selected").val() : 0;
			var dataString = 'office_id=' + office_id + '&region_office_id=' + region_office_id + '&source=' + source + '&tso=' + tso;

			$.ajax({
				url: '<?= BASE_URL . 'dist_dashboards/userSyncData' ?>',
				type: "POST",
				data: dataString,
				beforeSend: function() {
					$(".user_sync_status").removeClass('hide');
					$(".user_sync_status").addClass('show');
				},
				success: function(data) {
					$(".user_sync_data").html(data);
					$(".user_sync_status").removeClass('show');
					$(".user_sync_status").addClass('hide');
				}
			});
		}

		$("#download_xl").click(function(e) {
			e.preventDefault();
			var html = $("#sr_list").html();
			var blob = new Blob([html], {
				type: 'data:application/vnd.ms-excel'
			});
			var downloadUrl = URL.createObjectURL(blob);
			var a = document.createElement("a");
			a.href = downloadUrl;
			a.download = "user_sync.xls";
			document.body.appendChild(a);
			a.click();
		});
	});
</script>