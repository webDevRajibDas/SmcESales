<style type="text/css">
	.border,.border td {
		border: 1px solid black;
		white-space: nowrap;
	}
</style>
<table id="GiftItem" class="table border" border="1">
					<thead>
						<tr>
							<th width="60" class="text-center"><?php //echo h('id'); ?>Serial</th>
							<th class="text-center"><?php echo h('Territory'); ?></th>
							<th class="text-center"><?php echo h('SO Name'); ?></th>
							<th class="text-center"><?php echo h('Thana'); ?></th>
							<th class="text-center"><?php echo h('Market'); ?></th>
							<th class="text-center"><?php echo h('Outlet'); ?></th>
							<th class="text-center"><?php echo h('Memo No'); ?></th>
							<th class="text-center"><?php echo h('Date'); ?></th>
							<th class="text-center"><?php echo h('Product'); ?></th>
							<th class="text-center"><?php echo h('Quantity'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php $serial=1;foreach ($gift_items as $item): ?>
					<tr>
						<td class="text-center"><?php /*echo h($item['GiftItem']['id']);*/ echo $serial++;?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['territory']); ?></td>
						<td class="text-center"><?php echo h($item['SalesPerson']['name']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['thana']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['market']); ?></td>
						<td class="text-center"><?php echo h($item['Outlet']['name']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['memo_no']); ?></td>
						<td class="text-center"><?php echo $this->App->dateformat($item['GiftItem']['date']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['product']); ?></td>
						<td class="text-center"><?php echo h($item['GiftItem']['quantity']); ?></td>
					
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>