<?php include 'db_connect.php' ?>

<?php 
$tenants = $conn->query("SELECT t.*,u.name as name,h.house_no,h.price,h.electricity_number,h.water_meter,h.belongings FROM tenants t inner join houses h on h.id = t.house_id inner join users u on u.id = t.user_id where t.status = 1 order by h.house_no desc ")->fetch_array();
foreach($tenants as $k => $v){
	if(!is_numeric($k)){
		$$k = $v;
	}
}
$id = $_GET['id'];
$date = $conn->query("SELECT date_in FROM tenants where id =".$id)->fetch_array()['date_in'];
$house = $tenants = $conn->query("SELECT t.*,h.house_no,h.price,h.electricity_number,h.water_meter,h.belongings FROM tenants t inner join houses h on h.id = t.house_id where t.status = 1 where t.id = $id ")->fetch_array();
$months = abs(strtotime(date('Y-m-d')." 23:59:59") - strtotime($date." 23:59:59"));
$months = floor(($months) / (30*60*60*24));
$payable = $price * $months;
$user = $conn->query("SELECT u.name FROM tenants t inner join users u on t.user_id = u.id where t.id =".$id)->fetch_array()['name'];
$paid = $conn->query("SELECT SUM(total_amount) as paid FROM payments where tenant_id =".$id);
$last_payment = $conn->query("SELECT * FROM payments where tenant_id =".$_GET['id']." order by unix_timestamp(date_created) desc limit 1");
$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
$last_payment_date =  'N/A';
$last_payment_electricity = 'N/A';
$last_payment_water = 'N/A';
if ($last_payment->num_rows > 0) {
	$last_payment= $last_payment->fetch_array();
	$last_payment_electricity = $last_payment['electricity_number'];
	$last_payment_water = $last_payment['water_number'];
}
$outstanding = $payable - $paid;

?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-4">
				<div id="details">
					<large><b>Chi Tiết</b></large>
					<hr>
					<p>Tên khách hàng: <b><?php echo ucwords($user) ?></b></p>
					<p>Tiền nhà hàng tháng: <b><?php echo number_format($price,2) ?></b></p>
					<p>Tiền điện/số: <b><?php echo $house['electricity_number'] ?></b></p>
					<p>Tiền nước/số: <b><?php echo $house['water_meter'] ?></b></p>
					<p>Đồ dùng: <b><?php echo nl2br($house['belongings']) ?></b></p>
					<p>Số điện đã sử dụng: <b><?php echo $last_payment_electricity ?></b></p>
					<p>Số nước: <b><?php echo $last_payment_water ?></b></p>
					<p>Tổng số tiền đã trả: <b><?php echo number_format($paid,2) ?></b></p>
					<p>Ngày bắt đầu thuê: <b><?php echo date("M d, Y",strtotime($date)) ?></b></p>
				</div>
			</div>
			<div class="col-md-8">
				<large><b>Danh sách thanh toán</b></large>
					<hr>
				<table class="table table-condensed table-striped">
					<thead>
						<tr>
							<th>Date</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$payments = $conn->query("SELECT * FROM payments where tenant_id = $id and status = 1");
						if($payments->num_rows > 0):
						while($row=$payments->fetch_assoc()):
						?>
					<tr>
						<td><?php echo date("M d, Y",strtotime($row['date_created'])) ?></td>
						<td class='text-right'><?php echo number_format($row['total_amount'],2) ?></td>
					</tr>
					<?php endwhile; ?>
					<?php else: ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
        <button type="submit" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
<style>
	#details p {
		margin: unset;
		padding: unset;
		line-height: 1.3em;
	}
	td, th{
		padding: 3px !important;
	}
</style>