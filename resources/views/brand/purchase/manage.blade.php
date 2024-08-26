@extends('master')
@section('title','จัดการใบนำเข้า')
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
	@if(isset($purchase))
	<h2 class="content-heading">ใบนำเข้าหมายเลข# {{$purchase->id}}</h2>
	@else
	<h2 class="content-heading">เพิ่มใบนำเข้า</h2>
	@endif
	<div class="col-12">
		<div class="block">
			@if(isset($purchase))
			@if($purchase->status!=1)
			@if($purchase->status!=9)
			<form method="POST" action="/purchase/update">
				<input hidden="" name="id" value="{{$purchase->id}}">
				<input hidden="" name="branch_id" value="{{$purchase->branch_id}}">
				@endif
				@else
				<form method="POST" action="/purchase/add">
					@endif
					@else
					<form method="POST" action="/purchase/add">
						@endif
						{{csrf_field()}}
						<div class="block-content">
							<h4>จัดการใบนำเข้า{{isset($purchase)?'เลขที่ '.$purchase->id:''}}
								@if(isset($purchase))
								@if($purchase->status==9)
								<span class="badge badge-danger">ยกเลิกใบนำเข้า</span>
								@elseif($purchase->status==0)
								<span class="badge badge-primary">รอทางร้านตอบรับ</span>
								@else
								<span class="badge badge-success">นำเข้าเรียบร้อยแล้ว</span>
								@endif
								@endif
							</h4>
							<div class="row">
								<div class="col-md-12 col-xs-12">

									<div class="form-group row">
										<label class="col-12">เลือกบริษัท</label>
										<div class="col-12">
										
											<select name="company_id" class="form-control">
												
												@if(isset($purchase))
												@foreach($companies as $company)
												<option {{$company->id==$purchase->company_id? 'selected' : ''}} value="{{$company->id}}">{{$company->name}}</option>
												@endforeach
												@else
												@foreach($companies as $company)
												<option value="{{$company->id}}">{{$company->name}}</option>
												@endforeach

												@endif
												
												
											</select>
											
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12">เลือกสาขา</label>
										<div class="col-12">
											<select name="branch_id" class="form-control">

												@if(isset($purchase))
												@foreach($branchs as $branch)
												<option {{$branch->id==$purchase->branch_id? 'selected' : ''}} value="{{$branch->id}}">{{$branch->name}}</option>
												@endforeach
												@else
												@foreach($branchs as $branch)
												<option value="{{$branch->id}}">{{$branch->name}}</option>
												@endforeach

												@endif
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12" for="unit">เลือกแบรนด์ที่จะจัดทำใบนำเข้าสินค้า</label>
										<div class="col-md-12">
											@if(isset($purchase))
											<input hidden="" name="user_id" value="{{$purchase->user_id}}">
											<input disabled="" class="form-control" value="{{$purchase->getUser->brand_name}}">
											@else
											@if(Auth::user()->role==2)
											<select id="brandselect" class="form-control" name="user_id" >
												<option selected="" disabled="">เลือกแบรนด์</option>
												@foreach($users as $user)
												<option value="{{$user->id}}">{{$user->brand_name}}</option>
												@endforeach
											</select>
											@else
												<input type="text" hidden="" name="company_id" value="{{Auth::user()->id}}">
												<input type="text" class="form-control" value="{{Auth::user()->brand_name}}" disabled="">
											@endif
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12" for="remark">หมายเหตุ</label>
										<div class="col-12">
											@if(isset($purchase))
											<textarea name="remark" class="form-control">{{$purchase->remark}}</textarea>
											@else
											<textarea name="remark" class="form-control"></textarea>
											@endif
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12" for="unit">เลือกวันที่จัดส่งสินค้า</label>
										<div class="col-lg-12">
											<input type="text" class="js-datepicker form-control" id="example-datepicker3" name="shipdate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="{{isset($purchase)? $purchase->shipdate : ''}}" {{isset($purchase)&&$purchase->status!=0? 'disabled':''}}>
										</div>
									</div>
									<hr style="width: 100%; border: 1px solid #CCC">
									<h4>รายการสินค้า</h4>

									<div class="row">
										<label class="col-8">สินค้า</label>
										<label class="col-4">จำนวน</label>
										<div class="col-8">
											<select id="product" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกสินค้า">
												<option value="">เลือกสินค้า</option>
											</select>
										</div>
										<div class="col-2">
											<input id="quantity" type="number" placeholder="กรอกจำนวน" class="form-control">
										</div>
										<div class="col-2"><a href="javascript:addItem();" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มสินค้า</a>
										</div>
										<div class="col-12">
											<br>
											<hr>
										</div>
									</div>
									@if(isset($purchase))
									@if($purchase->status!=0)
									<table class="table table-vcenter">
										<thead>
											<tr>
												<th class="text-center">ชื่อสินค้า</th>
												<th class="text-center">จำนวน</th>
											</tr>
										</thead>
										<tbody>
											@foreach($purchase->getItem as $item)
											<tr>
												<td>{{$item->getProductVariant->getProduct->name}} ({{$item->getProductVariant->variant}})</td>
												<td class="text-center">{{$item->quantity}} {{$item->getProductVariant->getProduct->getUnit->name}}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
									@else
									

									@foreach($purchase->getItem as $key=> $item)
									<div id="varintfield" class="form-group row">
										<div id="nameitemk{{$key}}" class="col-8">
											<input hidden="" name="products[]" value="{{$item->getProductVariant->id}}">
											<input disabled="" value="{{$item->getProductVariant->getProduct->name}} ({{$item->getProductVariant->variant}}) ({{$item->getProductVariant->getProduct->price}} บาท)" class="form-control">
										</div>
										<div id="itemk{{$key}}" class="col-2"><input type="number" value="{{$item->quantity}}" name="quantity[]" required="" class="form-control"></div>
										<div id="btnitemk{{$key}}" class="col-2"><a href="javascript:delitem('k{{$key}}');" class="btn btn-danger"><i class="fa fa-trash"></i>ลบ</a><br></div>
									</div>
									@endforeach
									@endif
									@endif


									<div id="varintfield" class="form-group row" style="margin-top:15px;">

									</div>


									<div class="row">
										<div class="col-12 ">

											<input type="submit" value="ยืนยัน" class="btn btn-primary pull-right" {{isset($purchase)&&$purchase->status!=0?'hidden':''}}>

											@if(isset($purchase))
											<a target="_blank" style="margin-right: 10px;" href="/purchase/printpo/{{$purchase->id}}" class="btn bg-elegance text-white pull-right"><i class="si si-printer"></i> พิมพ์ใบ PO (New)</a>
											<a target="_blank" style="margin-right: 10px;" href="/purchase/print/{{$purchase->id}}" class="btn btn-info pull-right"><i class="si si-printer"></i> พิมพ์ใบนำเข้า</a>
											<a target="_blank" style="margin-right: 10px;" href="/purchase/barcode/{{$purchase->id}}" class="btn btn-warning pull-right"><i class="si si-printer"></i> พิมพ์บาร์โค๊ด</a>
											@if($purchase->status!=1)
											@if($purchase->status==0)
											@if(Auth::user()->role==2)
											<a style="margin-right: 10px;" onclick="sendToBackend({{$purchase->id}})" class="pull-right btn btn-success"><i class="si si-login"></i> รับสินค้าเข้าร้าน</a>
											@endif
											<a style="margin-right: 10px;" href="/purchase/cancel/{{$purchase->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกใบนำเข้านี้ใช่หรือไม่? ')"   class="pull-right btn btn-danger"><i class="si si-ban"></i> ยกเลิกใบนำเข้า</a>
											@endif
											@endif
											@endif

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
			@if(isset($purchase))
			var brand_id = {{$purchase->user_id}};
			@else
			var brand_id = 0;
			@endif
			
			$("#brandselect").on('change',function(){
				brand_id = $(this).val();
			});

			@if(Auth::user()->role!=1)
			$("#pocreatebtn").addClass("active");
			@endif
			$("#poallbtn").addClass("active");
			count = 0;
			function addItem(){
				product_id = $("#product").val();
				product_name = $("#product option:selected").text();
				quantity = $("#quantity").val();
				variant = `<div id='item${count}' class="col-8 fixpad"><input type="text" name="products[]" hidden class="form-control" value='${product_id}'><input type="text" class="form-control" value='${product_name}'></div><div class="col-2" id="nameitem${count}"><input type="number" name="quantity[]" value='${quantity}' class="form-control" required></div><div id="btnitem${count}" class="col-2"><a href="javascript:delitem('${count}');" class="btn btn-danger"><i class="fa fa-trash"></i>ลบ</a><br></div>`;
				$("#varintfield").append(variant);
				count++;
			}
			function sendToBackend(id){
				Swal.fire({
					title: "Are you sure?",
					text: "ยืนยันการรับสินค้า",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "ยืนยัน"
				}).then((result) => {
					if (result.value) {
						$.ajax({
						url: `/admin/purchase/recieveNew/${id}`,
						type: "GET",
						dataType: 'json',
						success: function (response) {
							const status = response.status;
							const purchase = response.purchase;
							const stock = response.stock;
							const type = response.type;
							if(status){
								$.ajax({
									data: JSON.stringify({
										purchase,
										stock,
										type
									}),
									url: "https://pos-shopee.suttipongact.com/system",
									type: "POST",
									contentType: 'application/json',
									dataType: 'json',
									success: function (response) {
										Swal.fire({
											title: 'นำเข้าสำเร็จ',
											type: 'success',
											timer: 1500,
											showConfirmButton: false,
											onAfterClose: () => {
												window.location.reload();
											}
										});
									}
								});
							}
						}
					});
					} else if (result.isDenied) {
						Swal.fire("Changes are not saved", "", "info");
					}
				});
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
	</body>
	@endsection