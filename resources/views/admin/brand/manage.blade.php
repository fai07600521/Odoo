@extends('master')
@section('title','จัดการแบรนด์')
@section('content')
<div class="content">
	@if(isset($user))
	<h2 class="content-heading">{{$user->brand_name}}<small> จัดการแบรนด์</small></h2>
	@else
	<h2 class="content-heading">เพิ่มแบรนด์</h2>
	@endif
	<div class="col-12">
		<div class="block">
			@if(isset($user))
			<form method="POST" action="/admin/brand/update">
				<input hidden="" name="id" value="{{$user->id}}">
				@else
				<form method="POST" action="/admin/brand/add">
					@endif
					{{csrf_field()}}
					<div class="block-content">
						<h4>จัดการแบรนด์</h4>
						<div class="row">
							<div class="col-12">
								<div class="form-group row">
									<label class="col-12" for="brand_name">ชื่อแบรนด์</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="brand_name" name="brand_name" placeholder="กรอกชื่อผู้ที่ดูแลแบรนด์" value="{{isset($user)? $user->brand_name : ''}}" required="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="name">ผู้ดูแลแบรนด์</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="name" name="name" placeholder="กรอกชื่อผู้ที่ดูแลแบรนด์" value="{{isset($user)? $user->name : ''}}" required="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="name">สาขา/สำนักงาน</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="branch" name="branch" placeholder="กรอกสาขา เช่น สำนักงานใหญ่" value="{{isset($user)? $user->branch : ''}}" required="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="address">ที่อยู่</label>
									<div class="col-md-12">
										<textarea class="form-control" name="address">{{isset($user)? $user->address : ''}}
										</textarea>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="email">อีเมลล์</label>
									<div class="col-md-12">
										<input type="email" class="form-control" id="email" name="email" placeholder="กรอกอีเมลล์สำหรับให้แบรนด์ใช้เข้าสู่ระบบ" value="{{isset($user)? $user->email : ''}}" required="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="password">พาสเวิร์ด</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่านสำหรับแบรนด์เข้าสู่ระบบ" {{!isset($user)? "required=''":''}}>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="tax_id">เลขประจำตัวผู้เสียภาษี</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="tax_id" name="tax_id" placeholder="กรอกเลขประจำตัวผู้เสียภาษี" value="{{isset($user)? $user->tax_id : ''}}">
									</div>
								</div>


								<div class="form-group row">
									<label class="col-12" for="line">Line</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="line" name="line" placeholder="กรอกเบอร์โทรศัพท์หรือชื่อผู้ใช้งานไลน์" value="{{isset($user)? $user->line : ''}}">
									</div>
								</div>

								<div class="form-group row">
									<div class="col-12">
									<div class="custom-control custom-checkbox custom-control-inline mb-5">
										@if(isset($user))
										<input class="custom-control-input" type="checkbox" name="vat" id="vat" {{$user->vat==1? "checked=''": ""}}>
										@else
										<input class="custom-control-input" type="checkbox" name="vat" id="vat">
										@endif

										<label class="custom-control-label" for="vat">VAT</label>
									</div>
								</div>
								</div>

								<div class="row">
									<div class="col-12 ">
										<input style="margin-left:5px;" type="submit" value="ยืนยัน" class="btn btn-primary pull-right">
										@if(isset($user))
										@if($user->status==1)
										<a style="margin-left:5px;" href="/admin/brand/suspend/{{$user->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับแบรนด์{{$user->brand_name}}? ')"   class="btn btn-danger pull-right"><i class="si si-ban"></i> ระงับ</a>
										@else
										<a style="margin-left:5px;" href="/admin/brand/unsuspend/{{$user->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับแบรนด์{{$user->brand_name}}? ')"   class="btn btn-success pull-right"><i class="si si-reload"></i> ยกเลิกระงับ</a>
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
	<script type="text/javascript">
		$("#brandmanagebtn").addClass("active");
	</script>
	@endsection