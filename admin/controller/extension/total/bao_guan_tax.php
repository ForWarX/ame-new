<?php
class ControllerExtensionTotalBaoGuanTax extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/total/bao_guan_tax');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('bao_guan_tax', $this->request->post);
            
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=total', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_tax'] = $this->language->get('entry_tax');
        $data['entry_rate'] = $this->language->get('entry_rate');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=total', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/total/bao_guan_tax', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/total/bao_guan_tax', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=total', true);

        if (isset($this->request->post['bao_guan_tax_rate'])) {
            $data['bao_guan_tax_rate'] = $this->request->post['bao_guan_tax_rate'];
        } else {
            $data['bao_guan_tax_rate'] = $this->config->get('bao_guan_tax_rate');
        }

        if (isset($this->request->post['bao_guan_tax'])) {
            $data['bao_guan_tax'] = $this->request->post['bao_guan_tax'];
        } else {
            $data['bao_guan_tax'] = $this->config->get('bao_guan_tax');
        }

        if (isset($this->request->post['bao_guan_tax_status'])) {
            $data['bao_guan_tax_status'] = $this->request->post['bao_guan_tax_status'];
        } else {
            $data['bao_guan_tax_status'] = $this->config->get('bao_guan_tax_status');
        }

        if (isset($this->request->post['bao_guan_tax_sort_order'])) {
            $data['bao_guan_tax_sort_order'] = $this->request->post['bao_guan_tax_sort_order'];
        } else {
            $data['bao_guan_tax_sort_order'] = $this->config->get('bao_guan_tax_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/total/bao_guan_tax', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/total/bao_guan_tax')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}