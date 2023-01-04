@extends('master')
@section('title','จัดการสมาชิก')
@section('content')
<div class="content">
	@if(isset($member))
	<h2 class="content-heading">{{$member->name}}<small> จัดการสมาชิก</small></h2>
	@else
	<h2 class="content-heading">เพิ่มสมาชิก</h2>
	@endif
	<div class="col-12">
		<div class="block">
			@if(isset($member))
			<form method="POST" action="/admin/member/update">
				<input hidden="" name="id" value="{{$member->id}}">
				@else
				<form method="POST" action="/admin/member/add">
					@endif
					{{csrf_field()}}
					<div class="block-content">
						<h4>จัดการสมาชิก</h4>
						<div class="row">
							<div class="col-12">
								<div class="form-group row">
									<label class="col-12" for="name">ชื่อสมาชิก</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="name" name="name" placeholder="กรอกชื่อสมาชิก" value="{{isset($member)? $member->name : ''}}" required="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-12" for="email">รายละเอียด</label>
									<div class="col-md-12">
										<input type="text" class="form-control" id="detail" name="detail" placeholder="กรอกรายละเอียดของสมาชิก" value="{{isset($member)? $member->detail : ''}}" required="">
									</div>
								</div>
								<div class="row">
									<div class="col-12">
										<input style="margin-left:5px;" type="submit" value="ยืนยัน" class="btn btn-primary pull-right">
										@if(isset($member))
										@if($member->status==1)
										<a style="margin-left:5px;" href="/admin/member/suspend/{{$member->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับสมาชิก {{$member->name}}? ')"   class="btn btn-danger pull-right"><i class="si si-ban"></i> ระงับ</a>
										@else
										<a style="margin-left:5px;" href="/admin/member/unsuspend/{{$member->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับสมาชิก {{$member->name}}? ')"   class="btn btn-success pull-right"><i class="si si-reload"></i> ยกเลิกระงับ</a>
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
		$("#membermanagebtn").addClass("active");
	</script>
	@endsection