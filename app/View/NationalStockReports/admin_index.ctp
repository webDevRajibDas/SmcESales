<style>
	table, th, td {
		/*border: 1px solid black;*/
		border-collapse: collapse;
	}
	#content { display: <?php 
		if (!empty($office_id)) {
			echo 'block';
		}else{
			echo 'none';
		}
	?>; }
	@media print
		{
			#non-printable { display: none; }
			#content { display: block; }
			table, th, td {
		border: 1px solid black;
		border-collapse: collapse;
	}
		}
</style>

<style>
.search .radio label {
width: auto;
float:none;
padding-left:5px;
}
.search .radio legend {
float: left;
margin: 5px 20px 0 0;
text-align: right;
width: 30%;
display: inline-block;
font-weight: 700;
font-size:14px;
border-bottom:none;
}
#market_list .checkbox label{
padding-left:10px;
width:auto;
}
#market_list .checkbox{
width:33%;
float:left;
margin:1px 0;
}
</style>

<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
        
			<div class="box-header">
				<h3 class="box-title"><i class="glyphicon glyphicon-th-large"></i> <?php echo __('National Stock Report'); ?></h3>
                <div class="col-md-12">
                	<iframe width="100%" style="min-height:900px;" src="http://182.160.103.236:8079/app/webroot/migrator/">
                </div>
			</div>	
			
		</div>
	</div>
</div>	
            
			