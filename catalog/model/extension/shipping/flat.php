<?php
class ModelExtensionShippingFlat extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/flat');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('flat_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('flat_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		//start calculate the AME shipping fee
		if ($this->cart->getProducts()) {


			$lbtokg  =  0.45359237;

			//价格

			//保健品-------
			$category1_price=3.8;
			//奶粉类-------
			$category2_price=4;
			//日用品-------
			$category3_price=4.5;
			//食品类
			$category4_price=4.5;
			//化妆品------
			$category5_price=10;
			//鞋子帽子类-----
			$category6_price=8;
			//箱包类
			$category7_price=10;
			//蛋白粉类-----
			$category8_price=4;
			//人参类
			$category9_price=15;

			$product_weight1 = 0;
			$product1_box    =  array();
			$box_no1 = 1;
			$product_weight2 = 0;
			$product2_box    =  array();
			$box_no2 = 1;
			$product_weight3 = 0;
			$product3_box    =  array();
			$box_no3 = 1;
			$product_weight4 = 0;
			$product4_box    =  array();
			$box_no4 = 1;
			$product_weight5 = 0;
			$product5_box    =  array();
			$box_no5 = 1;
			$product_weight6 = 0;
			$product6_box    =  array();
			$box_no6 = 1;
			$product_weight7 = 0;
			$product7_box    =  array();
			$box_no7 = 1;
			$product_weight8 = 0;
			$product8_box    =  array();
			$box_no8 = 1;
			$product_weight9 = 0;
			$product9_box    =  array();
			$box_no9 = 1;



			$category1_fee = 0;
			$category2_fee = 0;
			$category3_fee = 0;
			$category4_fee = 0;
			$category5_fee = 0;
			$category6_fee = 0;
			$category7_fee = 0;
			$category8_fee = 0;
			$category9_fee = 0;

			if($this->cart->getProducts()) {

				$products = $this->cart->getProducts();

				$productArr = $products;

				if ($productArr) {
					//sort
					$sortArray = array();

					foreach($productArr as $pd){
						foreach($pd as $key=>$value){
							if(!isset($sortArray[$key])){
								$sortArray[$key] = array();
							}
							$sortArray[$key][] = $value;
						}
					}

					$orderby = "weight"; //the key you want to sort from the array

					array_multisort($sortArray[$orderby],SORT_ASC,$productArr);

					//saparate
					$productArrSaparated1 = array();
					$productArrSaparated2 = array();
					$productArrSaparated3 = array();
					$productArrSaparated4 = array();
					$productArrSaparated5 = array();
					$productArrSaparated6 = array();
					$productArrSaparated7 = array();
					$productArrSaparated8 = array();
					$productArrSaparated9 = array();

					$this->load->model('catalog/product');

					foreach($productArr as $sp){
						if(isset($sp['quantity'])){
							if($sp['quantity']!=0){
								$saparateArray = array(
									'quantity'       => 1,
									'weight'         => $sp['weight'],
									'category_id'   => $this->model_catalog_product ->getCategories($sp['product_id'])
								);
								$product_quantity = $sp['quantity'];
								for($i=$product_quantity; $i>0; $i--) {
									if($saparateArray['category_id']==61) {
										array_push($productArrSaparated1, $saparateArray);
									}
									if($saparateArray['category_id']==86) {
										array_push($productArrSaparated2, $saparateArray);
									}
									if($saparateArray['category_id']==65) {
										array_push($productArrSaparated3, $saparateArray);
									}
									if($saparateArray['category_id']==62) {
										array_push($productArrSaparated4, $saparateArray);
									}
									if($saparateArray['category_id']==104) {
										array_push($productArrSaparated5, $saparateArray);
									}
									if($saparateArray['category_id']==179) {
										array_push($productArrSaparated6, $saparateArray);
									}
									if($saparateArray['category_id']==103) {
										array_push($productArrSaparated7, $saparateArray);
									}
									if($saparateArray['category_id']==193) {
										array_push($productArrSaparated8, $saparateArray);
									}
									if($saparateArray['category_id']==163) {
										array_push($productArrSaparated9, $saparateArray);
									}
								}
							}else{

							}

						}else{

						}
					}


					//allocate

					foreach($productArrSaparated1 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 61) {
							$box_count = ceil(count($productArrSaparated1)/6);
							$product1_box[$box_no1][] = $product['weight'];
							$box_no1++;
							if($box_no1 > $box_count){
								$box_no1 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated2 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 86) {
							$box_count = ceil(count($productArrSaparated2)/6);
							$product2_box[$box_no2][] = $product['weight'];
							$box_no2++;
							if($box_no2 > $box_count){
								$box_no2 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated3 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 65) {
							$box_count = ceil(count($productArrSaparated3)/6);
							$product3_box[$box_no3][] = $product['weight'];
							$box_no3++;
							if($box_no3 > $box_count){
								$box_no3 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated4 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 62) {
							$box_count = ceil(count($productArrSaparated4)/6);
							$product4_box[$box_no4][] = $product['weight'];
							$box_no4++;
							if($box_no4 > $box_count){
								$box_no4 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated5 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 104) {
							$box_count = ceil(count($productArrSaparated5)/6);
							$product5_box[$box_no5][] = $product['weight'];
							$box_no5++;
							if($box_no5 > $box_count){
								$box_no5 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated6 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 179) {
							$box_count = ceil(count($productArrSaparated6)/6);
							$product6_box[$box_no6][] = $product['weight'];
							$box_no6++;
							if($box_no6 > $box_count){
								$box_no6 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated7 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 89) {
							$box_count = ceil(count($productArrSaparated7)/6);
							$product7_box[$box_no7][] = $product['weight'];
							$box_no7++;
							if($box_no7 > $box_count){
								$box_no7 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated8 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 193) {
							$box_count = ceil(count($productArrSaparated8)/6);
							$product8_box[$box_no8][] = $product['weight'];
							$box_no8++;
							if($box_no8 > $box_count){
								$box_no8 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}
					foreach($productArrSaparated9 as $product) {

						$this->load->model('catalog/product');

						//$product_info = $this->model_catalog_product -> getProduct($product['sku']);

						if ( isset( $product['option'] )) {

							$option = array_filter($product['option']);

						} else {

							$option = array();

						}


						//if($product_info) {

						//$category_info = $this->model_catalog_product ->getCategories($product_info['product_id']);

						//	if($category_info){

						//foreach($category_info as $category) {

						//$product['category_id'] = $category["category_id"];

						//$product['weight'] = $product_info['weight'];

						//保健品


						if ($product['category_id'] == 163) {
							$box_count = ceil(count($productArrSaparated9)/6);
							$product9_box[$box_no9][] = $product['weight'];
							$box_no9++;
							if($box_no9 > $box_count){
								$box_no9 = 1;
							}
						}
						else {

						}
						//}
						//}else{
						//	$json['success'] = 0;
						//}
						//}

					}

				}

			}
			$json['array']  =  $product1_box;

			//calculate shipping_fee

			if(isset($product1_box)){
				$box_count = ceil(count($productArrSaparated1)/6);
				for($product1_box_no = 1; $product1_box_no<=$box_count; $product1_box_no++) {
					foreach($product1_box[$product1_box_no] as $product_weight){
						$product_weight1 += $product_weight;
					}
					if (($product_weight1 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight1 / $lbtokg + 0.6) - floor($product_weight1 / $lbtokg + 0.6)) >= 0) && ((($product_weight1 / $lbtokg + 0.6) - floor($product_weight1 / $lbtokg + 0.6)) <= 0.2)) {

							$category1_fee = $category1_fee + floor($product_weight1 / $lbtokg + 0.6) * $category1_price + 0.5 * $category1_price + 1;

						} else {

							$category1_fee = $category1_fee + ceil($product_weight1 / $lbtokg + 0.6) * $category1_price + 1;

						}
					} else {

						$category1_fee = $category1_fee + 1 + 2 * $category1_price;

					}

					$product_weight1 = 0;
				}
			}
			if(isset($product2_box)){
				$box_count = ceil(count($productArrSaparated2)/6);
				for($product2_box_no = 1; $product2_box_no<=$box_count; $product2_box_no++) {
					foreach($product2_box[$product2_box_no] as $product_weight){
						$product_weight2 += $product_weight;
					}
					if (($product_weight2 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight2 / $lbtokg + 0.6) - floor($product_weight2 / $lbtokg + 0.6)) >= 0) && ((($product_weight2 / $lbtokg + 0.6) - floor($product_weight2 / $lbtokg + 0.6)) <= 0.2)) {

							$category2_fee = $category2_fee + floor($product_weight2 / $lbtokg + 0.6) * $category2_price + 0.5 * $category2_price + 1;

						} else {

							$category2_fee = $category2_fee + ceil($product_weight2 / $lbtokg + 0.6) * $category2_price + 1;

						}
					} else {

						$category2_fee = $category2_fee + 1 + 2 * $category2_price;

					}

					$product_weight2 = 0;
				}
			}
			if(isset($product3_box)){
				$box_count = ceil(count($productArrSaparated3)/6);
				for($product3_box_no = 1; $product3_box_no<=$box_count; $product3_box_no++) {
					foreach($product3_box[$product3_box_no] as $product_weight){
						$product_weight3 += $product_weight;
					}
					if (($product_weight3 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight3 / $lbtokg + 0.6) - floor($product_weight3 / $lbtokg + 0.6)) >= 0) && ((($product_weight3 / $lbtokg + 0.6) - floor($product_weight3 / $lbtokg + 0.6)) <= 0.2)) {

							$category3_fee = $category3_fee + floor($product_weight3 / $lbtokg + 0.6) * $category3_price + 0.5 * $category3_price + 1;

						} else {

							$category3_fee = $category3_fee + ceil($product_weight3 / $lbtokg + 0.6) * $category3_price + 1;

						}
					} else {

						$category3_fee = $category3_fee + 1 + 2 * $category3_price;

					}

					$product_weight3 = 0;
				}
			}
			if(isset($product4_box)){
				$box_count = ceil(count($productArrSaparated4)/6);
				for($product4_box_no = 1; $product4_box_no<=$box_count; $product4_box_no++) {
					foreach($product4_box[$product4_box_no] as $product_weight){
						$product_weight4 += $product_weight;
					}
					if (($product_weight4 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight4 / $lbtokg + 0.6) - floor($product_weight4 / $lbtokg + 0.6)) >= 0) && ((($product_weight4 / $lbtokg + 0.6) - floor($product_weight4/ $lbtokg + 0.6)) <= 0.2)) {

							$category4_fee = $category4_fee + floor($product_weight4 / $lbtokg + 0.6) * $category4_price + 0.5 * $category4_price + 1;

						} else {

							$category4_fee = $category4_fee + ceil($product_weight4 / $lbtokg + 0.6) * $category4_price + 1;

						}
					} else {

						$category4_fee = $category4_fee + 1 + 2 * $category4_price;

					}

					$product_weight4 = 0;
				}
			}
			if(isset($product5_box)){
				$box_count = ceil(count($productArrSaparated5)/6);
				for($product5_box_no = 1; $product5_box_no<=$box_count; $product5_box_no++) {
					foreach($product5_box[$product5_box_no] as $product_weight){
						$product_weight5 += $product_weight;
					}
					if (($product_weight5 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight5 / $lbtokg + 0.6) - floor($product_weight5 / $lbtokg + 0.6)) >= 0) && ((($product_weight5 / $lbtokg + 0.6) - floor($product_weight5 / $lbtokg + 0.6)) <= 0.2)) {

							$category5_fee = $category5_fee + floor($product_weight5 / $lbtokg + 0.6) * $category5_price + 0.5 * $category5_price + 1;

						} else {

							$category5_fee = $category5_fee + ceil($product_weight5 / $lbtokg + 0.6) * $category5_price + 1;

						}
					} else {

						$category5_fee = $category5_fee + 1 + 2 * $category5_price;

					}

					$product_weight5 = 0;
				}
			}
			if(isset($product6_box)){
				$box_count = ceil(count($productArrSaparated6)/6);
				for($product6_box_no = 1; $product6_box_no<=$box_count; $product6_box_no++) {
					foreach($product6_box[$product6_box_no] as $product_weight){
						$product_weight6 += $product_weight;
					}
					if (($product_weight6 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight6 / $lbtokg + 0.6) - floor($product_weight6 / $lbtokg + 0.6)) >= 0) && ((($product_weight6 / $lbtokg + 0.6) - floor($product_weight6 / $lbtokg + 0.6)) <= 0.2)) {

							$category6_fee = $category1_fee + floor($product_weight6 / $lbtokg + 0.6) * $category6_price + 0.5 * $category6_price + 1;

						} else {

							$category6_fee = $category6_fee + ceil($product_weight6 / $lbtokg + 0.6) * $category6_price + 1;

						}
					} else {

						$category6_fee = $category6_fee + 1 + 2 * $category6_price;

					}

					$product_weight6 = 0;
				}
			}
			if(isset($product7_box)){
				$box_count = ceil(count($productArrSaparated7)/6);
				for($product7_box_no = 1; $product7_box_no<=$box_count; $product7_box_no++) {
					foreach($product7_box[$product7_box_no] as $product_weight){
						$product_weight7 += $product_weight;
					}
					if (($product_weight7 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight7 / $lbtokg + 0.6) - floor($product_weight7 / $lbtokg + 0.6)) >= 0) && ((($product_weight7 / $lbtokg + 0.6) - floor($product_weight7 / $lbtokg + 0.6)) <= 0.2)) {

							$category7_fee = $category7_fee + floor($product_weight7 / $lbtokg + 0.6) * $category7_price + 0.5 * $category7_price + 1;

						} else {

							$category7_fee = $category7_fee + ceil($product_weight7 / $lbtokg + 0.6) * $category7_price + 1;

						}
					} else {

						$category7_fee = $category7_fee + 1 + 2 * $category7_price;

					}

					$product_weight7 = 0;
				}
			}
			if(isset($product8_box)){
				$box_count = ceil(count($productArrSaparated8)/6);
				for($product8_box_no = 1; $product8_box_no<=$box_count; $product8_box_no++) {
					foreach($product8_box[$product8_box_no] as $product_weight){
						$product_weight8 += $product_weight;
					}
					if (($product_weight8 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight8 / $lbtokg + 0.6) - floor($product_weight8 / $lbtokg + 0.6)) >= 0) && ((($product_weight8 / $lbtokg + 0.6) - floor($product_weight8 / $lbtokg + 0.6)) <= 0.2)) {

							$category8_fee = $category8_fee + floor($product_weight8 / $lbtokg + 0.6) * $category8_price + 0.5 * $category8_price + 1;

						} else {

							$category8_fee = $category8_fee + ceil($product_weight8 / $lbtokg + 0.6) * $category8_price + 1;

						}
					} else {

						$category8_fee = $category8_fee + 1 + 2 * $category8_price;

					}

					$product_weight8 = 0;
				}
			}
			if(isset($product9_box)){
				$box_count = ceil(count($productArrSaparated9)/6);
				for($product9_box_no = 1; $product9_box_no<=$box_count; $product9_box_no++) {
					foreach($product9_box[$product9_box_no] as $product_weight){
						$product_weight9 += $product_weight;
					}
					if (($product_weight9 / $lbtokg + 0.6) > 2.0) {

						if (((($product_weight9 / $lbtokg + 0.6) - floor($product_weight9 / $lbtokg + 0.6)) >= 0) && ((($product_weight9 / $lbtokg + 0.6) - floor($product_weight9 / $lbtokg + 0.6)) <= 0.2)) {

							$category9_fee = $category9_fee + floor($product_weight9 / $lbtokg + 0.6) * $category9_price + 0.5 * $category9_price + 1;

						} else {

							$category9_fee = $category9_fee + ceil($product_weight9 / $lbtokg + 0.6) * $category9_price + 1;

						}
					} else {

						$category9_fee = $category9_fee + 1 + 2 * $category9_price;

					}

					$product_weight9 = 0;
				}
			}







			//将几个品类的邮费加在一起
			$total_fee = 0;
			$total_fee = $total_fee + $category1_fee + $category2_fee + $category3_fee + $category4_fee + $category5_fee + $category6_fee
				+ $category7_fee + $category8_fee+ $category9_fee;


		}

//------------------------------------------------
		$method_data = array();

		if ($status) {
			$quote_data = array();

			$quote_data['flat'] = array(
				'code'          =>  'flat.flat',
				'title'         =>  $this->language->get('text_description'),
				'cost'          =>  $total_fee,
				'tax_class_id' =>  $this->config->get('flat_tax_class_id'),
				'text'          =>  $this->currency->format($this->tax->calculate($total_fee, $this->config->get('flat_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'flat',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('flat_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}