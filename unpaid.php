<?php include('db_connect.php');?>
<?php
$currentMonth = date('m');
$currentYear = date('Y');
$invoices = $conn->query("SELECT p.*,u.name as name FROM payments p inner join tenants t on t.id = p.tenant_id inner join users u on u.id = t.user_id where MONTH(p.date_created) = $currentMonth and YEAR(p.date_created) = $currentYear");
?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
		<div class="row">
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>Danh sách hợp đồng chưa thanh toán</b>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Date</th>
									<th class="">Tenant</th>
									<th class="">Amount</th>
									<th class="">Status</th>
									<th style="<?=$styleForm?>" class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								while($row=$invoices->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<?php echo date('M d, Y',strtotime($row['date_created'])) ?>
									</td>
									<td class="">
										 <p> <b><?php echo ucwords($row['name']) ?></b></p>
									</td>
									<td class="text-right">
										 <p> <b><?php echo number_format($row['total_amount'],2) ?></b></p>
									</td>
									<td class="text-right">
										 <p> <b><?php echo ($row['status'] ? 'Đã thanh toán' : 'Chưa thanh toán') ?></b></p>
									</td>
									<td class="text-center"  style='<?=$styleForm?>'>
										<button name="momo" id="momo" <?php echo ($row['status'] ? 'disabled' : '') ?>  class="btn btn-sm btn-outline-danger view_calculate" style="<?=$styleForm?>" data-id="<?php echo $row['id'] ?>">Pay</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p{
		margin: unset
	}
	img{
		max-width:100px;
		max-height:150px;
	}
</style>
<script>
	$(document).ready(function(){
		$('table').dataTable()
	})
	
	$('#new_invoice').click(function(){
		uni_modal("New invoice","manage_payment.php","mid-large")
		
	})
	$('.edit_invoice').click(function(){
		uni_modal("Manage invoice Details","manage_payment.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_invoice').click(function(){
		_conf("Are you sure to delete this invoice?","delete_invoice",[$(this).attr('data-id')])
	})

	$('.view_calculate').click(function(){
		uni_modal("Thanh Toán","view_calculate.php?id="+$(this).attr('data-id'),"large")
		
	})
	updateUrl = window.location.href;
	const params = new URLSearchParams(updateUrl);
	if (params.has('message')) {
		let payment_id = params.get('extraData');
		$.ajax({
			url:'ajax.php?action=update_payment',
			method:'POST',
			data:{id:payment_id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
	
	function delete_invoice($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_payment',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>