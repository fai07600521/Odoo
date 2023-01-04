@extends('master')
@section('title','จัดการโปรโมชั่นอัตโนมัติทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
<style type="text/css">
	.before{
		background-color: #ffeaaf;
	}
	.done{
		background-color: #afafaf;
	}
	.running{
		background-color: #ceffce;
	}
</style>
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">เรียกดูโปรโมชั่นอัตโนมัติ</h2>
	<div class="block">
			<form id="mainform" method="POST" action="/admin/promotions/specific">
				{{csrf_field()}}
				<div class="block-content">
					<div class="form-group row">
						<label class="col-12">เลือกสาขาที่ต้องการดูโปรโมชั่น</label>
						<div class="col-12">
							<select name="branch_id" class="form-control">
								<option value="0">ทุกสาขา</option>
								@foreach($branchs as $branch)
								<option value="{{$branch->id}}">{{$branch->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group row">

						<div class="col-6">
							<div class="row">
								<label class="col-12">วันที่เริ่มต้น</label>
								<div class="col-12">
									<input type="text" class="js-datepicker form-control" name="start_date" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required="" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="row">
								<label class="col-12">วันที่สิ้นสุด</label>
								<div class="col-12">
									<input type="text" class="js-datepicker form-control" name="end_date" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required="" autocomplete="off">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-12">
							<button data-url="/admin/promotions/specific" class="btn btn-primary pull-right submitbtn">เรียกดูโปรโมชั่น</button>
							<button data-url="/admin/promotions/dateprint" class="btn btn-warning pull-right submitbtn">พิมพ์ป้ายรายเดือน</button>
						</div>
						<br><br>
					</div>
				</div>
			</form>
		</div>
	@if(isset($promotions))
	<h2 class="content-heading">โปรโมชั่นทั้งหมด <small>จัดการโปรโมชั่น</small></h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
				<a href="/admin/promotions/create" class="btn btn-success"><i class="fa fa-plus"></i> สร้างโปรโมชั่น</a>
			</div>
			<br><br>
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รายละเอียด</td>
							<td class="text-center">สาขาที่แสดงผล</td>
							<td class="text-center">วันเริ่มต้น</td>
							<td class="text-center">วันสิ้นสุด</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($promotions as $key=> $promotion)
						<?php
						$today = new DateTime();
						$startdate = new DateTime($promotion->startdate);
						$enddate = new DateTime($promotion->enddate);
						$status = "";
						if($startdate > $today){
							$status = "before";
						}
						if($enddate < $today){
							$status = "done";
						}
						if($today>$startdate && $today<$enddate){
							$status = "running";
						}
						
						?>
						<tr class="{{$status}}">
							<td class="text-center" >{{$key+1}}</td>
							<td >
								{{$promotion->description}}
							</td>
							<td style="width: 200px;">
								@foreach($promotion->getBranch as $branch)
								- {{$branch->getBranchinfo->name}}<br>
								@endforeach
							</td>
							<td style="width: 150px;">
								{{$promotion->startdate}}
							</td>
							<td style="width: 150px;">
								{{$promotion->enddate}}
							</td>

							<td class="text-center" style="width: 100px;">
								<a href="/admin/promotions/print/group/{{$promotion->id}}" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> พิมพ์ป้าย</a>
								<a href="/admin/promotions/deletepromotion/{{$promotion->id}}" class="btn btn-danger btn-block"><i class="fa fa-trash"></i> ลบ</a>
								<a href="/admin/promotions/get/{{$promotion->id}}" class="btn btn-primary btn-block"><i class="fa fa-info"></i> แก้ไข</a>
	
							</td>
						</tr>
						@endforeach
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
	@endif
</div>
@endsection
@section('script')
<script src="/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
	$('.data-table').dataTable();
	jQuery(function(){ Codebase.helpers(['datepicker']); });
</script>
<script type="text/javascript">
	$("#brandmanagebtn").addClass("active");

	$(".submitbtn").on('click',function(e){
		e.preventDefault();
		$("#mainform").attr('action', $(this).attr('data-url')).submit();
	})

</script>
@endsection