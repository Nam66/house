<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
			<form action="" id="manage-house">
				<div class="card">
					<div class="card-header">
						    Phòng
				  	</div>
					<div class="card-body">
							<div class="form-group" id="msg"></div>
							<input type="hidden" name="id">
							<div class="form-group">
								<label class="control-label">Số Phòng</label>
								<input type="text" class="form-control" name="house_no" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Loại Phòng</label>
								<select name="category_id" id="" class="custom-select" required>
									<?php 
									$categories = $conn->query("SELECT * FROM categories order by name asc");
									if($categories->num_rows > 0):
									while($row= $categories->fetch_assoc()) :
									?>
									<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
								<?php endwhile; ?>
								<?php else: ?>
									<option selected="" value="" disabled="">Please check the category list.</option>
								<?php endif; ?>
								</select>
							</div>
							<div class="form-group">
								<label for="" class="control-label">Mô tả</label>
								<textarea name="description" id="" cols="30" rows="4" class="form-control" required></textarea>
							</div>
							<div class="form-group">
								<label class="control-label">Giá</label>
								<input type="number" class="form-control text-right" name="price" step="any" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Tiền Điện/số</label>
								<input type="number" class="form-control text-right" name="electricity_number" step="any" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Tiền nước/số</label>
								<input type="number" class="form-control text-right" name="water_meter" step="any" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Tiền dịch vụ</label>
								<input type="number" class="form-control text-right" name="service_price" step="any" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Tiền wifi</label>
								<input type="number" class="form-control text-right" name="wifi" step="any" required="">
							</div>
							<div class="form-group">
								<label class="control-label">Đồ dùng</label>
								<textarea class="form-control" name="belongings" step="any" required=""></textarea>
							</div>
					</div>
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Lưu</button>
								<button class="btn btn-sm btn-default col-sm-3" type="reset" > Hủy</button>
							</div>
						</div>
					</div>
				</div>
			</form>
			</div>
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<b>Danh sách phòng</b>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">House</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$house = $conn->query("SELECT h.*,c.name as cname FROM houses h inner join categories c on c.id = h.category_id order by id asc");
								while($row=$house->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p>House #: <b><?php echo $row['house_no'] ?></b></p>
										<p><small>loại phòng: <b><?php echo $row['cname'] ?></b></small></p>
										<p><small>mô tả: <b><?php echo $row['description'] ?></b></small></p>
										<p><small>giá: <b><?php echo number_format($row['price'],2) ?></b></small></p>
										<p><small>trạng thái: <b><?php echo ($row['house_status']) ? 'Đã thuê' : 'còn trống' ?></b></small></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_house" type="button" data-id="<?php echo $row['id'] ?>"  data-house_no="<?php echo $row['house_no'] ?>" data-description="<?php echo $row['description'] ?>" data-category_id="<?php echo $row['category_id'] ?>" data-price="<?php echo $row['price'] ?>" data-electricity_number="<?php echo $row['electricity_number'] ?>" data-water_meter="<?php echo $row['water_meter'] ?>" data-service_price="<?php echo $row['service_price'] ?>" data-wifi="<?php echo $row['wifi'] ?>" data-belongings="<?php echo $row['belongings'] ?>" >Edit</button>
										<button class="btn btn-sm btn-danger delete_house" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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
	td p {
		margin: unset;
		padding: unset;
		line-height: 1em;
	}
</style>
<script>
	$('#manage-house').on('reset',function(e){
		$('#msg').html('')
	})
	$('#manage-house').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_house',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
				else if(resp==2){
					$('#msg').html('<div class="alert alert-danger">House number already exist.</div>')
					end_load()
				}
			}
		})
	})
	$('.edit_house').click(function(){
		start_load()
		var cat = $('#manage-house')
		cat.get(0).reset()
		cat.find("[name='id']").val($(this).attr('data-id'))
		cat.find("[name='house_no']").val($(this).attr('data-house_no'))
		cat.find("[name='description']").val($(this).attr('data-description'))
		cat.find("[name='price']").val($(this).attr('data-price'))
		cat.find("[name='electricity_number']").val($(this).attr('data-electricity_number'))
		cat.find("[name='water_meter']").val($(this).attr('data-water_meter'))
		cat.find("[name='electricity_number']").val($(this).attr('data-electricity_number'))
		cat.find("[name='water_meter']").val($(this).attr('data-water_meter'))
		cat.find("[name='service_price']").val($(this).attr('data-service_price'))
		cat.find("[name='wifi']").val($(this).attr('data-wifi'))
		cat.find("[name='belongings']").val($(this).attr('data-belongings'))
		end_load()
	})
	$('.delete_house').click(function(){
		_conf("Are you sure to delete this house?","delete_house",[$(this).attr('data-id')])
	})
	function delete_house($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_house',
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
	$('table').dataTable()
</script>