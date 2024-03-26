<style type="text/css">
	.border,
	.border td {
		border: 1px solid black;
		white-space: nowrap;
	}
</style>
<table id="DistGiftItem" class="table table-bordered" border="1">
	<thead>
		<tr>
			<th width="60" class="text-center">Serial</th>
			<th class="text-center">Distributor</th>
			<th class="text-center">SR</th>
			<th class="text-center">Route</th>
			<th class="text-center">Market</th>
			<th class="text-center">Outlet</th>
			<th class="text-center">Memo no</th>
			<th class="text-center">Remarks</th>
			<th class="text-center">Date</th>
			<th class="text-center">Product</th>
			<th class="text-center">Quantity</th>
		</tr>
	</thead>

	<tbody>
		<?php $serial = 1;
		foreach ($dist_gift_items as $item) : ?>
			<tr>
				<td class="text-center"><?php /*echo h($item['GiftItem']['id']);*/ echo $serial++; ?></td>
				<td class="text-center"><?php echo h($item['DistGiftItem']['distributor']); ?></td>
				<td class="text-center"><?php echo h($item['DistSalesRepresentative']['name']); ?></td>
				<td class="text-center"><?php echo h($item['DistGiftItem']['route']); ?></td>
				<td class="text-center"><?php echo h($item['DistGiftItem']['market']); ?></td>
				<td class="text-center"><?php echo h($item['DistOutlet']['name']); ?></td>
				<td class="text-center"><?php echo h($item['DistGiftItem']['memo_no']); ?></td>
				<td class="text-center"><?php echo h($item['DistGiftItem']['remarks']); ?></td>
				<td class="text-center"><?php echo $this->App->dateformat($item['DistGiftItem']['date']); ?></td>
				<td class="text-center"><?php echo h($item['DistGiftItem']['product']); ?></td>
				<td class="text-center"><?php echo h($item['DistGiftItem']['quantity']); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>