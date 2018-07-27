<?php

class ModelExtensionPaymentLemonwayIdeal extends Model
{
    private function prefix()
    {
        return (version_compare(VERSION, '3.0', '>=')) ? 'payment_' : '';
    }

    /*
    This function is required for OpenCart to show the method in the checkout page
    */
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/lemonway');

        $mode = $this->config->get($this->prefix() . 'lemonway_is_test_mode') ? " (Test)" : "";

        $method_data = array(
            'code' => 'lemonway_ideal',
            'title' => $this->language->get('text_ideal'),
            'terms' => '',
            'sort_order' => '' /*TODO: $this->config->get($this->prefix() . 'lemonway_cc_sort_order')*/
        );

        return $method_data;
    }


    /*
    Private function to generate a random wkToken
    */
    private function generateUniqueToken($order_id)
    {
        return $order_id . "-" . time() . "-" . uniqid();
    }

    /*
    Check if this order has a wkToken
    */
    private function checkIfOrderHasWkToken($order_id)
    {
        return (bool)$this->db->query(
            "SELECT `wktoken` 
            FROM `" . DB_PREFIX . "lemonway_wktoken` lw 
            WHERE lw.`order_id` = " . (int)$this->db->escape($order_id)
        )->num_rows;
    }

    /*
    Associate a random wkToken for an order
    */
    public function saveWkToken($order_id, $registerCard)
    {
        $wkToken = $this->generateUniqueToken($order_id);

        // Whether the order has a wkToken
        if ($this->checkIfOrderHasWkToken($order_id)) {
            $query = "UPDATE `" . DB_PREFIX . "lemonway_wktoken` SET 
                `wktoken` = '" . $this->db->escape($wkToken) . "', 
                `register_card` = " . (int)$this->db->escape($registerCard) . " 
                WHERE `order_id` = " . (int)$this->db->escape($order_id);
        } else {
            $query = "INSERT INTO `" . DB_PREFIX . "lemonway_wktoken` (`order_id`, `wktoken`, `register_card`) 
                VALUES (
                    " . (int)$this->db->escape($order_id) . ", 
                    '" . $this->db->escape($wkToken) . "', 
                    " . (int)$this->db->escape($registerCard) . "
                )";
        }

        $this->db->query($query);

        return $wkToken;
    }

//    /*
//   Get the saved card of a customer
//   */
//    public function getCustomerCard($customerId)
//    {
//        var_dump($customerId);
//        return $this->db->query(
//            "SELECT *
//            FROM `" . DB_PREFIX . "lemonway_oneclick` lo
//            WHERE lo.`customer_id` = " . (int)$this->db->escape($customerId)
//        )->row;
//    }

    /*
    Get the order ID by its unique wkToken
    */
    public function getOrderIdFromToken($wkToken)
    {
        $order_id = $this->db->query(
            "SELECT `order_id` 
            FROM `" . DB_PREFIX . "lemonway_wktoken` lw 
            WHERE lw.`wktoken` = '" . $this->db->escape($wkToken) . "'"
        )->row['order_id'];
//        var_dump($wkToken);

        return $order_id;
    }

    /*
    Check if the user has chosen to save a card for this order by using the wkToken
    */
    public function getRegisterCardFromToken($wkToken)
    {
        $registerCard = $this->db->query(
            "SELECT `register_card` 
            FROM `" . DB_PREFIX . "lemonway_wktoken` lw 
            WHERE lw.`wktoken` = '" . $this->db->escape($wkToken) . "'"
        )->row['register_card'];

        return (bool)$registerCard;
    }
}
