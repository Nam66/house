<?php 
include 'db_connect.php';
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM payments where id= ".$_GET['id'])->fetch_array();
foreach($qry as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<form action="momo.php" id="calculate-payment" enctype="application/x-www-form-urlencoded">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Water Number</label>
				<input type="number" class="form-control" name="water_number"  value="<?=$qry['water_number']?>" disabled>
			</div>
            <div class="col-md-4">
				<label for="" class="control-label">Electricity Number</label>
				<input type="number" class="form-control" name="electricity_number"  value="<?=$qry['electricity_number']?>" disabled>
			</div>
		</div>
		<div class="modal-footer">
        <button type="submit" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger" name="momo" id="momo">Pay</button>
      </div>
	</form>
</div>