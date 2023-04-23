@extends('master')
@section('title','จัดการสินค้า')
@section('style')
<link rel="stylesheet" href="/css/fm.tagator.jquery.css">
<style type="text/css">
	.fixpad{
		margin-bottom: 10px;
	}
</style>
@endsection
@section('content')
<div class="content">
	@if(isset($product))
	<h2 class="content-heading">{{$product->name}}</h2>
	@else
	<h2 class="content-heading">เพิ่มสินค้า</h2>
	@endif
	<div class="col-12">
		<div class="block">
			@if(isset($product))
			<form method="POST" action="/products/update" enctype="multipart/form-data">
				<input hidden="" name="id" value="{{$product->id}}">
				@else
				<form method="POST" action="/products/add" enctype="multipart/form-data">
					@endif
					{{csrf_field()}}
					<div class="block-content">
						<h4>จัดการสินค้า</h4>
						<div class="row">
							<div class="col-md-6 col-xs-12" style="text-align: center;">
								@if(isset($product))
								<img src="{{$product->pic_url}}" style="width: 60%;"><br>
								@else
								<img src="/assets/system/nopic.png" style="width: 60%;"><br>
								@endif
								<div class="form-group row">
									<label class="col-12" for="picture">เลือกรูปภาพสินค้า</label>
									<div class="col-12">
										<input type="file" id="picture" name="picture">
									</div>
								</div><br>
								<hr style="width: 100%; border: 1px solid #CCC">
								<h5 class="text-center">คุณลักษณะของสินค้า</h5>
								<div class="form-group row">
									<label class="col-8" style="padding-top:7px;">เพิ่มคุณลักษณะของสินค้าเช่น XL ดำ, S น้ำเงิน</label>
									<label class="col-4"><a href="javascript:addItem();" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มคุณลักษณะ</a>
									</div>
									<div id="varintfield" class="form-group row">
										@if(isset($product))
										@foreach($product->getVariant as $val)
										<div class="col-12 fixpad">
											<input class="form-control" value="{{$val->variant}}" name="old_variants[{{$val->id}}]">
										</div>
										@endforeach
										@endif
									</div>

								</div>
								<div class="col-md-6 col-xs-12">
									@if(Auth::user()->role=="2")
									<div class="form-group row">
										<label class="col-12" for="user_id">เลือกแบรนด์ที่จะเพิ่มสินค้า</label>
										<div class="col-md-12">
											<select class="form-control" name="user_id">
												@foreach($users as $user)
												@if(isset($product))
												<option value="{{$user->id}}"{{$user->id==$product->user_id? 'selected' : ''}}>{{$user->brand_name}}</option>
												@else
												<option value="{{$user->id}}">{{$user->brand_name}}</option>
												@endif

												@endforeach
											</select>
										</div>
									</div>
									@endif

									<div class="form-group row">
										<label class="col-12" for="name">ชื่อสินค้า</label>
										<div class="col-md-12">
											<input type="text" class="form-control" id="name" name="name" placeholder="กรอกชื่อสินค้า" value="{{isset($product)? $product->name : ''}}" required="">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12" for="unit">หน่วยนับหลัก</label>
										<div class="col-md-12">
											<select class="form-control" name="unit_id">
												@foreach($units as $unit)
												@if(isset($product))
												<option value="{{$unit->id}}"{{$unit->id==$product->unit_id? 'selected' : ''}}>{{$unit->name}}</option>
												@else
												<option value="{{$unit->id}}">{{$unit->name}}</option>
												@endif

												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12" for="price">ราคา</label>
										<div class="col-md-12">
											<input type="number" class="form-control" id="price" name="price" placeholder="กรอกราคา" value="{{isset($product) ? $product->price : ''}}" required="">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12" for="barcode">บาร์โค๊ด</label>
										<div class="col-md-12">
											<input type="text" class="form-control" id="barcode" name="barcode" placeholder="กรอกบาร์โค๊ด" value="{{isset($product) ? $product->getVariant[0]->barcode : ''}}">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12" for="description">เกี่ยวกับสินค้า</label>
										<div class="col-md-12">
											<textarea class="form-control" name="description">{{isset($product) ? $product->description : ''}}</textarea>
										</div>
									</div>
									@if(isset($product))
									<div class="form-group row">
										<label class="col-12" for="product-tags">Tag</label>
										<div class="col-lg-10">
											<input type="text" class="js-tags-input form-control" data-height="34px" id="product-tags" name="tags" value="@foreach($product->getTags as $tag){{$tag->getTagDetail->name}},@endforeach">
										</div>
									</div>
									@else
									<div class="form-group row">
										<label class="col-12" for="product-tags">Tag</label>
										<div class="col-lg-10">
											<input type="text" class="js-tags-input form-control" data-height="34px" id="product-tags" name="tags" value="">
										</div>
									</div>
									@endif
									<div class="form-group row">
										<div class="col-6">
											<div class="row">
												<div class="form-group row">
													<label class="col-12">ประเภทการลด</label>
													<div class="col-12">
														@if(isset($product))
														<div class="custom-control custom-radio custom-control-inline mb-5">
															<input class="custom-control-input" type="radio" name="discount_type" id="discount0" value="0" {{$product->discount_type==0?'checked=""':''}} >
															<label class="custom-control-label" for="discount0">ไม่มีการกำหนดราคา</label>
														</div>
														<div class="custom-control custom-radio custom-control-inline mb-5">
															<input class="custom-control-input" type="radio" name="discount_type" id="discount1" value="1" {{$product->discount_type==1?'checked=""':''}} >
															<label class="custom-control-label" for="discount1">กำหนดราคา</label>
														</div>
														<div class="custom-control custom-radio custom-control-inline mb-5">
															<input class="custom-control-input" type="radio" name="discount_type" id="discount2" value="2" {{$product->discount_type==2?'checked=""':''}}>
															<label class="custom-control-label" for="discount2">กำหนด %</label>
														</div>
														@else
														<div class="custom-control custom-radio custom-control-inline mb-5">
															<input class="custom-control-input" type="radio" name="discount_type" id="discount0" value="0" checked="">
															<label class="custom-control-label" for="discount0">ไม่มีการกำหนดราคา</label>
														</div>
														<div class="custom-control custom-radio custom-control-inline mb-5">
															<input class="custom-control-input" type="radio" name="discount_type" id="discount1" value="1" checked="">
															<label class="custom-control-label" for="discount1">กำหนดราคา</label>
														</div>
														<div class="custom-control custom-radio custom-control-inline mb-5">
															<input class="custom-control-input" type="radio" name="discount_type" id="discount2" value="2" >
															<label class="custom-control-label" for="discount2">กำหนด %</label>
														</div>

														@endif
													</div>
												</div>
											</div>
										</div>
										<div class="col-6">
											<div class="row">
												<label class="col-12" for="price">ราคาโปรโมชั่น <br><font style="color:red;">(ไม่เล่นโปรให้ใส่ 0 เสมอ !)</font></label>
												<div class="col-md-12">
													<input type="number" class="form-control" id="price" name="discount_price" placeholder="กรอกราคา" value="{{isset($product) ? $product->discount_price : '0'}}" >
												</div>
											</div>
										</div>

									</div>

									<div class="row">
										<div class="col-12 ">
											<input type="submit" value="ยืนยัน" class="btn btn-primary pull-right">
											@if(isset($product))
											@if($product->status==1)
											<a href="/products/suspend/{{$product->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับการขายสินค้า {{$product->name}}? ')"   class="pull-right btn btn-danger"><i class="si si-ban"></i> ระงับ</a>
											@else
											<a href="/products/unsuspend/{{$product->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับการขายสินค้า {{$product->name}}? ')"   class="pull-right btn btn-success"><i class="si si-reload"></i> ยกเลิกระงับ</a>
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
		<script src="/js/fm.tagator.jquery.js"></script>
		<script type="text/javascript">
			$("#productbtn").addClass("active");
			$count = 0;
			function addItem(){
				variant = '<div id="item'+$count+'" class="col-10 fixpad"><input type="text" name="product_variants[]" class="form-control" placeholder="กรุณากรอกคุณลักษณะ"></div><div id="btnitem'+$count+'" class="col-2"><a href="javascript:delitem('+$count+');" class="btn btn-danger"><i class="fa fa-trash"></i>ลบ</a><br></div>';
				$("#varintfield").append(variant);
				$count++;
			}
			function delitem(id){
				$("#item"+id).remove();
				$("#btnitem"+id).remove();
			}
			$("#product-tags").tagator({
  autocomplete: [@foreach($tags as $tag)'{{$tag->name}}',@endforeach'']
});

		</script>
		@endsection