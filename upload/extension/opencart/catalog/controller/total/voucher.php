<?php
namespace Opencart\Catalog\Controller\Extension\Opencart\Total;
/**
 * Class Voucher
 *
 * @package Opencart\Catalog\Controller\Extension\Opencart\Total
 */
class Voucher extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): string {
		if ($this->config->get('total_voucher_status')) {
			$this->load->language('extension/opencart/total/voucher');

			$data['save'] = $this->url->link('extension/opencart/total/voucher.save', 'language=' . $this->config->get('config_language'), true);
			$data['remove'] = $this->url->link('extension/opencart/total/voucher.remove', 'language=' . $this->config->get('config_language'), true);
			$data['list'] = $this->url->link('checkout/cart.list', 'language=' . $this->config->get('config_language'), true);

			if (isset($this->session->data['voucher'])) {
				$data['voucher'] = $this->session->data['voucher'];
			} else {
				$data['voucher'] = '';
			}

			return $this->load->view('extension/opencart/total/voucher', $data);
		}

		return '';
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/opencart/total/voucher');

		$json = [];

		if (isset($this->request->post['voucher'])) {
			$voucher = (string)$this->request->post['voucher'];
		} else {
			$voucher = '';
		}

		if (!$this->config->get('total_voucher_status')) {
			$json['error'] = $this->language->get('error_status');
		}

		$this->load->model('checkout/voucher');

		$voucher_info = $this->model_checkout_voucher->getVoucher($voucher);

		if (!$voucher_info) {
			$json['error'] = $this->language->get('error_voucher');
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_success');

			$this->session->data['voucher'] = $voucher;

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Remove
	 *
	 * @return void
	 */
	public function remove() {
		$this->load->language('extension/opencart/total/voucher');

		$json = [];

		if (!isset($this->session->data['voucher'])) {
			$json['error'] = $this->language->get('error_remove');
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_remove');

			unset($this->session->data['voucher']);

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
