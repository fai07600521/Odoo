@extends('master')
@section('title','เข้าสู่ระบบ POS')
@section('content')
<div class="content">
	<h2 class="content-heading">เลือกสาขาเพื่อเข้าใช้งาน POS</h2>
	<div class="col-12">
		<div class="block">
			<form method="POST" action="/admin/main">
				{{csrf_field()}}
				<div class="block-content">
					<div class="form-group row">
						<label class="col-12">เลือกสาขาที่ต้องการเปิด POS</label>
						<div class="col-12">
							<select name="branch_id" class="form-control">
								@foreach($branchs as $branch)
								<option value="{{$branch->id}}">{{$branch->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-12">
						<input class="btn btn-primary pull-right" value="ยืนยัน" type="submit">
					</div>
						<br><br>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
@section('script')
<script type="text/javascript">
	$("#posbtn").addClass("active");
</script>
@endsection