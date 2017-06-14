<?php
class ModelExtensionPaymentLemonway extends Model {

    public function getMethod($address, $total) {

        $this->load->language('extension/payment/lemonway');

        $method_data = array();

            $method_data = array(
                'code'       => 'lemonway',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' =>''
            );



        return $method_data;
    }



    public function getCustomerCard($id_customer){

        $query = 'SELECT * FROM `' . DB_PREFIX . 'lemonway_oneclic` lo WHERE lo.`id_customer` = '
            . (int)$this->db->escape($id_customer) . '';
        $current_card = $this->db->query($query);

        return $current_card->row;
    }




    public function insertOrUpdateCard($id_customer, $data){

        $oldCard = $this->getCustomerCard($id_customer);

        if (!empty($oldCard['card_num'])) {
            $oldCard['id_oneclic'] = (int)$oldCard['id_oneclic'];
            $data = array_merge($oldCard, $data);
            $data['date_upd'] = date('Y-m-d H:i:s');
        } else {
            $data['date_add'] = date('Y-m-d H:i:s');
        }

        // Escape data
        foreach ($data as $key => $value) {
            $data[$key] = $this->db->escape($value);
        }
        $data['id_customer'] = (int)$data['id_customer'];
        $data['id_card'] = (int)$data['id_card'];

        if (empty($data['card_num'])) {
            $query = 'INSERT  INTO  `' . DB_PREFIX . 'lemonway_oneclic`  (`id_customer`,`id_card`,`date_add`) values ( ' . $this->db->escape($data['id_customer']) . ', ' . $this->db->escape($data['id_card']) . ',\'' . $this->db->escape($data['date_add']) . '\')';
        } else {
            $query = 'REPLACE INTO  `' . DB_PREFIX . 'lemonway_oneclic`  (`id_oneclic`,`id_customer`,`id_card`,`card_num`, `card_exp` ,`card_type`,`date_add`,`date_upd`) values (' . $data['id_oneclic'] . ',' . $data['id_customer'] . ',' . $data['id_card'] . ',\'' . $data['card_num'] . '\',\'' . $data['card_exp'] . '\',\'' . $data['card_type'] . '\',\'' . $data['date_add'] . '\',\'' . $data['date_upd'] . '\')';
        }

        $this->db->query($query);

    }


    public function getCardId(){



        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '0' AND customer_id = '" . $this->db->escape($this->customer->getId()) . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' order by  date_added desc  limit 1");

        return $cart_query->rows['0']['cart_id'];

    }


    public function generateUniqueCartId($id_cart) {
        return $id_cart . "-" . time() . "-" . uniqid();
    }


    public function getWkToken($id_cart){


        return $this->db->query(
            'SELECT `wktoken` FROM `' . DB_PREFIX . 'lemonway_wktoken` lw WHERE lw.`id_cart` = ' . (int)$this->db->escape($id_cart)
        );
    }

    public function checkIfCartHasWkToken($id_cart){
        return (bool)$this->getWkToken($id_cart)->num_rows;
    }





    public function saveWkToken($id_cart){

        $wkToken = $this->generateUniqueCartId($id_cart);

        //Default  update query
        $query = 'UPDATE `' . DB_PREFIX . 'lemonway_wktoken` SET `wktoken` = \'' . $this->db->escape($wkToken) .
            "' WHERE `id_cart` = " . (int)$this->db->escape($id_cart);


        //If cart haven't wkToken we insert it
        if (!$this->checkIfCartHasWkToken($id_cart)) {
            $query = 'INSERT INTO `' . DB_PREFIX . 'lemonway_wktoken` (`id_cart`, `wktoken`) VALUES (\''
                . (int)$this->db->escape($id_cart) . '\',\'' . $this->db->escape($wkToken) . '\') ';
        }


        $this->db->query($query);

        return $wkToken;
    }



    public function getCartIdFromToken($wktoken){

        if ($id_cart = $this->db->query(
            'SELECT `id_cart` FROM `' . DB_PREFIX . 'lemonway_wktoken` lw WHERE lw.`wktoken` = \''
            . $this->db->escape($wktoken) . "'"
        )
        ) {

            return $id_cart;
        } else {
            $this->session->data['error'] = $this->language->get('error_card_not_found');
            return false;
        }
    }





}