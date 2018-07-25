<?php
class ModelExtensionPaymentLemonway extends Model
{   
    private function prefix() {
        return (version_compare(VERSION, '3.0', '>=')) ? 'payment_' :  '';
    }

    /*
    This function is required for OpenCart to show the method in the checkout page
    */
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/lemonway');

        $mode = $this->config->get($this->prefix() . 'lemonway_is_test_mode') ? " (Test)" : "";

        $method_data = array(
            'code'       => 'lemonway',
            'title'      => $this->language->get('text_card') . $mode,
            'terms'      => '',
            'sort_order' => '' /*TODO: $this->config->get($this->prefix() . 'lemonway_cc_sort_order')*/
        );

        return $method_data;
    }

    /*
    Get the saved card of a customer
    */
    public function getCustomerCard($customerId)
    {
        return $this->db->query(
            "SELECT * 
            FROM `" . DB_PREFIX . "lemonway_oneclick` lo 
            WHERE lo.`customer_id` = " . (int)$this->db->escape($customerId)
        )->row;
    }

    /*
    Save or update the card of a customer
    */
    public function insertOrUpdateCard($data)
    {
        $oldCard = $this->getCustomerCard($data['customer_id']);

        if (!$oldCard) {
            // If no card saved
            $data['date_add'] = date('Y-m-d H:i:s');
            $query = "INSERT INTO `" . DB_PREFIX . "lemonway_oneclick` (`customer_id`, `card_id`, `date_add`) 
                VALUES ( " .
                    (int)$data['customer_id'] . ", " .
                    (int)$data['card_id'] . ", 
                    '" . $this->db->escape($data['date_add']) . "'
                )";
        } else {
            // If the client has already saved a card => Update
            $data = array_merge($oldCard, $data);
            $data['date_upd'] = date('Y-m-d H:i:s');
            $query = "REPLACE INTO `" . DB_PREFIX . "lemonway_oneclick` (`id`, `customer_id`, `card_id`, `card_num`, `card_exp`, `card_type`, `date_add`, `date_upd`) 
                VALUES (" .
                    (int)$this->db->escape($data['id']) . ", " .
                    (int)$this->db->escape($data['customer_id']) . ", " .
                    (int)$this->db->escape($data['card_id']) . ", 
                    '" . $this->db->escape($data['card_num']) . "', 
                    '" . $this->db->escape($data['card_exp']) . "', 
                    '" . $this->db->escape($data['card_type']) . "', 
                    '" . $this->db->escape($data['date_add']) . "', 
                    '" . $this->db->escape($data['date_upd']) . "'
                )";
        }

        $this->db->query($query);
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
