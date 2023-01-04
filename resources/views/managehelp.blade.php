@extends('master')
@section('title','เพิ่มคู่มือ')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/summernote/summernote-bs4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">เพิ่มคู่มือ</h2>
	<div class="col-12">
		<div class="block">
			@if(isset($help))
			<form method="POST" action="/admin/uphelp">
				<input hidden="" name="id" value="{{$help->id}}">
				@else
				<form method="POST" action="/admin/help">
					@endif
					{{csrf_field()}}
					<div class="block-content">

						<div class="form-group row">
							<label class="col-12">ชื่อคู่มือ</label>
							<div class="col-12">
								<input type="text" required="" value="{{isset($help)? $help->title:''}}" class="form-control" name="title">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-12">รายละเอียดคู่มือ</label>
							<div class="col-12">
								<textarea class="js-summernote" name="detail">{!!isset($help)? $help->detail : ''!!}</textarea>
							</div>
						</div>
						<div class="row">
							<div class="col-12 ">
								<input type="submit" value="ยืนยัน" class="btn btn-primary pull-right">
								<br><br>
							</div>
						</div>

					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
@section('script')
<script src="/assets/js/plugins/summernote/summernote-bs4.min.js"></script>
<script>jQuery(function(){ Codebase.helpers(['summernote'])});</script>

@endsection