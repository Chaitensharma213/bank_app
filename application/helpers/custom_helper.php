<?php
function getAccountHolderId($acc_no)
{
    $CI =& get_instance();

    $CI->db->select('customer_id');
    $CI->db->from('accounts');
    $CI->db->where('acc_no', $acc_no);
    $query = $CI->db->get();

    $result = $query->row();

    return ($result) ? $result->customer_id : null;
}


?>