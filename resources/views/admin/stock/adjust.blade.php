@extends('master')
@section('title','ปรับปรุงยอดในสต๊อกสินค้า')
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
@section('content')
<div class="content">
	<h2 class="content-heading">ปรับปรุงยอดสต๊อกสินค้า</h2>
	<div class="col-12">
		<div class="block">
			<form method="POST" action="/admin/stock/adjust">
				{{csrf_field()}}
				<div class="block-content">
					<h4>ปรับปรุงยอดสต๊อกสินค้า</h4>
					<div class="form-group row">
						<label class="col-12">เหตุผลในการปรับปรุงยอด</label>
						<div class="col-12">
							<input type="text" name="remark" required="" class="form-control" placeholder="กรุณากรอกเหตุผลในการปรับปรุงยอด">
						</div>

					</div>
					<div class="form-group row">
						<label class="col-12">สาขาที่จะปรับปรุงยอด</label>
						<div class="col-12">
							<select id="branch_id" name="branch_id" class="form-control">
								@foreach($branchs as $branch)
								<option value="{{$branch->id}}">{{$branch->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-12">เลือกแบรนด์เพื่อกรองสินค้า</label>
						<div class="col-12">
							<select id="brandselect" class="form-control" name="user_id">
								<option selected="" disabled="">เลือกแบรนด์</option>
								@foreach($users as $user)
								<option value="{{$user->id}}">{{$user->brand_name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-6" for="example-select2">สินค้า</label>
						<label class="col-2">จำนวนคงเหลือ</label>
						<label class="col-4" for="quantity">จำนวน</label>
					</div>
					<div class="row">
						<div class="col-6">
							<select id="product" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกสินค้า">
								<option value="">เลือกสินค้า</option>
							</select>
						</div>
						<div class="col-2">
							<input id="remainstock" value="0" class="form-control" disabled="">
						</div>
						<div class="col-2">
							<input id="quantity" type="number" placeholder="กรอกจำนวน" class="form-control">
						</div>
						<div class="col-2"><a href="javascript:addItem();" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มสินค้าเพื่อปรับสต๊อก</a>
						</div>
						<br>
					</div>


					<div id="varintfield" class="form-group row" style="margin-top:15px;">

					</div>


					<div class="row">
						<div class="col-12 ">
							<input type="submit" value="ยืนยัน" class="btn btn-primary pull-right" {{isset($purchase)&&$purchase->status!=0?'hidden':''}}>
							<br><br>
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
	count = 0;
	function addItem(){
		product_id = $("#product").val();
		product_name = $("#product option:selected").text();
		console.log(product_name);
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
</script>
<script type="text/javascript">
	brand_id = 0;
	$("#stockadjustbtn").addClass("active");
	$("#brandselect").on('change',function(){
		brand_id = $(this).val();
	});
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
					brand_id: brand_id
				}
			},
			processResults: function (data, page) {
				return {
					results: data
				};
			},
		}
	});	

	$("#product").on('change',function(){
		getStock();
	});
	$("#branch_id").on('change',function(){
			getStock();
	});

	function getStock(){
		branch_id = $("#branch_id").val();
		product_id = $("#product").val();
		$.ajax({
			url: "/admin/stock/productcheck", 
			method: "POST",
			dataType: "json",
			data: {branch_id:branch_id,product_id:product_id} ,
			success: function(result){
      			$("#remainstock").val(result.stock);
    		}});
	}

</script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
@endsection