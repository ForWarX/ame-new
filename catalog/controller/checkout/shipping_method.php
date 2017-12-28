<?php
class ControllerCheckoutShippingMethod extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');

		if (isset($this->session->data['shipping_address'])) {
		    if (!isset($this->session->data['order_type']) || $this->session->data['order_type'] != 'express') { // 购物下单，自动分单
                // 分单
                $products = $this->cart->getProducts();
                //var_dump($products);
                $boxes = $this->get_box_types(); // 箱子种类
                $order_split = $this->split_order($products, $boxes); // 当前类中分单算法
                /******************************
                 * 分单结果：[
                 *     路线，
                 *     箱子1 => [（产品1，方向1），……，（产品n，方向n）]，
                 *     ……，
                 *     箱子m => [（产品n+1，方向n+1），……，（产品2n，方向2n）]，
                 *     适应值
                 * ]
                 *******************************/
                $this->session->data['split_strategy'] = $order_split; // session保存分箱策略

                // ============================ Debug ============================
                if (false && $order_split != false) {
                    // 获取所有箱子种类
                    $boxes = $this->get_box_types();
                    $box_count = count($boxes);

                    //var_dump($order_split);

                    // 显示分单结果
                    foreach ($order_split as $order) {
                        //var_dump($order);
                        $products_group = $order[0]; // 拆分数量大于1的产品的产品组
                        $AGA_group = $order[1]; // 遗传算法的分箱结果

                        $route = $AGA_group[0] ? "BC" : "个人";
                        var_dump("\n路线：" . $route);
                        $adaptive_value = array_pop($AGA_group);
                        var_dump("\n适应值：" . $adaptive_value);
                        array_shift($AGA_group);
                        foreach ($AGA_group as $box) {
                            $box_type = $box[0][2] % $box_count;
                            var_dump("\n箱子类型" . $boxes[$box_type]['name']);
                            $output_str = "\n产品：[\n";
                            foreach ($box as $unit) {
                                $product_id = $unit[0];
                                $direction = $unit[1];
                                //$box_id = $unit[2];
                                switch ($direction) {
                                    case 1:
                                        $direction = "宽长高";
                                        break;
                                    case 2:
                                        $direction = "宽高长";
                                        break;
                                    case 3:
                                        $direction = "长宽高";
                                        break;
                                    case 4:
                                        $direction = "长高宽";
                                        break;
                                    case 5:
                                        $direction = "高宽长";
                                        break;
                                    case 6:
                                        $direction = "高长宽";
                                        break;
                                }
                                $output_str .= "-- " . $products_group[$product_id]['name'] . ", 方向：" . $direction . "\n";
                            }
                            $output_str .= "]";
                            var_dump($output_str);
                        }
                    }
                } else {
                    //var_dump("\n分箱失败");
                }
                // ========================== Debug End ==========================

                if ($order_split == false) {
                    $data['error_warning'] = sprintf($this->language->get('error_no_order_split'), $this->url->link('information/contact'));
                } else {
                    $data['error_warning'] = '';

                    // 按箱整理产品，把多个相同的产品合并到quantity中
                    $order = $this->box_combine_same_product($order_split, $boxes);
                    //var_dump($order);

                    $this->session->data['order_per_box'] = $order; // session保存处理好的分箱结果
                    $data['order'] = $order; // 把分单结果传给前端页面

                    // Shipping Methods
                    $method_data = array();

                    $this->load->model('extension/extension');

                    $results = $this->model_extension_extension->getExtensions('shipping');

                    // 对每个箱子单独进行运费计算
                    $box_num = 0;
                    foreach ($order as $order_per_box) {
                        foreach ($results as $result) {
                            if ($this->config->get($result['code'] . '_status')) {
                                if ($box_num == 0) {
                                    $this->load->model('extension/shipping/' . $result['code']);
                                }

                                // 由于运费计算是直接获取购物车中的产品，因此按照箱子对购物车中的产品进行更改
                                unset($order_per_box[0]); // 去除路线
                                unset($order_per_box[1]); // 去除箱子名
                                $this->cart->clear(); // 清空购物车
                                // 将箱子中所有产品放入购物车
                                foreach ($order_per_box as $product) {
                                    $this->cart->add($product['product_id'], $product['quantity'], $product['option']);
                                }

                                $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                                if ($quote) {
                                    if ($box_num == 0) {
                                        $method_data[$result['code']] = array(
                                            'title' => $quote['title'],
                                            'quote' => $quote['quote'],
                                            'sort_order' => $quote['sort_order'],
                                            'error' => $quote['error']
                                        );
                                    } else {
                                        // 合并运费
                                        foreach ($quote['quote'] as $key => $val) {
                                            $method_data[$result['code']]['quote'][$key]['cost'] += $val['cost'];
                                        }
                                    }
                                }
                            }
                        }
                        $box_num++;
                    }

                    // 对配送价格进行格式化处理
                    foreach ($method_data as $key1 => $method) {
                        foreach ($method['quote'] as $key2 => $quote) {
                            $text = $this->currency->format($this->tax->calculate($quote['cost'], $this->config->get('weight_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']);
                            $method_data[$key1]['quote'][$key2]['text'] = $text;
                        }
                    }

                    // 还原购物车
                    $this->cart->clear();
                    foreach ($products as $product) {
                        $this->cart->add($product['product_id'], $product['quantity'], $product['option']);
                    }

                    // 配送方式排序
                    $sort_order = array();

                    foreach ($method_data as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                    }

                    array_multisort($sort_order, SORT_ASC, $method_data);

                    $this->session->data['shipping_methods'] = $method_data;
                }
            } else { // 快递下单，暂且统一到店里结算
                // Shipping Methods
                $method_data = array();

                $this->load->model('extension/extension');

                $results = $this->model_extension_extension->getExtensions('shipping');

                $this->load->language('product/apply');
                $text_cannot_calculate_shipping = $this->language->get('text_cannot_calculate_shipping');

                foreach ($results as $result) {
                    if ($this->config->get($result['code'] . '_status')) {
                        $this->load->model('extension/shipping/' . $result['code']);

                        $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                        if ($quote) {
                            foreach ($quote['quote'] as $key => $q) {
                                $quote['quote'][$key]['text'] = $text_cannot_calculate_shipping;
                                $quote['quote'][$key]['cost'] = 0;
                            }
                            $method_data[$result['code']] = array(
                                'title' => $quote['title'],
                                'quote' => $quote['quote'],
                                'sort_order' => $quote['sort_order'],
                                'error' => $quote['error']
                            );
                        }
                    }
                }

                // 配送方式排序
                $sort_order = array();

                foreach ($method_data as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];
                }

                array_multisort($sort_order, SORT_ASC, $method_data);

                $this->session->data['shipping_methods'] = $method_data;
            }
		}

		$data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$data['text_comments'] = $this->language->get('text_comments');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_box'] = $this->language->get('text_box');

		$data['button_continue'] = $this->language->get('button_continue');

		if (empty($this->session->data['shipping_methods']) && empty($data['error_warning'])) {
            $data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
        } else {
            $data['error_warning'] = '';
        }

		if (isset($this->session->data['shipping_methods'])) {
			$data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$data['shipping_methods'] = array();
		}

		if (isset($this->session->data['shipping_method']['code'])) {
			$data['code'] = $this->session->data['shipping_method']['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($this->session->data['comment'])) {
			$data['comment'] = $this->session->data['comment'];
		} else {
			$data['comment'] = '';
		}

		$this->response->setOutput($this->load->view('checkout/shipping_method', $data));
	}

	public function save() {
		$this->load->language('checkout/checkout');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if shipping address has been set.
		if (!isset($this->session->data['shipping_address'])) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!isset($this->request->post['shipping_method'])) {
			$json['error']['warning'] = $this->language->get('error_shipping');
		} else {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			}
		}

		if (!$json) {
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

            $this->session->data['comment'] = '';

            if (!empty($this->request->post['comment'])) {
                $this->session->data['comment'] = strip_tags($this->request->post['comment']);
            }
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	// 分单操作，出错返回false
	protected function split_order($products, $boxes = null) {
	    //foreach ($products as $product) var_dump($product);

        // 获取所有箱子种类
        if ($boxes == null) {
            $boxes = $this->get_box_types();
        }

        // 将重量和空间的单位改成后台设置的单位
        // 参数$products是引用，因此会被修改
        $this->correct_unit($products);

        // 确认每个产品能够装得下的箱子以及可以放入该箱子的方向
        // 参数$products是引用，因此会被修改
        $result = $this->check_box_and_direction($products, $boxes);
        //var_dump($products[3]['choice']);

        if (!$result) return false; // 有产品放不下任何一种箱子

        // 获取产品的二级品类及相关数据
        // 之所以选择二级品类是根据价目表
        // 找到第一个二级品类就停止
        // 只有一级品类的就按照一级品类
        $found_id = []; // 已查找过的id，为了减少同品类查找时间
        foreach ($products as $key => $product) {
            $category = current($product['categories']);
            $cid = $category['category_id']; // 当前品类ID
            $pid = $category['parent_id']; // 父品类ID
            unset($products[$key]['categories']);

            while ($pid != 0) {
                if (isset($found_id[$pid])) {
                    if ($found_id[$pid] == 0) break; // 再往上就是一级品类，因此留在二级品类
                    else {
                        $cid = $pid;
                        $pid = $found_id[$pid];
                    }
                } else {
                    $category = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id='" . $pid . "'");
                    $category = $category->row;
                    $found_id[$pid] = $category['parent_id'];
                    if ($found_id[$pid] == 0) break; // 再往上就是一级品类，因此留在二级品类
                    else {
                        $cid = $pid;
                        $pid = $found_id[$pid];
                    }
                }
            }
            $category = $this->db->query("SELECT canMix, tax_limit, tax, tax_type, weight_limit, quantity_limit, violation_fee FROM " . DB_PREFIX . "category WHERE category_id='" . $cid . "'");
            $category = $category->row;
            $products[$key]['category_id'] = $cid;
            $products[$key]['category'] = $category;
            $products[$key]['can_mix'] = $category['canMix'];
        }
        //var_dump($products);

        // 按照二级品类分成多组
        $mix_group = [];
        foreach ($products as $product) {
            $added = false; // 产品是否已添加到任何一个组里
            // 合并到已有品类组
            foreach ($mix_group as $key => $group) {
                // 取出品类组中的第一件产品进行比较
                $added_product = $group[0];

                // 判断是否品类一致
                if ($added_product['category_id'] == $product['category_id']) {
                    $mix_group[$key][] = $product;
                    $added = true;
                    break;
                }
            }
            // 没有合并，创建新品类组
            if (!$added) {
                $mix_group[] = [$product];
            }
        }
        //var_dump($mix_group);

        $final_group = []; // 最终分单结果
        foreach ($mix_group as $group) {
            $products_group = $this->order_split_products($group); // 将数量大于1的产品拆分
            $group = $this->AGA_order($products_group, $boxes);
            if ($group == false) return false;
            $final_group[] = [$products_group, $group];
        }
        //var_dump($final_group);

        return $final_group;
    }

    /*******************************************************************************************
     * 自适应遗传算法分单
     *
     * 方向定义：
     * 大写为箱子属性，小写是产品属性
     * 1. w/W, l/L, h/H
     * 2. w/W, h/L, l/H
     * 3. l/W, w/L, h/H
     * 4. l/W, h/L, w/H
     * 5. h/W, w/L, l/H
     * 6. h/W, l/L, w/H
     *
     * 路线定义：
     * 0：个人包裹（限制数据来自品类的quantity_limit和weight_limit字段，收税数据来自品类的tax, tax_type, tax_limit字段）
     * 1：BC（不能超过￥2000以及20kg，收税数据来自extension/total/bao_guan_tax的设置）
     *
     * 设定：
     * -- 不能违规（超重、超量）
     * -- 每种箱子数量上限都为产品上限，即最差的情况一个箱子装一个产品
     * -- 箱子一共M种（索引0到M-1），产品一共N个，即每种箱子一共N个
     * -- 产品编号：产品组$group中的索引
     * -- 箱子编号：箱子种类总数 x 第几个箱子 + 第几种箱子 - 1，（第几种箱子 - 1）代表箱子种类索引
     * -- 比如箱子一共8种，第5个3类箱子编号是8x5+3-1=42，箱子个数=42/8=5，箱子种类=42%8=2->3类
     * -- 特征：(产品#，方向#，箱子#)，#表示第几个特征
     * -- 基因：[路线，(产品1，方向1，箱子1)，……，(产品n，方向n，箱子n)]
     * -- 方向和箱子编号均为随机选取，基因顺序随机
     * -- 每个产品能以哪些方向放入哪些箱子定义在$product['choice']中，$product['choice'] = [箱子种类索引 => 可选方向组]，$product['choice_count']记录了一共可以选择多少种箱子
     * -- 基因的顺序就是产品放入箱子的顺序，按照左下角原点原则排放
     * -- 基因组：众多的染色体组成基因组（染色体组名字太长，个体组太别扭）
     *
     * 适应值：
     * -- 空间适应值：根据体积比计算填充率，所有箱子的填充率相乘得出总填充率（箱子数越多乘积会迅速减少，对应箱子数最小化，也就是包含了填充率和箱子数量这两点）
     * -- 价格适应值：计算每个基因的价格，再计算整个基因组的总价，（1 - 两者的比例）为当前基因的价格适应值
     * -- 价格只考虑税费，不考虑运费，因为要对每种配送方式单独计算费用不仅麻烦而且特别耗效率
     * -- 0.7空间适应值 + 0.3价格适应值 = 基因适应值
     * -- 若装不下或者违规，则惩罚适应值为0
     *
     * 以下P代表概率，F代表当前基因适应值
     *
     * 精英遗传策略：
     * -- 用父代基因组中适应值最高的基因替代掉子代中适应值最差的基因
     *
     * 选择概率：
     * P = F / F_total    F_total >  0
     * P = 1 / gene_num   F_total == 0
     *
     * 以下F'代表两个父代基因中适应值较大的那个
     *
     * 交叉策略：
     * -- 保留路线，只交换相同物品
     * -- 随机（1-n）作为交换数量a，然后从[1, n]（0是路线）中随机选出a个数作为交换的物品索引
     * -- 交换时保持物品在数组中的位置
     *
     * 交叉概率：
     * Pc_init为初始交叉概率
     * Pc = 0                                                F_max == F_avg
     * Pc = Pc_init * ( F_max - F' ) / ( F_max - F_avg )     F' >  F_avg
     * Pc = Pc_init                                          F' <= F_avg
     *
     * 变异策略：
     * -- 若发生变异，则有一定概率改变某一个特征的方向、箱子、顺序
     * -- 1/3概率改变方向和箱子
     * -- 1/3概率只改变方向
     * -- 1/3概率只改变箱子
     * -- 以上为100%的概率
     * -- 顺序有单独25%概率进行改变，整个数组随机打乱
     *
     * 变异概率：
     * Pm_init为初始变异概率
     * Pm = 0                                                F_max == F_avg
     * Pm = Pm_init * ( F_max - F ) / ( F_max - F_avg )      F >  F_avg
     * Pm = Pm_init                                          F <= F_avg
     *
     * 返回：
     * 最佳的分箱基因，按照箱子归类整理
     *******************************************************************************************/
    // 参数：产品数组，箱子种类数组
    // $group中每个产品的索引即产品ID
    protected function AGA_order($group, $box_types) {
        //var_dump($group);
        //var_dump($box_types);

        // 初始设定
        $max_generation = 200;                   // 最大遗传次数
        $Pc_init = 1.0;                          // 初始交叉概率（要写小数）
        $Pm_init = 0.5;                          // 初始变异概率（要写小数）
        $box_types_count = count($box_types);    // 箱子种类数量
        $products_count = count($group);         // 产品数量
        $routes = [0, 1];                        // 0：个人包裹，1：BC
        $routes_count = count($routes);          // 路线总数
        $gene_num = 100;                         // 种群数量（2的倍数）
        //$fit_adaptive_value = 0.75;            // 比较合适的适应值【改：不进行适应值判定，因为物品少的时候根本装不满任何箱子，也就是适应值根本不可能达到这个值】
        $adaptive_rate = 0.6;                    // 空间适应值占有比例，剩余的是价格适应值所占比例
        $same_best_gene_max_count = 40;          // 最佳基因未改变的最大代数，可以看做找到了最优解

        // 每种箱子的数量 = 产品总数 / 品类的数量限制（即看成数量限制代表了箱子能装下这么多产品）
        $quantity_limit = $group[0]['category']['quantity_limit'];
        $box_count = $products_count % $quantity_limit == 0 ? $products_count / $quantity_limit : $products_count / $quantity_limit + 1;

        // 获取kg的weight_class_id，下面有用到单独设定的kg，数据要转成后台设定的重量单位
        $this->load->model("localisation/weight_class");
        $kg_class = $this->model_localisation_weight_class->getWeightClassDescriptionByUnit("kg");
        $kg_class_id = $kg_class['weight_class_id'];
        //var_dump($kg_class);

        // 生成初始种群
        $gene_group = [];
        for ($i = 0; $i < $gene_num; $i++) {
            $gene = [];
            $route = $routes[mt_rand(0,$routes_count-1)];
            $gene[] = $route;

            $range = range(0, $products_count-1);
            shuffle($range);
            foreach ($range as $product_id) {
                $choice = $group[$product_id]['choice']; // [箱子种类索引 => 可选方向组]
                // 箱子编号 = 箱子种类总数 x 第几个箱子 + 第几种箱子 - 1，（第几种箱子 - 1）代表箱子种类索引
                $box_key = array_rand($choice);
                $box_id = $box_types_count * mt_rand(1, $box_count) + $box_key;
                // 方向
                $direction_key = array_rand($choice[$box_key]);
                $direction = $choice[$box_key][$direction_key];

                $gene[] = [$product_id, $direction, $box_id];
            }
            $gene_group[] = $gene;
        }
        //var_dump($gene_group);

        // 遗传循环
        $old_best_gene = null; // 父代中适应值最高的基因，用于替换子代中最差的基因
        $same_best_gene_count = 0; // 记录最佳基因经过多少代没有更换
        for ($cur_generation = 0; $same_best_gene_count < $same_best_gene_max_count && $cur_generation < $max_generation; $cur_generation++) {
            // 删除原先的适应值
            if ($old_best_gene != null) {
                array_walk($gene_group, function(&$gene) {
                    array_pop($gene);
                });
            }

            $gene_total_price = 0; // 基因组的总价，用于计算价格适应值

            //var_dump("当代基因组");
            //var_dump($gene_group);

            // 计算适应值
            foreach ($gene_group as $key => $gene) {
                // $gene: [路线，(产品1，方向1，箱子1)，……，(产品n，方向n，箱子n)]

                //var_dump("当前基因：");
                //var_dump($gene);

                $route = $gene[0]; // 路线编号

                // 按照箱子归类整理产品
                $boxes = [];
                next($gene); // 跳过路线
                while ($unit = current($gene)) {
                    //$product_id = $unit[0];
                    //$direction = $unit[1];
                    $box_id = $unit[2];
                    $boxes[$box_id][] = $unit;
                    next($gene);
                }
                reset($gene); // 重置数组指针到开头
                //var_dump("按箱归类：");
                //var_dump($boxes);

                $adaptive_value = 0; // 基因总适应值
                $adaptive_value_space = []; // 存放计算空间适应值所用的数据
                $adaptive_value_price = []; // 存放计算价格适应值所用的数据
                $not_adaptive = false; // bool，用于辨别基因是否适应

                // 遍历箱子
                foreach ($boxes as $box) {
                    // 产品总体积超过箱子上限
                    $product_total_volume = 0;
                    foreach ($box as $unit) {
                        $product_id = $unit[0];
                        //$direction = $unit[1];
                        //$box_id = $unit[2];
                        $product_total_volume += $group[$product_id]['volume'];
                    }
                    $box_id = $box[0][2];
                    $box_type = $box_id % $box_types_count;
                    $box_data = $box_types[$box_type];
                    $box_volume = $box_data['volume'];
                    if ($product_total_volume > $box_volume) {
                        $not_adaptive = true;
                        //var_dump("总体积超过上限");
                        break;
                    }

                    // 不同路线的数量和重量限制
                    switch ($route) {
                        case 0: // 个人包裹，每个品类的限制不同，数量和重量都有限制
                            // 获取当前箱中品类的限制
                            $product_id = $box[0][0];
                            $category = $group[$product_id]['category'];
                            $quantity_limit = $category['quantity_limit'];
                            $weight_limit = $category['weight_limit'];

                            // 判断数量超上限
                            if (count($box) > $quantity_limit) {
                                $adaptive_value = 0;
                                $not_adaptive = true;
                                //var_dump("个人线，品类数量上限：" . $quantity_limit);
                            }

                            // 判断重量超上限
                            $product_total_weight = 0;
                            foreach ($box as $unit) {
                                $product_id = $unit[0];
                                //$direction = $unit[1];
                                //$box_id = $unit[2];
                                $product_total_weight += $group[$product_id]['weight'];
                            }
                            if ($product_total_weight > $weight_limit) {
                                $adaptive_value = 0;
                                $not_adaptive = true;
                                //var_dump("个人线，品类重量上限：" . $weight_limit);
                            }

                            break;
                        case 1: // BC，总重不超过20kg
                            // 转换重量单位为后台设定的单位
                            $weight_limit = 20;
                            $weight_limit = $this->weight->convert($weight_limit, $kg_class_id, $this->config->get("config_weight_class_id"));

                            // 判断重量超上限
                            $product_total_weight = 0;
                            foreach ($box as $unit) {
                                $product_id = $unit[0];
                                //$direction = $unit[1];
                                //$box_id = $unit[2];
                                $product_total_weight += $group[$product_id]['weight'];
                            }
                            if ($product_total_weight > $weight_limit) {
                                $adaptive_value = 0;
                                $not_adaptive = true;
                                //var_dump("BC线，重量上限：" . $weight_limit);
                            }

                            break;
                    }
                    // 有某种数据超过上限，跳出遍历箱子的循环
                    if ($not_adaptive) {
                        break;
                    }

                    // 从空间判断是否装得下
                    // 参数：产品数组，产品装填顺序和方向，箱子数据
                    if (!$this->if_box_can_contain($group, $box, $box_data)) {
                        $not_adaptive = true;
                        //var_dump("空间装不下");
                        //var_dump($box);
                        break;
                    }

                    // 空间适应值
                    $adaptive_value_space[] = (float)$product_total_volume / $box_volume;

                    // 价格适应值
                    switch ($route) {
                        case 0: // 个人包裹，每个箱子价值若超过品类额度上限，则要交税
                            $total_price = 0; // 该箱子总价
                            foreach ($box as $unit) {
                                $product_id = $unit[0];
                                //$direction = $unit[1];
                                //$box_id = $unit[2];
                                $product = $group[$product_id];
                                $total_price += $this->currency->convert($product['price'], $this->config->get("config_currency"), 'CNY');
                            }

                            // 计算税额
                            $product_id = $box[0][0];
                            $product = $group[$product_id];
                            $category = $product['category'];
                            $tax_limit = $category['tax_limit']; // 免税上限（RMB）
                            $tax_rate = $category['tax']; // 税额
                            $tax_type = $category['tax_limit']; // 税额是 0：固定值，1：产品价格百分比
                            if ($total_price > $tax_limit) {
                                if ($tax_type == 0) {
                                    $tax = $tax_rate;
                                } else {
                                    $tax = $total_price * $tax_rate;
                                }

                                $adaptive_value_price[] = $tax;
                            }

                            break;
                        case 1: // BC，税 = 产品价格 x 0.5 x 0.119
                            $total_price = 0; // 该箱子总价
                            foreach ($box as $unit) {
                                $product_id = $unit[0];
                                //$direction = $unit[1];
                                //$box_id = $unit[2];
                                $product = $group[$product_id];
                                $total_price += $product['price'];
                            }

                            $base_rate = (float)$this->config->get("bao_guan_tax_rate");
                            $bao_guan_rate = (float)$this->config->get("bao_guan_tax");
                            $tax = $total_price * $base_rate * $bao_guan_rate;
                            $adaptive_value_price[] = $tax;

                            break;
                    }
                }

                if ($not_adaptive) {
                    // 违规，惩罚适应值为0
                    $adaptive_value = 0;
                } else {
                    // 空间适应值
                    $space_value = -1;
                    foreach ($adaptive_value_space as $value) {
                        if ($space_value == -1) {
                            $space_value = $value;
                        } else {
                            $space_value *= $value;
                        }
                    }

                    // 该基因总价，等到所有基因价格计算完后再计算价格适应值
                    $total_price = 0;
                    foreach ($adaptive_value_price as $price) {
                        $total_price += $price;
                    }
                    $gene_total_price += $total_price; // 将当前基因的价格加入基因组的总价

                    $adaptive_value = [$space_value, $total_price];
                    //var_dump($adaptive_value);
                }

                // 将适应值加到每个基因最后
                $gene_group[$key][] = $adaptive_value;
                //var_dump($gene_group);
            }
            //var_dump("计算适应值后的基因组");
            //var_dump($gene_group);

            // 计算最终适应值，以及用上一代最佳基因替换本代最差基因
            $total_adaptive_value = 0; // 基因组总适应值，用于计算之后的遗传概率
            $lowest_adaptive_value = 1; // 最低的适应值
            $lowest_adaptive_key = -1; // 最低适应值的基因的索引
            foreach ($gene_group as $key => $gene) {
                $adaptive_value = array_pop($gene_group[$key]);
                if ($adaptive_value != 0) {
                    $space_value = $adaptive_value[0];
                    if ($gene_total_price == 0) {
                        $price_value = 1; // 基因组总价为0，即所有基因价格适应值都最大
                    } else {
                        $price_value = 1 - $adaptive_value[1] / $gene_total_price;
                    }

                    $adaptive_value = $adaptive_rate * $space_value + (1 - $adaptive_rate) * $price_value;
                }
                $gene_group[$key][] = $adaptive_value; // 每个基因自己的适应值
                $total_adaptive_value += $adaptive_value; // 基因组总适应值
                if ($adaptive_value < $lowest_adaptive_value) { // 记录适应值最低的基因
                    $lowest_adaptive_value = $adaptive_value;
                    $lowest_adaptive_key = $key;
                }
            }
            // 用父代适应值最高的基因替换本代适应值最低的基因
            if ($old_best_gene != null && $lowest_adaptive_key != -1 && end($old_best_gene) > $lowest_adaptive_value) {
                $total_adaptive_value -= end($gene_group[$lowest_adaptive_key]);
                $total_adaptive_value += end($old_best_gene);
                $gene_group[$lowest_adaptive_key] = $old_best_gene;
            }

            // 遗传过程：

            // 将基因组按照适应值从大到小排序
            usort($gene_group, function($a, $b) {
                $adaptive_a = end($a);
                $adaptive_b = end($b);
                if ($adaptive_a == $adaptive_b) return 0;
                return $adaptive_a < $adaptive_b ? 1 : -1;
            });

            // 记录本代适应值最高的基因
            if ($old_best_gene == $gene_group[0]) {
                // 最佳基因没有更换
                $same_best_gene_count++;
            } else {
                $old_best_gene = $gene_group[0];
                $same_best_gene_count = 0;
            }

            $adaptive_max = end($old_best_gene); // 最高适应值
            $adaptive_average = (float)$total_adaptive_value / $gene_num; // 平均适应值

            $new_group = []; // 子代基因组
            $new_group_count = 0; // 子代中的基因数量
            $pick_func = $total_adaptive_value > 0 ? "pick_gene1" : "pick_gene2"; // 选择基因的方式
            while ($new_group_count < $gene_num) {
                // 选择基因对
                $gene_pair = $this->$pick_func($gene_group, $gene_num, $total_adaptive_value);
                //var_dump($gene_pair);

                $gene1 = $gene_pair[0]; // 基因1
                $gene2 = $gene_pair[1]; // 基因2

                // 交叉概率
                if ($adaptive_max == $adaptive_average) {
                    $Pc = 0;
                } else {
                    $adaptive_bigger = max(end($gene1), end($gene2));
                    if ($adaptive_bigger > $adaptive_average) {
                        $Pc = $Pc_init * ($adaptive_max - $adaptive_bigger) / ($adaptive_max - $adaptive_average);
                    } else { // $adaptive_bigger <= $adaptive_average
                        $Pc = $Pc_init;
                    }
                }

                // 变异概率
                if ($adaptive_max == $adaptive_average) {
                    $Pm1 = $Pm2 = 0;
                } else {
                    $adaptive_value1 = end($gene1);
                    $adaptive_value2 = end($gene2);
                    if ($adaptive_value1 > $adaptive_average) {
                        $Pm1 = $Pm_init * ($adaptive_max - $adaptive_value1) / ($adaptive_max - $adaptive_average);
                    } else { // $adaptive_value1 <= $adaptive_average
                        $Pm1 = $Pm_init;
                    }
                    if ($adaptive_value2 > $adaptive_average) {
                        $Pm2 = $Pm_init * ($adaptive_max - $adaptive_value2) / ($adaptive_max - $adaptive_average);
                    } else { // $adaptive_value1 <= $adaptive_average
                        $Pm2 = $Pm_init;
                    }
                }

                // 交叉
                if ($Pc > 0 && $this->mt_rand_float() <= $Pc) {
                    $cross_keys = array_rand(range(1, $products_count), mt_rand(1, $products_count)); // 要进行交叉的特征索引
                    if (!is_array($cross_keys)) $cross_keys = [$cross_keys]; // array_rand()只随机出一个数的时候并不是数组
                    foreach ($cross_keys as $key) {
                        $key++; // array_rand()选出来的是range()制造的特征索引数组的索引，正常应该用range()[$key]来获取特征索引，不过特征索引只比$key大了1，所以简单处理了
                        $gene1_value = $gene1[$key]; // 基因1的特征（物品#，方向#，箱子#）
                        $product_id = $gene1_value[0];
                        // 在基因2中查找相同特征
                        for ($index = 1; $index <= $products_count; $index++) { // 不能用current()和next()，因为循环中更改了$gene2，会导致数组指针失灵
                            $gene2_value = $gene2[$index];
                            if ($gene2_value[0] == $product_id) {
                                $gene1[$key] = $gene2_value;
                                $gene2[$index] = $gene1_value;
                            }

                            /*
                            if (!is_array($gene2_value)) {
                                var_dump("交叉时基因2选取了非特征数据, index：" . $index . "，产品数量：" . $products_count);
                                var_dump($gene2);
                                var_dump($gene2_value);
                            }*/
                        }
                    }
                }

                // 基因1变异
                if ($Pm1 > 0 && $this->mt_rand_float() <= $Pm1) {
                    $key = mt_rand(1, $products_count); // 要变异的特征索引
                    $product_id = $gene1[$key][0]; // 要变异的特征里的产品ID
                    $choice = $group[$product_id]['choice']; // [箱子种类索引 => 可选方向组]
                    // 箱子编号 = 箱子种类总数 x 第几个箱子 + 第几种箱子 - 1，（第几种箱子 - 1）代表箱子种类索引
                    $box_key = array_rand($choice);
                    $box_id = $box_types_count * mt_rand(1, $box_count) + $box_key;
                    // 方向
                    $direction_key = array_rand($choice[$box_key]);
                    $direction = $choice[$box_key][$direction_key];
                    switch (mt_rand(0, 2)) {
                        case 0: // 改变方向和箱子
                            $gene1[$key][1] = $direction;
                            $gene1[$key][2] = $box_id;

                            break;
                        case 1: // 只改变方向
                            $gene1[$key][1] = $direction;
                            break;
                        case 2: // 只改变箱子
                            $gene1[$key][2] = $box_id;
                            break;
                    }

                    // 改变装填顺序
                    if (mt_rand(1, 2) == 1) {
                        $route = $gene1[0];
                        $adaptive_value = array_pop($gene1);
                        array_shift($gene1);
                        shuffle($gene1);
                        array_unshift($gene1, $route);
                        $gene1[] = $adaptive_value;
                    }
                }
                // 基因2变异
                if ($Pm2 > 0 && $this->mt_rand_float() <= $Pm2) {
                    $key = mt_rand(1, $products_count); // 要变异的特征索引
                    $product_id = $gene2[$key][0]; // 要变异的特征里的产品ID
                    $choice = $group[$product_id]['choice']; // [箱子种类索引 => 可选方向组]
                    // 箱子编号 = 箱子种类总数 x 第几个箱子 + 第几种箱子 - 1，（第几种箱子 - 1）代表箱子种类索引
                    $box_key = array_rand($choice);
                    $box_id = $box_types_count * mt_rand(1, $box_count) + $box_key;
                    // 方向
                    $direction_key = array_rand($choice[$box_key]);
                    $direction = $choice[$box_key][$direction_key];
                    switch (mt_rand(0, 2)) {
                        case 0: // 改变方向和箱子
                            $gene2[$key][1] = $direction;
                            $gene2[$key][2] = $box_id;
                            break;
                        case 1: // 只改变方向
                            $gene2[$key][1] = $direction;
                            break;
                        case 2: // 只改变箱子
                            $gene2[$key][2] = $box_id;
                            break;
                    }

                    // 改变装填顺序
                    if (mt_rand(1, 2) == 1) {
                        $route = $gene2[0];
                        $adaptive_value = array_pop($gene2);
                        array_shift($gene2);
                        shuffle($gene2);
                        array_unshift($gene2, $route);
                        $gene2[] = $adaptive_value;
                    }
                }

                $new_group[] = $gene1;
                $new_group[] = $gene2;
                $new_group_count += 2;
            }

            //var_dump("子代基因组");
            //var_dump($new_group);

            $gene_group = $new_group; // 子代取代父代
        }
        //var_dump("最佳基因是否更换");
        //var_dump($same_best_gene_count != 0);


        // 选出最好的基因
        $best_gene = $old_best_gene;
        $adaptive_best = end($best_gene);
        foreach ($gene_group as $gene) {
            $adaptive_value = end($gene);
            if ($adaptive_value > $adaptive_best) {
                $best_gene = $gene;
            }
        }
        //var_dump($best_gene);

        // 按照箱子归类整理产品
        $boxes = [];
        $adaptive_value = array_pop($best_gene); // 先取出数组最后一个适应值，减少下面循环的时候判断元素是否是数组或是否是最后一个
        if ($adaptive_value == 0) {
            // 未找到任何装箱方案
            return false;
        } else {
            next($best_gene);
            while ($unit = current($best_gene)) {
                //$product_id = $unit[0];
                //$direction = $unit[1];
                $box_id = $unit[2];
                $boxes[$box_id][] = $unit;
                next($best_gene);
            }
            reset($best_gene);
            array_splice($best_gene, 1, $products_count, $boxes); // 用按箱分好的数组取代原本的基因特征
            $best_gene[] = $adaptive_value;
            //var_dump($best_gene);
        }

        return $best_gene;
    }

    // 判断箱子是否装得下产品
    // 利用三叉树分割法在装入产品后分割剩余空间，分成前、右、上三个空间
    // 分割时若剩余宽 > 剩余长，则前空间 = 剩余宽 x 全长，若剩余宽 <= 剩余长，则右空间 = 剩余长 x 全宽
    // 参数：产品数组，产品装填顺序和方向，箱子数据
    protected function if_box_can_contain($group, $box, $box_data) {
        //var_dump($group);
        //var_dump($box);
        //var_dump($box_data);

        $box_width = $box_data['width'];
        $box_length = $box_data['length'];
        $box_height = $box_data['height'];

        if ($box_width <= 0 || $box_length <= 0 || $box_height <= 0) return false; // 箱子数据错误

        // 初始箱子空间
        $box_space = [new BoxSpace(0,0,0,$box_width,$box_length,$box_height)];

        foreach ($box as $unit) {
            $product_id = $unit[0];
            $direction = $unit[1];
            //$box_id = $unit[2];

            $product = $group[$product_id];
            $product_width = $product['width'];
            $product_length = $product['length'];
            $product_height = $product['height'];

            // 根据方向确定放入箱子时的长宽高
            switch ($direction) {
                case 1: // 1. w/W, l/L, h/H
                    $item_width = $product_width;
                    $item_length = $product_length;
                    $item_height = $product_height;
                    break;
                case 2: // 2. w/W, h/L, l/H
                    $item_width = $product_width;
                    $item_length = $product_height;
                    $item_height = $product_length;
                    break;
                case 3: // 3. l/W, w/L, h/H
                    $item_width = $product_length;
                    $item_length = $product_width;
                    $item_height = $product_height;
                    break;
                case 4: // 4. l/W, h/L, w/H
                    $item_width = $product_length;
                    $item_length = $product_height;
                    $item_height = $product_width;
                    break;
                case 5: // 5. h/W, w/L, l/H
                    $item_width = $product_height;
                    $item_length = $product_width;
                    $item_height = $product_length;
                    break;
                case 6: // 6. h/W, l/L, w/H
                    $item_width = $product_height;
                    $item_length = $product_length;
                    $item_height = $product_width;
                    break;
                default:
                    $item_width = 0;
                    $item_length = 0;
                    $item_height = 0;
            }

            if ($item_width == 0 || $item_length == 0 || $item_height == 0) return false; // 产品数据出错

            $added = false; // 判断是否已放入箱子
            foreach ($box_space as $key => $space) {
                if ($space->width >= $item_width && $space->length >= $item_length && $space->height >= $item_height) { // 物体可放入该空间
                    $added = true;
                    $width_rest = $space->width - $item_width; // 剩余宽度
                    $length_rest = $space->length - $item_length; // 剩余长度
                    $height_rest = $space->height - $item_height; // 剩余高度
                    $spaces_rest = []; // 剩余空间
                    // 按照剩余宽度和剩余长度的大小来决定前空间和右空间谁占用更大的空间
                    if ($width_rest <= 0 && $length_rest <= 0) {
                        // 物体正好撑满长和宽，无前和右空间可分割
                    } elseif ($width_rest <= 0) { // 物体撑满宽
                        $space_right = new BoxSpace($space->x + $item_length, $space->y, $space->z, $space->width, $length_rest, $space->height);
                        $spaces_rest[] = $space_right;
                    } elseif ($length_rest <= 0) { // 物体撑满长
                        $space_front = new BoxSpace($space->x, $space->y + $item_width, $space->z, $width_rest, $space->length, $space->height);
                        $spaces_rest[] = $space_front;
                    } else {
                        if ($width_rest > $length_rest) { // 剩余宽 > 剩余长
                            $space_front = new BoxSpace($space->x, $space->y + $item_width, $space->z, $width_rest, $space->length, $space->height);
                            $space_right = new BoxSpace($space->x + $item_length, $space->y, $space->z, $item_width, $length_rest, $space->height);
                            $spaces_rest[] = $space_front;
                            $spaces_rest[] = $space_right;
                        } else { // 剩余宽 <= 剩余长
                            $space_front = new BoxSpace($space->x, $space->y + $item_width, $space->z, $width_rest, $item_length, $space->height);
                            $space_right = new BoxSpace($space->x + $item_length, $space->y, $space->z, $space->width, $length_rest, $space->height);
                            $spaces_rest[] = $space_right;
                            $spaces_rest[] = $space_front;
                        }
                    }
                    // 上空间
                    if ($height_rest > 0) {
                        $space_top = new BoxSpace($space->x, $space->y, $space->z + $item_height, $item_width, $item_length, $height_rest);
                        $spaces_rest[] = $space_top;
                    }

                    // 将新空间替换掉原本的空间
                    array_splice($box_space, $key, 1, $spaces_rest);

                    break;
                }
            }

            // 未装入箱子
            if (!$added) {
                // 暂且不合并剩余空间
                return false;
            }
        }

        return true;
    }

    // 选择基因方式1：按照适应值概率
    // 返回包含两个基因的数组
    protected function pick_gene1($gene_group, $gene_count, $total_adaptive_value) {
        $key1_rate = $this->mt_rand_float();
        $key2_rate = $this->mt_rand_float();
        $key1_adaptive_value = $key1_rate * $total_adaptive_value;
        $key2_adaptive_value = $key2_rate * $total_adaptive_value;
        $picked_gene = [];
        foreach ($gene_group as $gene) {
            $adaptive_value = end($gene);
            if ($key1_adaptive_value > 0) {
                $key1_adaptive_value -= $adaptive_value;
                if ($key1_adaptive_value <= 0) $picked_gene[] = $gene;
            }
            if ($key2_adaptive_value > 0) {
                $key2_adaptive_value -= $adaptive_value;
                if ($key2_adaptive_value <= 0) $picked_gene[] = $gene;
            }
            if (count($picked_gene) == 2) break;
        }
        return $picked_gene;
    }

    // 选择基因方式2：平均选择，所有基因概率相同
    // 返回包含两个基因的数组
    protected function pick_gene2($gene_group, $gene_count, $total_adaptive_value) {
        $key1 = mt_rand(0, $gene_count - 1);
        while(($key2 = mt_rand(0, $gene_count - 1)) == $key1);
        return [$gene_group[$key1], $gene_group[$key2]];
    }

    // 生成随机小数，默认0-1之间
    protected function mt_rand_float($min=0, $max=1) {
        return $min + mt_rand()/mt_getrandmax() * ($max-$min);
    }

    // 获取所有箱子种类
    protected function get_box_types() {
        $this->load->model('extension/total/box_type');
        $box_types = $this->model_extension_total_box_type->getBoxTypes();
        if (empty($box_types)) return null;
        // 原索引是箱子名，转成数字，并且计算体积
        $boxes = [];
        foreach ($box_types as $key => $box) {
            $box['name'] = $key;
            $box['volume'] = $box['length'] * $box['width'] * $box['height'];
            $boxes[] = $box;
        }
        // 使箱子按体积从大到小排列
        usort($boxes, function($box1, $box2) {
            if ($box1['volume'] == $box2['volume']) return 0;
            return $box1['volume'] < $box2['volume'] ? 1 : -1;
        });
        return $boxes;
    }

    // 纠正重量单位和长度单位，并计算体积
    protected function correct_unit(&$products) {
        foreach ($products as $key => $product) {
            $products[$key]['weight'] = $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get("config_weight_class_id"));
            $products[$key]['weight_class_id'] = $this->config->get("config_weight_class_id");
            $products[$key]['length'] = $this->length->convert((float)$product['length'], $product['length_class_id'], $this->config->get("config_length_class_id"));
            $products[$key]['width'] = $this->length->convert((float)$product['width'], $product['length_class_id'], $this->config->get("config_length_class_id"));
            $products[$key]['height'] = $this->length->convert((float)$product['height'], $product['length_class_id'], $this->config->get("config_length_class_id"));
            $products[$key]['length_class_id'] = $this->config->get("config_length_class_id");
            $products[$key]['volume'] = $product['length'] * $product['width'] * $product['height'];
        }
    }

    // 将group中产品数量大于1的分成不同的产品
    protected function order_split_products($group) {
        $new_group = [];
        foreach ($group as $product) {
            $quantity = (int)$product['quantity'];
            $product['quantity'] = 1;
            for (; $quantity > 0; $quantity--) {
                $new_product = $product;
                $new_group[] = $new_product;
            }
        }

        return $new_group;
    }

    // 分箱后合并同一产品的数量
    protected function box_combine_same_product($order_split, $box_types) {
        //var_dump($order_split[0][0][0]);

        // 获取所有箱子种类
        if ($box_types == null) {
            $box_types = $this->get_box_types();
        }
        $box_types_count = count($box_types);

        $new_order = []; // [（路线1，箱子1，产品详情1，……，产品详情n），……，（路线m，箱子m，产品详情1，……，产品详情n））]
        $key_offset = 2; // 在新整理的箱子中路线和箱子要占用索引0和1，因此产品索引要往后推2
        foreach ($order_split as $mix_group) { // 按能否混装分开的组
            $products_group = $mix_group[0]; // 产品详情组
            $boxes = $mix_group[1]; // 分箱结果
            $route = $boxes[0]; // 路线
            array_pop($boxes); // 去掉最后的适应值
            array_shift($boxes); // 去掉第一个路线
            foreach ($boxes as $box) {
                $box_id = $box[0][2] % $box_types_count;
                $box_name = $box_types[$box_id]['name'];
                $new_box = [0 => $route, 1 => $box_name];
                foreach ($box as $unit) {
                    $product_id = $unit[0]; // 产品详情组中的索引
                    //$direction = $unit[1];
                    //$box_id = $unit[2];
                    $product = $products_group[$product_id];
                    $in_box_id = $product['product_id'] + $key_offset;
                    if (isset($new_box[$in_box_id])) {
                        $new_box[$in_box_id]['quantity'] += 1;
                    } else {
                        $new_box[$in_box_id] = $product;
                        $new_box[$in_box_id]['quantity'] = 1;
                    }
                }
                $new_order[] = $new_box;
            }
        }

        return $new_order;
    }

    // 产品绑定能够放得下的箱子以及能够放入该箱子的方向
    // $product['choice'] = [ 箱子种类索引 => 可选方向组 ]
    // $product['choice_count'] = 可选箱子的数量
    /* 方向定义：
     * 大写为箱子属性，小写是产品属性
     * 1. w/W, l/L, h/H
     * 2. w/W, h/L, l/H
     * 3. l/W, w/L, h/H
     * 4. l/W, h/L, w/H
     * 5. h/W, w/L, l/H
     * 6. h/W, l/L, w/H
     */
    // 返回是否成功
    protected function check_box_and_direction(&$products, $boxes) {
        foreach ($products as $key => $product) {
            $l = $product['length'];
            $w = $product['width'];
            $h = $product['height'];
            $v = $product['volume'];
            $choice = [];
            foreach ($boxes as $box_key => $box) {
                $directions = range(1, 6);
                $L = $box['length'];
                $W = $box['width'];
                $H = $box['height'];
                $V = $box['volume'];
                if ($v > $V) continue;
                if ($w > $W || $l > $L || $h > $H) {
                    unset($directions[0]);
                }
                if ($w > $W || $h > $L || $l > $H) {
                    unset($directions[1]);
                }
                if ($l > $W || $w > $L || $h > $H) {
                    unset($directions[2]);
                }
                if ($l > $W || $h > $L || $w > $H) {
                    unset($directions[3]);
                }
                if ($h > $W || $w > $L || $l > $H) {
                    unset($directions[4]);
                }
                if ($h > $W || $l > $L || $w > $H) {
                    unset($directions[5]);
                }
                if (!empty($directions)) {
                    $choice[$box_key] = $directions;
                }
            }
            if (empty($choice)) return false;
            $products[$key]['choice'] = $choice;
            $products[$key]['choice_count'] = count($choice);
        }

        return true;
    }
}

// 箱子剩余空间类
class BoxSpace {
    public $x;
    public $y;
    public $z;
    public $length;
    public $width;
    public $height;

    public function __construct($x, $y, $z, $w, $l, $h) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->length = $l;
        $this->width = $w;
        $this->height = $h;
    }
}