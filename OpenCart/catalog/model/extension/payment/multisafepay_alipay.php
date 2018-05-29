<?php
/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the MultiSafepay plugin
 * to newer versions in the future. If you wish to customize the plugin for your
 * needs please document your changes and make backups before you update.
 *
 * @category    MultiSafepay
 * @package     Connect
 * @author      TechSupport <techsupport@multisafepay.com>
 * @copyright   Copyright (c) 2017 MultiSafepay, Inc. (http://www.multisafepay.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class ModelExtensionPaymentMultiSafePayAlipay extends Model
{

    public function getMethod($address, $total)
    {
        if ($total == 0) {
            return false;
        }

        $this->load->language('extension/payment/multisafepay');
        $storeid = $this->config->get('config_store_id');

        if ($storeid == 0) {
            $appendix = '';
        }else{
            $appendix = '_' . $storeid;
        }

        $totalcents = $total * 100;

        if ($total) {
            if ($this->config->get('payment_multisafepay_alipay_min_amount' . $appendix) && $totalcents < $this->config->get('payment_multisafepay_alipay_min_amount' . $appendix)) {
                return false;
            }
            if ($this->config->get('payment_multisafepay_alipay_max_amount' . $appendix) && $totalcents > $this->config->get('payment_multisafepay_alipay_max_amount' . $appendix)) {
                return false;
            }
        }

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('payment_multisafepay_alipay_geo_zone_id' . $appendix) . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('payment_multisafepay_alipay_geo_zone_id' . $appendix)) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'multisafepay_alipay',
                'title' => $this->language->get('text_title_alipay'),
                'terms' => '',
                'sort_order' => $this->config->get('payment_multisafepay_alipay_sort_order' . $appendix)
            );
        }

        return $method_data;
    }

}

?>