@extends('master')
@section('title','ย้ายคลังสินค้า')
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
	<h2 class="content-heading">ย้ายคลังสินค้า</h2>
	<div class="col-12">
		<div class="block">
			@if(isset($stockadj))
			<form method="POST" action="/admin/stock/transfer/update">
				<input hidden="" name="id" value="{{$stockadj->id}}">
				@else
				<form method="POST" action="/admin/stock/transfer">
				@endif
				{{csrf_field()}}
				<div class="block-content">
					@if(isset($stockadj))
					<h4>แก้ไขใบย้ายสินค้าหมายเลข {{$stockadj->id}}</h4>
					@else
					<h4>ย้ายคลังสินค้า</h4>
					@endif
					<div class="form-group row">
						<label class="col-12">เหตุผลในการย้ายคลังสินค้า</label>
						<div class="col-12">
							<input type="text" name="remark" required="" class="form-control" placeholder="กรุณากรอกเหตุผลในการปรับปรุงยอด" value="{{isset($stockadj)?$stockadj->remark:''}}">
						</div>

					</div>
					<div class="form-group row">
						<label class="col-12">คลังสินค้าต้นทาง</label>
						<div class="col-12">
							@if(isset($stockadj))
							<input disabled="" class="form-control" value="{{$stockadj->getSource->name}}">
							<input hidden="" name="src_id" value="{{$stockadj->src_id}}">
							@else
							<select name="src_id" class="form-control">
								@foreach($branchs as $branch)
								<option value="{{$branch->id}}">{{$branch->name}}</option>
								@endforeach
							</select>
							@endif
						</div>
					</div>
					<div class="form-group row">
						<label class="col-12">คลังสินค้าปลายทาง</label>
						<div class="col-12">
							@if(isset($stockadj))
							<input disabled="" class="form-control" value="{{$stockadj->getDestination->name}}">
							<input hidden="" name="src_id" value="{{$stockadj->dst_id}}">
							@else
							<select name="dst_id" class="form-control">
								@foreach($branchs as $branch)
								<option value="{{$branch->id}}">{{$branch->name}}</option>
								@endforeach
							</select>
							@endif
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
						<label class="col-8" for="example-select2">สินค้า</label>
						<label class="col-4" for="quantity">จำนวน</label>
					</div>
					<div class="row">
						<div class="col-8">
							<select id="product" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกสินค้า">
								<option value="">เลือกสินค้า</option>
							</select>
						</div>
						<div class="col-2">
							<input id="quantity" type="number" placeholder="กรอกจำนวน" class="form-control">
						</div>
						<div class="col-2"><a href="javascript:addItem();" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มสินค้าเพื่อปรับสต๊อก</a>
						</div>
						<br>
					</div>


					<div id="varintfield" class="form-group row" style="margin-top:15px;">
						<div class="col-12">
							<br>
							<hr>
							<br>
						</div>
						@if(isset($stockadj))
						@foreach($stockadj->getItem as $key=> $item)
						<div id="nameitemk{{$key}}" class="col-6">
							<input hidden="" class="products-input" name="products[]" value="{{$item->getProductVariant->id}}">
							<input disabled="" value="{{$item->getProductVariant->getProduct->name}} ({{$item->getProductVariant->variant}}) ({{$item->getProductVariant->getProduct->price}} บาท)" class="form-control">
						</div>
						<input class="form-control col-2" disabled="" value="{{$item->getProductVariant->id}}">
						<div id="itemk{{$key}}" class="col-2"><input type="number" value="{{$item->quantity}}" name="quantity[]" required="" class="form-control quantity-input"></div>
						<div id="btnitemk{{$key}}" class="col-2"><a href="javascript:delitem('k{{$key}}');" class="btn btn-danger"><i class="fa fa-trash"></i>ลบ</a><br></div>
						@endforeach
						@endif

					</div>


					<div class="row">
						<div class="col-12 text-right">
						@if(isset($stockadj))
						
						
							@if($stockadj->status==0)
							<a href="/admin/stock/transfer/cancel/{{$stockadj->id}}" class="btn btn-danger" onclick="return confirm('คุณมั่นใจที่จะยกเลิกใบย้ายสินค้านี้ใช่หรือไม่? ')">ยกเลิกใบย้ายสินค้า</a>
							<a href="/admin/stock/transfer/submit/{{$stockadj->id}}" class="btn btn-success">ยืนยันการย้ายสินค้า</a>
							
							@if($stockadj->status!=9)
							<a href="/admin/stock/transfer/print/{{$stockadj->id}}" class="btn bg-elegance text-white">พิมพ์ใบย้ายสินค้า</a>&nbsp;
							@endif
							
							
							@endif
	
						
						@endif
						<input type="submit" value="บันทึก" class="btn btn-primary pull-right" >
						<br><br>
						</div>
					</div>
					<div class="row text-right">
						<div class="col">
							<button type="button" onclick="copyVariant()" class="btn btn-success">Copy VaraintId</button>
							<button type="button" onclick="copyQuantities()" class="btn btn-info">Copy Quantities</button>
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

	function copyVariant(){
		const inputsproducts = document.querySelectorAll(".products-input");
		const quantitiesproducts = Array.from(inputsproducts).map(function(input) {
			return input.value;
		});
		const csvContent = quantitiesproducts.join("\n");
		navigator.clipboard.writeText(csvContent); //products
		Swal.fire({
			title: 'copy variant success',
			type: 'success',
			timer: 1500,
			showConfirmButton: false,
		});
	}


	function copyQuantities() {
		const inputs = document.querySelectorAll(".quantity-input");
		const quantities = Array.from(inputs).map(function(input) {
			return input.value;
		});
		const csvContent = quantities.join("\n");
		navigator.clipboard.writeText(csvContent);
		Swal.fire({
			title: 'copy quantities success',
			type: 'success',
			timer: 1500,
			showConfirmButton: false,
		});
	}


</script>
<script type="text/javascript">
	brand_id = 0;
	$("#transferbtn").addClass("active");
	$("#brandselect").on('change',function(){
		brand_id = $(this).val();
	});
	$("#brandselect").select2();

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
</script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
@endsection