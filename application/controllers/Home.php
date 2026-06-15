<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Bank_model');
	}

	public function index()
	{
		$data['title'] = 'Bank | Home Page';
		$data['settings'] = array();

		$this->load->view('home',$data);
	}

	public function account_register()
	{
		$holder_name    = $_POST['holder_name'];
		$email          = $_POST['email'];
		$password       = $_POST['password'];
		$phone          = $_POST['phone'];

		$row = $this->db->select_max('id')->get('customers')->row();
    	$last_id = !empty($row->id) ? $row->id : 0;
		$new_id = (int)$last_id + 1;
		$padded = str_pad($new_id, 8, '0', STR_PAD_LEFT);
		$account_no = '250590'.$padded;

		$data = array(
			'name'         => $holder_name,
			'email'        => $email,
			'password'     => md5($password),
			'phone'        => $phone,
			'created_at'   => date('Y-m-d H:i:s')
		);
		$this->db->insert('customers',$data);
		$ins_id = $this->db->insert_id();

		if($ins_id)
		{
			$acc_data = array(
				'branch_id'       => 1,
				'customer_id'     => $ins_id,
				'acc_no'          => $account_no,
				'balance'         => 0,
				'created_at'      => date('Y-m-d H:i:s')
			);
			$this->db->insert('accounts', $acc_data);
		}

		$login_data = array(
			'login_id'     => $ins_id,
			'name'         => $holder_name,
			'email'        => $email,
			'phone'        => $phone,
			'acc_no'       => $account_no,
		);

		$this->session->set_userdata('login_data',$login_data);
		$this->session->set_userdata('is_login',true);
		echo 'ok';
	}

	public function account_login()
	{
		$email    = $this->input->post('username');
	    $password = $this->input->post('password');

	    // DB se user fetch karo
	    $user = $this->db->select('c.*,a.acc_no')->from('customers c')->join('accounts a','c.id = a.customer_id','left')->where('email', $email)->get()->row();

	    if($user)
	    {
	        if($user->password == md5($password))
	        {
	            $login_data = array(
	                'login_id' => $user->id,
	                'name'     => $user->name,
	                'email'    => $user->email,
	                'phone'    => $user->phone,
	                'acc_no'   => $user->acc_no,
	            );

	            $this->session->set_userdata('login_data', $login_data);
	            $this->session->set_userdata('is_login', true);
	            echo "ok";
	        }
	        else
	        {
	            echo "Invalid password";
	        }
	    }
	    else
	    {
	        echo "User not found";
	    }
	}

	public function get_balance()
	{
	    $login_data = $this->session->userdata('login_data');
	    $acc_no = $login_data['acc_no'];
	    $balance = $this->Bank_model->get_balance($acc_no);
	    echo $balance;
	}

	public function deposit_money()
	{
		//echo "<pre>"; print_r($_POST); die;
	    $amount = (float)$this->input->post('amount');
	    $type   = (int)$this->input->post('type');

	    $login_data = $this->session->userdata('login_data');
	    $acc_no     = $login_data['acc_no'];

	    $account_id = getAccountHolderId($acc_no);
	    //echo "<pre>"; print_r(array('acc_no'=>$acc_no,'acc_id'=>$account_id)); die;

	    if(!$account_id || $amount <= 0 || !in_array($type, [1,2]))
	    {
	        echo "Invalid request.";
	        return;
	    }

	    $this->db->trans_start();
	    $status = $this->Bank_model->deposit($acc_no, $amount, $type);

	    if($status)
	    {
	        $trans_data = array(
	            'sender_id'   => $account_id,
	            'reciever_id' => $account_id,
	            'amount'      => $amount,
	            'type'        => $type,
	            'created_at'  => date('Y-m-d H:i:s')
	        );

	        $this->Bank_model->add_transaction($trans_data);
	    }
	    $this->db->trans_complete();

	    if($this->db->trans_status() && $status)
	    {
	        echo ($type == 1) ? "₹$amount deposited successfully." : "₹$amount withdrawn successfully.";
	    }
	    else
	    {
	        echo ($type == 2) ? "Withdraw failed (Insufficient balance or error)." : "Deposit failed.";
	    }
	}

	public function send_money()
	{
		$status = false;
	    $from_acc = $this->session->userdata('login_data')['acc_no'];
	    $to_acc = $this->input->post('to_account');
	    $amount = $this->input->post('amount');
	    $sender_id = getAccountHolderId($from_acc);
	    $reciever_id = getAccountHolderId($to_acc);

	    if($sender_id && $reciever_id)
	    {
	    	$trans_data = array(
	    		'sender_id'       => $sender_id,
	    		'reciever_id'     => $reciever_id,
	    		'amount'          => $amount,
	            'type'            => 3,
				'created_at'      => date('Y-m-d H:i:s')
	    	);
	    	$this->Bank_model->add_transaction($trans_data);
	    	$status = $this->Bank_model->money_transfer($from_acc, $to_acc, $amount);
	    }
	    echo $status ? "₹$amount sent to $to_acc." : "Transfer failed.";
	}

	public function account_logout()
	{
		$logout = $this->input->post('logout');
		if($logout == 'ok')
		{
			$this->session->unset_userdata('login_data');
			$this->session->unset_userdata('is_login');

			echo "ok";
		}
	}

}
