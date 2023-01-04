@extends('master')
@section('title','จัดการสินค้า')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
<style type="text/css">
	.fixpad{
		margin-bottom: 10px;
	}
</style>
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">พิมพ์บาร์โค๊ดแบบกำหนดเอง</h2>
	<div class="col-12">
		<div class="block">
			<form method="POST" action="/products/barcodeprint">
				{{csrf_field()}}
				<div class="block-content">
					<h4>พิมพ์บาร์โค๊ดแบบกำหนดเอง
					</h4>
					<div class="row">
						<div class="col-md-12 col-xs-12">

							<h4>รายการสินค้า</h4>


							<div class="form-group row">
								<label class="col-6" for="example-select2 text-center">สินค้า</label>
								<label class="col-4" for="quantit text-center">จำนวนที่จะพิมพ์</label>
							</div>

							<div class="row">
								<div class="col-6">
									<select id="product" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกสินค้า">
										<option value="">เลือกสินค้า</option>
									</select>
								</div>
								<div class="col-4">
									<input id="quantity" type="number" placeholder="กรอกจำนวนที่จะพิมพ์" class="form-control">
								</div>
								<div class="col-2"><a href="javascript:addItem();" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มสินค้า</a>
								</div>
								<br>
							</div>
							<hr style="width: 100%; border: 1px solid #CCC">
							<div id="varintfield" class="form-group row" style="margin-top:15px;">

							</div>


							<div class="row">
								<div class="col-12 ">
									<input type="submit" value="พิมพ์บาร์โค๊ด" class="btn btn-warning pull-right" >

									<br><br>
								</div>
							</div>

						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="/assets/js/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
	$("#printbarcodebtn").addClass("active");
	count = 0;
	function addItem(){
		product_id = $("#product").val();
		product_name = $("#product option:selected").text();
		quantity = $("#quantity").val();
		variant = `<div id='item${count}' class="col-8 fixpad"><input type="text" name="products[]" hidden class="form-control" value='${product_id}'><input type="text" class="form-control" value='${product_name}'></div><div class="col-2" id="nameitem${count}"><input type="number" name="quantity[]" value='${quantity}' class="form-control" required></div><div id="btnitem${count}" class="col-2"><a href="javascript:delitem('${count}');" class="btn btn-danger"><i class="fa fa-trash"></i>ลบ</a><br></div>`;
		$("#varintfield").append(variant);
		count++;
	}
	function delitem(id){
		$("#item"+id).remove();
		$("#btnitem"+id).remove();
		$("#nameitem"+id).remove();
	}
		$('#product').select2({
		placeholder: 'เลือกสินค้า',
		ajax: {
			dataType: 'json',
			method: 'post',

			url: '{{Auth::user()->role==2?'/admin/stock/store':'/stock/store'}}',
			delay: 200,
			data: function(params) {
				return {
					term: params.term
				}
			},
			processResults: function (data, page) {
				return {
					results: data
				};
			},
		}
	});
</script>
<script>jQuery(function(){ Codebase.helpers(['datepicker'); });</script>
</body>
@endsection
