<?php
namespace Opencart\catalog\controller\api;
/**
 * Class Reward
 *
 * @package Opencart\Catalog\Controller\Api
 */
class Reward extends \Opencart\System\Engine\Controller {
	/**
	 * @return void
	 */
	public function index(): void {
		$this->load->language('api/reward');

		$json = [];

		if (isset($this->request->post['reward'])) {
			$reward = abs((int)$this->request->post['reward']);
		} else {
			$reward = 0;
		}

		$available = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		if ($reward) {
			if ($reward > $available) {
				$json['error'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
			}

			if ($reward > $points_total) {
				$json['error'] = sprintf($this->language->get('error_maximum'), $points_total);
			}
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_success');

			$this->session->data['reward'] = $reward;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Remove
	 *
	 * @return void
	 */
	public function remove(): void {
		$this->load->language('api/reward');

		$json['success'] = $this->language->get('text_remove');

		unset($this->session->data['reward']);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Maximum
	 *
	 * @return void
	 */
	public function maximum(): void {
		$this->load->language('api/reward');

		$json = [];

		$json['maximum'] = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$json['maximum'] += $product['points'];
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Available
	 *
	 * @return void
	 */
	public function available(): void {
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(['points' => $this->customer->getRewardPoints()]));
	}
}
