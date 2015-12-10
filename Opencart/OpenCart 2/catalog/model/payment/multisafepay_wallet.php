<?php

class ModelPaymentMultiSafePayWallet extends Model {

    public function getMethod($address, $total) {
        if($total == 0){
	        return false;
        }
        $this->load->language('payment/multisafepay');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('multisafepay_wallet_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        /* if ($this->config->get('multisafepay_total') > 0 && $this->config->get('multisafepay_total') > $total) {
          $status = false;
          } else */
        if (!$this->config->get('multisafepay_wallet_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $totalcents = $total * 100;

        if ($total) {
            if ($this->config->get('multisafepay_wallet_min_amount') && $totalcents < $this->config->get('multisafepay_wallet_min_amount')) {
                return false;
            }
            if ($this->config->get('multisafepay_wallet_max_amount') && $totalcents > $this->config->get('multisafepay_wallet_max_amount')) {
                return false;
            }
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'multisafepay_wallet',
                'title' => $this->language->get('text_title_wallet'),
                'terms' => '',
                'sort_order' => $this->config->get('multisafepay_wallet_sort_order')
            );
        }

        return $method_data;
    }

}

?>