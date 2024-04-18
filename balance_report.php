<?php include 'db_connect.php' ?>
<style>
	.on-print{
		display: none;
	}
</style>
<noscript>
	<style>
		.text-center{
			text-align:center;
		}
		.text-right{
			text-align:right;
		}
		table{
			width: 100%;
			border-collapse: collapse
		}
		tr,td,th{
			border:1px solid black;
		}
	</style>
</noscript>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<div class="col-md-12">
					<hr>
						<div class="row">
							<div class="col-md-12 mb-2">
							<button class="btn btn-sm btn-block btn-success col-md-2 ml-1 float-right" type="button" id="print"><i class="fa fa-print"></i> Print</button>
							</div>
						</div>
					<div id="report">
						<div class="on-print">
							 <p><center>Rental Balances Report</center></p>
							 <p><center>As of <b><?php echo date('F ,Y') ?></b></center></p>
						</div>
						<div class="row">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>Tenant</th>
										<th>House #</th>
										<th>Monthly Rate</th>
										<th>Total Amount</th>
										<th>Last Payment Paid</th>
										<th>Last Payment</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$i = 1;
									// $tamount = 0;
									$tenants =$conn->query("SELECT t.*, u.name as name,h.house_no,h.price FROM tenants t inner join houses h on h.id = t.house_id inner join users u on t.user_id = u.id where t.status = 1 order by h.house_no desc ");
									if($tenants->num_rows > 0):
									while($row=$tenants->fetch_assoc()):
										$paid = $conn->query("SELECT SUM(total_amount) as paid FROM payments where status = 1 and tenant_id =".$row['id']);
										if (!empty($paid->fetch_array()['paid'])):
											print_r($paid->fetch_array());
											$months = abs(strtotime(date('Y-m-d')." 23:59:59") - strtotime($row['date_in']." 23:59:59"));
											$months = floor(($months) / (30*60*60*24));
											$payable = $row['price'] * $months;
											$last_payment = $conn->query("SELECT * FROM payments where tenant_id =".$row['id']." and status = 1 order by unix_timestamp(date_created) desc limit 1");
											$last_paymentCheck = $last_payment->fetch_array();
											$paid = $paid->num_rows > 0 ? $last_paymentCheck['total_amount'] : 0;
											$last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_paymentCheck['date_created'])) : 'N/A';
											$outstanding = $payable - $paid;
									?>
									<tr>
										<td><?php echo $i++ ?></td>
										<td><?php echo ucwords($row['name']) ?></td>
										<td><?php echo $row['house_no'] ?></td>
										<td class="text-right"><?php echo number_format($row['price'],2) ?></td>
										<td class="text-right"><?php echo number_format($paid,2) ?></td>
										<td class="text-right"><?php echo number_format($outstanding,2) ?></td>
										<td><?php echo date('M d,Y',strtotime($last_payment)) ?></td>
									</tr>
									<?php endif;?>
								<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<th colspan="9"><center>No Data.</center></th>
									</tr>
								<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('#print').click(function(){
		var _style = $('noscript').clone()
		var _content = $('#report').clone()
		var nw = window.open("","_blank","width=800,height=700");
		nw.document.write(_style.html())
		nw.document.write(_content.html())
		nw.document.close()
		nw.print()
		setTimeout(function(){
		nw.close()
		},500)
	})
	$('#filter-report').submit(function(e){
		e.preventDefault()
		location.href = 'index.php?page=payment_report&'+$(this).serialize()
	})
</script>