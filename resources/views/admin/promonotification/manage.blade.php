@extends('master')
@section('title','จัดการโปรโมชั่น')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
@endsection
@section('content')
<div class="content">
	@if(isset($promotion))
	<h2 class="content-heading">{{$promotion->description}}<small> จัดการโปรโมชั่น</small></h2>
	@else
	<h2 class="content-heading">เพิ่มโปรโมชั่น</h2>
	@endif
	<div class="col-12">
		<div class="block">
			<form method="POST" action="{{isset($promotion)?'/admin/promonotification/update':'/admin/promonotification/create'}}">
				@if(isset($promotion))
				<input hidden="" value="{{$promotion->id}}" name="id">
				@endif
				{{csrf_field()}}
				<div class="block-content">
					<h4>จัดการโปรโมชั่น</h4>
					<div class="row">
						<div class="col-12">
							<div class="form-group row">
								<label class="col-12" for="name">รายละเอียด</label>
								<div class="col-md-12">
									<input type="text" class="form-control" id="description" name="description" placeholder="กรอกรายละเอียด" value="{{isset($promotion)? $promotion->description : ''}}" required="">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12">เลือกสาขาที่จะให้แสดงผล</label>
								<div class="col-12">
									@if(isset($promotion))
									@foreach($branchs as $branch)
									<div class="custom-control custom-checkbox custom-control-inline mb-5">
										<input class="custom-control-input" type="checkbox" name="branch[]"  id="branch{{$branch->id}}" value="{{$branch->id}}" {{in_array($branch->id, $branchinuse)?'checked':''}}>
										<label class="custom-control-label" for="branch{{$branch->id}}">{{$branch->name}}</label>
									</div>
									@endforeach
									@else
									@foreach($branchs as $branch)
									<div class="custom-control custom-checkbox custom-control-inline mb-5">
										<input class="custom-control-input" type="checkbox" name="branch[]"  id="branch{{$branch->id}}" value="{{$branch->id}}">
										<label class="custom-control-label" for="branch{{$branch->id}}">{{$branch->name}}</label>
									</div>
									@endforeach
									@endif

								</div>
							</div>	
							<div class="form-group row">
								<label class="col-12" for="startdate">วันเริ่มต้น</label>
								<div class="col-lg-12">
									<input type="text" class="js-datepicker form-control"  name="startdate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="{{isset($promotion)? $promotion->startdate : ''}}">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-12" for="enddate">วันสิ้นสุด</label>
								<div class="col-lg-12">
									<input type="text" class="js-datepicker form-control" name="enddate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" value="{{isset($promotion)? $promotion->enddate : ''}}" >
								</div>
							</div>						
							<div class="form-group row">
								<div class="col-12 text-right">
									@if(isset($promotion))
									<button type="submit" class="btn btn-primary">บันทึก</button>
									@else
									<button type="submit" class="btn btn-success">เพิ่ม</button>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	@if(isset($promotion))
	<h2 class="content-heading">สินค้าที่เข้าร่วมโปรโมชั่น</h2>
	<div class="col-12">
		<div class="block">

			<div class="block-content">
				<form method="POST" action="/admin/promonotification/addproduct">
					{{csrf_field()}}
					<input hidden="" value="{{$promotion->id}}" name="id">
					<div class="form-group row productbox">
						<label class="col-12">เลือกรูปแบบการส่งผลโปรโมชั่น</label>
						<div class="col-12">
							<input type="radio" name="type" value="product" checked=""> รายสินค้า
							<input type="radio" name="type" value="brand"> รายแบรนด์

						</div>
					</div>
					<div class="form-group row productbox">
						<label class="col-12">เลือกสินค้า</label>
						<div class="col-12">
							<select id="product" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกสินค้า" name="products[]" multiple="multiple">
								<option value="">เลือกสินค้า</option>
							</select>
						</div>
					</div>
					<div class="form-group row brandbox">
						<label class="col-12">เลือกแบรนด์</label>
						<div class="col-12">
							<select id="brand" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกแบรนด์" name="brand_id">
								<option value="">เลือกแบรนด์</option>
								@foreach($brands as $brand)
								<option value="{{$brand->id}}">{{$brand->brand_name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-12 text-right">
							<button type="submit" class="btn btn-success">เพิ่ม</button>
						</div>
					</div>

				</form>

				<div class="row">
					<div class="col-12">
						<br><br>
						<h5>รายการสินค้าในโปรโมชั่น</h5>
					</div>
					<div class="col-12">
						<table class="table table-hover data-table">
							<thead>
								<tr>
									<td class="text-center">#</td>
									<td class="text-center">สินค้า</td>
									<td class="text-center">การกระทำ</td>
								</tr>
							</thead>
							<tbody>
								<?php
								$promoproducts = $promotion->getProduct;
								?>
								@foreach($promoproducts as $key=> $product)
								<tr>
									<td class="text-center">{{$key+1}}</td>
									<td>
										{{$product->getProductVariant->getProduct->name}} ({{$product->getProductVariant->variant}})
									</td>
									<td class="text-center">
										<a href="/admin/promonotification/delete/{{$promotion->id}}/{{$product->product_id}}" onclick="return confirm('คุณมั่นใจที่จะลบสินค้านี้ออกจากโปรโมชั่น? ')"   class="btn btn-danger"><i class="si si-ban"></i> ลบ</a>
									</td>
								</tr>
								@endforeach
							</tbody>

						</table>
					</div>
				</div>
			</div>
		</div>



		@endif
	</div>
	@endsection
	@section('script')
	<script src="/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="/assets/js/plugins/select2/js/select2.full.min.js"></script>

	<script type="text/javascript">
		$("#promotionnewbtn").addClass("active");
		$('#product').select2({
			placeholder: 'เลือกสินค้า',
			ajax: {
				dataType: 'json',
				method: 'post',
				url: '/admin/promonotification/discountprice/apigetproduct',
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
	<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
	@endsection