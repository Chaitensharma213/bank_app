<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Bank_model extends CI_model
{
    public function get_balance($acc_no)
    {
        $query = $this->db->get_where('accounts', ['acc_no' => $acc_no]);
        return $query->row()->balance ?? 0;
    }

    public function deposit($acc_no, $amount, $type = 1)
    {
        $this->db->trans_start();
        $acc_data = $this->db->get_where('accounts', ['acc_no' => $acc_no])->row();

        if(empty($acc_data))
        {
            $this->db->trans_complete();
            return false;
        }
        $current_balance = (float)$acc_data->balance;
        if($type == 1)
        {
            $new_balance = $current_balance + $amount;
        } 
        elseif($type == 2)
        {
            if($current_balance < $amount)
            {
                $this->db->trans_complete();
                return false;
            }
            $new_balance = $current_balance - $amount;
        } 
        else
        {
            $this->db->trans_complete();
            return false;
        }

        $this->db->where('acc_no', $acc_no)->update('accounts', ['balance' => $new_balance]);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function money_transfer($from_acc, $to_acc, $amount)
    {
        $this->db->trans_start();
        $this->db->set('balance', "balance - $amount", FALSE)->where('acc_no', $from_acc)->update('accounts');
        $this->db->set('balance', "balance + $amount", FALSE)->where('acc_no', $to_acc)->update('accounts');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function add_transaction($data)
    {
        $this->db->insert('transactions', $data);
    }

}