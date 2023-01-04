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
	<h2 class="content-heading">พิมพ์ป้ายราคา</h2>
	<div class="col-12">
		<div class="block">
			<form method="POST" action="/products/promotionprint">
				{{csrf_field()}}
				<div class="block-content">
					<h4>พิมพ์ป้ายราคา</h4>
					<div class="row">
						<div class="col-md-12 col-xs-12">

							<h4>เลือกสินค้า</h4>


							<div class="form-group row">
								<label class="col-2" for="example-select2 text-center">แบรนด์</label>
								<label class="col-4" for="example-select2 text-center">สินค้า</label>
								<label class="col-2" for="example-select2 text-center">ราคาโปรโมชั่น</label>
								<label class="col-2" for="quantit text-center">จำนวนที่จะพิมพ์</label>
							</div>

							<div class="row">
								<div class="col-2">
									<select id="brand" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกแบรนด์">
										<option value="0" disabled="" selected="">เลือกสินค้า</option>
										@foreach($brands as $brand)
										<option value="{{$brand->id}}">{{$brand->brand_name}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-4">
									<select id="product" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกสินค้า">
										<option value="">เลือกสินค้า</option>
									</select>
								</div>
								<div class="col-2">
									<input id="price" type="number" placeholder="กรอกราคาโปรโมชั่น" class="form-control">
								</div>
								<div class="col-2">
									<input id="quantity" type="number" placeholder="กรอกจำนวนที่จะพิมพ์" class="form-control">
								</div>
								<div class="col-2"><a href="javascript:addItem();" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มสินค้า</a>
								</div>
								<br>
							</div>
							<hr style="width: 100%; border: 1px solid #CCC">
							<h4 id="edittitle" style="display: none;">แก้ไขรายละเอียด</h4>
							<div id="varintfield" class="form-group row" style="margin-top:15px;">

							</div>


							<div class="row">
								<div class="col-12 ">
									<input type="submit" value="พิมพ์ป้าย" class="btn btn-warning pull-right" >

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
		$("#edittitle").fadeIn();
		brand_name = $("#brand option:selected").text();
		price = $("#price").val();
		product_name = $("#product option:selected").text();
		quantity = $("#quantity").val();
		variant = `<div id='itembrand${count}' class="col-2 fixpad"><input name="brand_name[]" type="text" class="form-control" value='${brand_name}'></div><div id='item${count}' class="col-4 fixpad"><input name="product_name[]" type="text" class="form-control" value='${product_name}'></div><div id='priceitem${count}' class="col-2 fixpad"><input name="price[]" type="text" class="form-control" value='${price}'></div><div class="col-2" id="nameitem${count}"><input type="number" name="quantity[]" value='${quantity}' class="form-control" required></div><div id="btnitem${count}" class="col-2"><a href="javascript:delitem('${count}');" class="btn btn-danger"><i class="fa fa-trash"></i>ลบ</a><br></div>`;
		$("#varintfield").append(variant);
		count++;
	}
	function delitem(id){
		$("#item"+id).remove();
		$("#btnitem"+id).remove();
		$("#nameitem"+id).remove();
		$("#itembrand"+id).remove();
		$("#priceitem"+id).remove();
	}
	brand_id = 0;
	$("#brand").on('change',function(){
		brand_id = $(this).val();
	});
	$('#brand').select2();
	$('#product').select2({
		placeholder: 'เลือกสินค้า',
		ajax: {
			dataType: 'json',
			method: 'post',
			url: '/admin/stock/store',
			delay: 200,
			data: function(params) {
				return {
					term: params.term,
					brand_id: brand_id,
					mode: "label"
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

</body>
@endsection
