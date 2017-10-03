<?php
class ControllerExtensionModuleSlideshow extends Controller {
	public function index($setting) {
		static $module = 0;		

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/javascript/jquery/owl-carousel/owl.carousel.css');
		$this->document->addScript('catalog/view/javascript/jquery/owl-carousel/owl.carousel.min.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/home_tracking.css');

		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;

		$data['width'] = $setting['width'];
		$data['height'] = $setting['height'];

		$this->load->language('common/search');
		$data['text_track_ad1'] = $this->language->get('text_track_ad1');
		$data['text_track_ad2'] = $this->language->get('text_track_ad2');
		$data['text_track_ad3'] = $this->language->get('text_track_ad3');
		$data['text_track_ad4'] = $this->language->get('text_track_ad4');
		$data['text_track_label'] = $this->language->get('text_track_label');
		$data['text_track_placeholder'] = $this->language->get('text_track_placeholder');
		$data['text_track_btn'] = $this->language->get('text_track_btn');

		$this->load->language('common/home_mid_btn');
		$data['text_home_mid_btn_service'] = $this->language->get('text_home_mid_btn_service');
		$data['text_home_mid_btn_contact'] = $this->language->get('text_home_mid_btn_contact');
		$data['text_home_mid_btn_order'] = $this->language->get('text_home_mid_btn_order');
		$data['text_home_mid_btn_member'] = $this->language->get('text_home_mid_btn_member');

		return $this->load->view('extension/module/slideshow', $data);
	}
}
