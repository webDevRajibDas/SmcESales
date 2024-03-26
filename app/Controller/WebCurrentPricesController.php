<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class WebCurrentPricesController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Product');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Current Prices');
		$this->loadModel('ProductCombinationsV2');


		$plist = $this->Product->find('list', array(
			'conditions' => array(
				'Product.product_type_id' => 1
			),
			'order' => array('Product.order' => 'ASC')
		));
		$j = 1;

		//echo '<pre>';print_r($plist);exit;

		foreach ($plist as $key => $pval) {
			$product_id = $key;

			$sqlForSo = "
				select 
					pp.id as price_id
				from  product_prices_v2 pp
				inner join product_price_section_v2 pps on pps.product_price_id=pp.id
				where pps.is_so=1 and pp.product_id=$product_id and pp.effective_date = (
					select max(effective_date) from product_prices_v2 ppv 
					inner join product_price_section_v2 ppsv on ppsv.product_price_id=ppv.id  
					where ppv.product_id=$product_id and ppsv.is_so=1
					)
				order by pp.id desc
			";

			$soQRs = $prices = $this->Product->Query($sqlForSo);

			if (!empty($soQRs)) {
				$soprice[] = array(
					'p_price_id' => $soQRs[0][0]['price_id'],
				);
			}

			$sqlForSr = "
				select 
					pp.id as price_id
				from  product_prices_v2 pp
				inner join product_price_section_v2 pps on pps.product_price_id=pp.id
				where pps.is_sr=1 and pp.product_id=$product_id and pp.effective_date = (
					select max(effective_date) from product_prices_v2 ppv 
					inner join product_price_section_v2 ppsv on ppsv.product_price_id=ppv.id  
					where ppv.product_id=$product_id and ppsv.is_sr=1
					)
					order by pp.id desc
			";

			$srQRs = $prices = $this->Product->Query($sqlForSr);
			if (!empty($srQRs)) {
				$srprice[] = array(
					'p_price_id' => $srQRs[0][0]['price_id'],
				);
			}

			$sqlForDb = "
				select 
					pp.id as price_id
				from  product_prices_v2 pp 
				inner join product_price_section_v2 pps on pps.product_price_id=pp.id
				where pps.is_db=1 and pp.product_id=$product_id and pp.effective_date = (
					select max(effective_date) from product_prices_v2 ppv 
					inner join product_price_section_v2 ppsv on ppsv.product_price_id=ppv.id  
					where ppv.product_id=$product_id and ppsv.is_db=1
					)
					order by pp.id desc
			";

			$dbQRs = $prices = $this->Product->Query($sqlForDb);
			if (!empty($dbQRs)) {
				$dbprice[] = array(
					'p_price_id' => $dbQRs[0][0]['price_id'],
				);
			}
		}

		$sosrprice = array_merge($soprice, $srprice);
		$sosrpriceid = array();
		foreach ($sosrprice as $valpriceid) {
			$sosrpriceid[$valpriceid['p_price_id']] = $valpriceid['p_price_id'];
		}

		$dbpriceidarray = array();
		foreach ($dbprice as $dbpriceval) {
			$dbpriceidarray[$dbpriceval['p_price_id']] = $dbpriceval['p_price_id'];
		}

		$allpriceid = array_merge($sosrpriceid, $dbpriceidarray);

		$unickPriceArray = array();

		foreach ($allpriceid as $unickpriceid) {
			$unickPriceArray[$unickpriceid] = $unickpriceid;
		}

		foreach ($unickPriceArray as $price_id_val) {

			$priceInfo = "
				select 
					ppv.mrp, p.name as product_name
				from  products p
				inner join product_prices_v2 ppv on ppv.product_id=p.id
				where ppv.id=$price_id_val
				
			";

			$rsp = $this->Product->Query($priceInfo);

			$product_price_id = $price_id_val;

			$product_name = $rsp[0][0]['product_name'];
			$mrp = $rsp[0][0]['mrp'];

			$productPriceList = $this->ProductCombinationsV2->find('all', array(
				'conditions' => array(
					'ProductCombinationsV2.product_price_id' => $product_price_id
				),
				'fields' => array(
					'ProductCombinationsV2.id',
					'ProductCombinationsV2.min_qty',
					'ProductCombinationsV2.price',
					'ProductCombinationsV2.sr_price',
					'ProductCombinationsV2.product_id',
					'ProductCombinationsV2.section_id',
					'ProductCombinationsV2.product_price_id',
				),
				'recursive' => -1
			));



			foreach ($productPriceList as $priceVal) {

				$p_com_id = $priceVal['ProductCombinationsV2']['id'];

				$checkdbsosr  =  $this->check_sor_sr_selected($priceVal['ProductCombinationsV2']['section_id']);

				if ($checkdbsosr == 0) {
					$srpirce = $priceVal['ProductCombinationsV2']['price'];
				} else {
					$srpirce = 0;
				}

				$rs[$j]['id'] = $p_com_id;
				$rs[$j]['price_id'] = $product_price_id;
				$rs[$j]['product_id'] = $priceVal['ProductCombinationsV2']['product_id'];
				$rs[$j]['product_name'] = $product_name;
				$rs[$j]['mrp'] = $mrp;
				$rs[$j]['min_qty'] = $priceVal['ProductCombinationsV2']['min_qty'];
				$rs[$j]['so_price'] = $srpirce;
				$rs[$j]['sr_price'] = $priceVal['ProductCombinationsV2']['sr_price'] + 0;
				$rs[$j]['db_price'] = $this->get_db_price($p_com_id);

				$j++;
			}
		}
		//echo '<pre>';print_r($rs);
		//echo '<pre>';print_r($productArray);exit;

		$resultPrice = $this->product_serialize($rs);
		$priceArray = array();
		$k = 1;
		foreach ($resultPrice as $v) {
			foreach ($v as $pval) {
				$priceArray[$k]['id'] = $pval['id'];
				$priceArray[$k]['price_id'] = $pval['price_id'];
				$priceArray[$k]['product_id'] = $pval['product_id'];
				$priceArray[$k]['product_name'] = $pval['product_name'];
				$priceArray[$k]['mrp'] = $pval['mrp'];
				$priceArray[$k]['min_qty'] = $pval['min_qty'];
				$priceArray[$k]['so_price'] = $pval['so_price'];
				$priceArray[$k]['sr_price'] = $pval['sr_price'];
				$priceArray[$k]['db_price'] = $pval['db_price'];

				$k++;
			}
		}

		//	echo '<pre>';print_r($priceArray);exit;
		$prices = $priceArray;

		$this->set(compact('prices'));
	}


	public function product_serialize($array)
	{

		$new = [];

		foreach ($array as $key => $value) {

			$pid = $value['product_id'];

			$new[$pid] = array_filter($array, function ($var) use ($pid) {
				return ($var['product_id'] == $pid);
			});
		}

		return $new;
	}


	function get_db_price($id)
	{

		$this->loadModel('ProductPriceDbSlabs');

		$dbprice = $this->ProductPriceDbSlabs->find('first', array(
			'conditions' => array(
				'ProductPriceDbSlabs.product_combination_id' => $id
			),
			'recursive' => -1
		));

		if (!empty($dbprice)) {
			return $dbprice['ProductPriceDbSlabs']['price'];
		} else {
			return 0;
		}
	}

	function check_sor_sr_selected($id)
	{

		$this->loadModel('ProductPriceSectionV2');

		$dbprice = $this->ProductPriceSectionV2->find('first', array(
			'conditions' => array(
				'ProductPriceSectionV2.id' => $id,
				'ProductPriceSectionV2.is_so' => 0,
				'ProductPriceSectionV2.is_sr' => 0,
				'ProductPriceSectionV2.is_db' => 1
			),
			'recursive' => -1
		));

		if (!empty($dbprice)) {
			return 1;
		} else {
			return 0;
		}
	}
}
