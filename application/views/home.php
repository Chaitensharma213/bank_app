<?php
$is_login = $this->session->userdata('is_login');
$login_data = array();
if($is_login)
{
	$login_data = $this->session->userdata('login_data');
    //echo "<pre>"; print_r($login_data); die;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $title; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<style>
body {
    background-image: url('<?= base_url("uploads/bank_bg.jpeg") ?>');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    min-height: 100vh;
    margin: 0;
}
.blurred {
    filter: blur(5px);
    transition: filter 0.3s ease;
}
.log_btn{
	background-color: #9b4007;
}
.logout{
	margin-left: 20px;
}
</style>
</head>
<body>

<div id="pageContent">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark" style="background-color: #672800;">
        <div class="container-fluid">
            <a class="navbar-brand" href="javascript:void(0)">Bank App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php
                	if($is_login):
                	$user_name = !empty($login_data) ? $login_data['name'] : 'Guest';
                ?>
                    <li class="nav-item">
                        <a class="text-white" style="text-decoration: none;" href="javascript:void(0)">Welcome! <?= $user_name; ?> </a>
                        <a class="btn text-white log_btn logout" href="javascript:void(0)">Logout</a>
                    </li>
                <?php else: ?>
                	<li class="nav-item">
                        <a class="btn text-white log_btn" href="javascript:void(0)">Login</a>
                    </li>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</div>

<?php if($is_login): ?>
<div class="container mt-5">
    <!-- Check Balance -->
    <div class="card mb-3">
        <div class="card-header bg-info text-white">Account Balance</div>
        <div class="card-body">
            <button class="btn btn-primary" id="checkBalance">Get Balance</button>
            <p class="mt-2" id="balanceDisplay"></p>
        </div>
    </div>

    <!-- Deposit Money -->
    <div class="card mb-3">
        <div class="card-header bg-success text-white">Deposit / Withdraw</div>

        <form id="transactionForm">
            <div class="card-body row">
                
                <div class="col-md-6">
                    <label class="form-label">Transaction Type</label>
                    <select class="form-control" name="type" required>
                        <option value="1">Deposit</option>
                        <option value="2">Withdraw</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Amount</label>
                    <input type="number" class="form-control" name="amount" min="1" required>
                </div>

                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <p class="mt-2" id="transactionMsg"></p>
                </div>

            </div>
        </form>
    </div>

    <!-- Send Money -->
    <div class="card mb-3">
        <div class="card-header bg-warning text-dark">Send Money</div>
        <div class="card-body">
            <form id="sendMoneyForm">
                <div class="mb-2">
                    <label class="form-label">To Account Number</label>
                    <input type="text" class="form-control" name="to_account" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Amount</label>
                    <input type="number" class="form-control" name="amount" required>
                </div>
                <button type="submit" class="btn btn-warning">Send</button>
                <p class="mt-2" id="transferMsg"></p>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>


<div class="modal fade" id="accLoginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Login Required</h5>
            </div>

            <div class="modal-body">

                <!-- Login form -->
                <form id="loginForm" method="post" action="<?= site_url('auth/login') ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Account No./Email/Phone No</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <p>Don't have Account? <a href="javascript:void(0)" id="showCreateAccount">Click here</a></p>
                    <button type="submit" class="btn btn-primary logAcc">Login</button>
                </form>

                <!-- Create Account form (hidden by default) -->
                <form id="createAccountForm" method="post" action="<?= site_url('auth/register') ?>" style="display:none;">
                    <div class="mb-3">
                        <label for="holder_name" class="form-label">A/c Holder Name</label>
                        <input type="text" class="form-control" id="holder_name" name="holder_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="newEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="newEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_no" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone_no" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="newPassword" name="password" required>
                    </div>
                    <p>Already have an account? <a href="javascript:void(0)" id="showLogin">Back to Login</a></p>
                    <button type="submit" class="btn btn-success regAcc">Create Account</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    <?php if(!$this->session->userdata('is_login')): ?>
        const modal = new bootstrap.Modal(document.getElementById('accLoginModal'));
        modal.show();
        $('#pageContent').addClass('blurred');
    <?php else: ?>
        $('#pageContent').removeClass('blurred');
    <?php endif; ?>

    $('#showCreateAccount').click(function() {
        $('#loginForm').hide();
        $('#createAccountForm').show();
        $('#modalTitle').text('Create New Account');
    });

    $('#showLogin').click(function() {
        $('#createAccountForm').hide();
        $('#loginForm').show();
        $('#modalTitle').text('Login Required');
    });
});

$(document).on('submit','#loginForm', function(e){
	e.preventDefault();
	var form = $(this);
	var username = $('#username').val();
	var password = $('#password').val();

	if(username != '' && password != '')
	{
        $('.logAcc').prop('disabled', true);
		$.ajax({
			url: '<?= base_url() ?>Home/account_login',
			method: 'POST',
            data: form.serialize(),
            success: function(data){
            	if(data == 'ok')
            	{
                    $('.logAcc').prop('disabled', false);
            		window.location.href = '<?= base_url().'home' ?>';
            	}
            }
		});
	}
});

$(document).on('submit','#createAccountForm', function(e){
	e.preventDefault();
	var form = $(this);
	var holder_name = $('#holder_name').val();
	var email = $('#newEmail').val();
	var password = $('#newPassword').val();
	var phone_no = $('#phone_no').val();

	if(holder_name != '' && email != '' && password != '' && phone_no != '')
	{
        $('.regAcc').prop('disabled', true);
		$.ajax({
			url: '<?= base_url() ?>Home/account_register',
			method: 'POST',
            data: form.serialize(),
            success: function(data){
            	if(data == 'ok')
            	{
                    $('.regAcc').prop('disabled', false);
            		window.location.href = '<?= base_url().'home' ?>';
            	}
            }
		});
	}
});

// Get Balance
$('#checkBalance').click(function() {
    $.ajax({
        url: '<?= base_url() ?>home/get_balance',
        method: 'GET',
        success: function(response) {
            $('#balanceDisplay').text("Your balance is ₹" + response);
        }
    });
});

// Deposit/Withdraw Money
$('#transactionForm').submit(function(e){
    e.preventDefault();

    $.ajax({
        url: '<?= base_url() ?>home/deposit_money',
        method: 'POST',
        data: $(this).serialize(),
        success: function(data){
            $('#transactionForm')[0].reset();
            $('#transactionMsg').text(data);

            setTimeout(function(){
                $('#transactionMsg').empty();
            }, 2000);
        }
    });
});

// Send Money
$('#sendMoneyForm').submit(function(e){
    e.preventDefault();
    $.ajax({
        url: '<?= base_url() ?>home/send_money',
        method: 'POST',
        data: $(this).serialize(),
        success: function(data){
            $('#sendMoneyForm')[0].reset();
            $('#transferMsg').text(data);
            setTimeout(function(){
                $('#transferMsg').empty();
            },2000);
        }
    });
});

$(document).on('click','.logout', function(){
	$.ajax({
        url: '<?= base_url() ?>home/account_logout',
        method: 'POST',
        data: {logout:'ok'},
        success: function(data){
            if(data == 'ok')
            {
            	window.location.href = '<?= base_url().'home' ?>';
            }
        }
    });
});

</script>

</body>
</html>
