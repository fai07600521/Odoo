@extends('master')
@section('title','จัดการผู้ดูแลระบบ')
@section('content')
<div class="content">
	@if(isset($user))
	<h2 class="content-heading">{{$user->name}}<small> จัดการผู้ดูแลระบบ</small></h2>
	@else
	<h2 class="content-heading">เพิ่มผู้ดูแลระบบ</h2>
	@endif
	<div class="col-12">
		<div class="block">
			@if(isset($user))
			<form method="POST" action="/admin/admin/update">
				<input hidden="" name="id" value="{{$user->id}}">
				@else
				<form method="POST" action="/admin/admin/add">
					@endif
					{{csrf_field()}}
					<div class="block-content">
						<h4>จัดการผู้ดูแลระบบ</h4>
						<div class="row">
							<div class="col-12">
								<div class="form-group row">
									<label class="col-12" for="name">ผู้ดูแลระบบ</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="name" name="name" placeholder="กรอกชื่อผู้ที่ดูแลระบบ" value="{{isset($user)? $user->name : ''}}" required="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="email">อีเมลล์</label>
									<div class="col-md-12">
										<input type="email" class="form-control" id="email" name="email" placeholder="กรอกอีเมลล์สำหรับให้ผู้ดูแลระบบใช้เข้าสู่ระบบ" value="{{isset($user)? $user->email : ''}}" required="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="password">พาสเวิร์ด</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่านสำหรับผู้ดูแลเข้าสู่ระบบ"  required="">
									</div>
								</div>

								<div class="row">
									<div class="col-12 ">
										<input style="margin-left:5px;" type="submit" value="ยืนยัน" class="btn btn-primary pull-right">
										@if(isset($user))
										@if($user->status==1)
										<a style="margin-left:5px;" href="/admin/admin/suspend/{{$user->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับผู้ดูแลระบบ{{$user->name}}? ')"   class="btn btn-danger pull-right"><i class="si si-ban"></i> ระงับ</a>
										@else
										<a style="margin-left:5px;" href="/admin/admin/unsuspend/{{$user->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับผู้ดูแลระบบ{{$user->name}}? ')"   class="btn btn-success pull-right"><i class="si si-reload"></i> ยกเลิกระงับ</a>
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
		$("#adminmanagebtn").addClass("active");
	</script>
	@endsection