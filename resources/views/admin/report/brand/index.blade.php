@extends('master')
@section('title','สรุปยอดขาย')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">เรียกดูยอดขาย Brand Report</h2>
	<div class="col-12">
		<div class="block">
			<form method="POST" action="/admin/brand/report">
				{{csrf_field()}}
				<div class="block-content">
					<div class="form-group row">
						<label class="col-12">เลือกสาขาที่ต้องการเรียกดูยอดขาย</label>
						<div class="col-12">
							<select name="branch_id" class="form-control">
								@foreach($branchs as $branch)
								<option value="{{$branch->getBranch->id}}">{{$branch->getBranch->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">

						<div class="col-6">
							<div class="row">
								<label class="col-12">วันที่เริ่มต้น</label>
								<div class="col-12">
									<input type="date" class="form-control" 
									min="2023-04-01"
									name="start_date" 
									data-week-start="1" 
									data-autoclose="true" 
									data-today-highlight="true" 
									data-date-format="yyyy-mm-dd" 
									placeholder="yyyy-mm-dd" 
									required="" 
									autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="row">
								<label class="col-12">วันที่สิ้นสุด</label>
								<div class="col-12">
									<input type="date" class="form-control" 
									min="2023-04-01"
									name="end_date" 
									data-week-start="1" 
									data-autoclose="true" 
									data-today-highlight="true" 
									data-date-format="yyyy-mm-dd" 
									placeholder="yyyy-mm-dd" 
									required="" 
									autocomplete="off">
								</div>
							</div>
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
<script src="/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
	$("#reportdaybtn").addClass("active");
</script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
</body>
@endsection