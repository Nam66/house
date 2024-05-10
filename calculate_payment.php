<?php 
include 'db_connect.php'; 
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM tenants where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<form action="" id="calculate-payment">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Water Number</label>
				<input type="number" class="form-control" name="water_number"  value="" required>
			</div>
            <div class="col-md-4">
				<label for="" class="control-label">Electricity Number</label>
				<input type="number" class="form-control" name="electricity_number"  value="" required>
			</div>
		</div>
		<div class="modal-footer">
        <button type="submit" class="btn btn-primary" id='submit' onclick="$('#uni_modal').submit()">Save</button>
        <button type="submit" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
	</form>
</div>
<script>
	
	$('#calculate-payment').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=calculate_payment',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully saved.",'success')
						setTimeout(function(){
							location.reload()
						},1000)
				}
			}
		})
	})
</script>