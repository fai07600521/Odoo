@extends('master')
@section('title','จัดการสาขา')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">สาขาทั้งหมด <small>จัดการสาขา</small></h2>
	<div class="block">
		<div class="block-content">
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รายละเอียดสาขา</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($branchs as $key=> $branch)
						<tr>
							<td class="text-center">{{$key+1}}</td>
							<td>
								<b>ชื่อสาขา: </b>{{$branch->name}}<br>
								<b>ที่อยู่: </b>{{$branch->address}}<br>
								<b>เบอร์โทร: </b>{{$branch->telephone}}
							</td>
							<td>
								@if($branch->status==0)
								<span class="badge badge-danger">ระงับการใช้งาน</span>
								@else
								<span class="badge badge-success">ปกติ</span>
								@endif
							</td>
							<td class="text-center">
								<a href="/admin/branch/get/{{$branch->id}}" class="btn btn-primary"><i class="fa fa-cogs"></i> จัดการสาขา</a>
							</td>
						</tr>
						@endforeach
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
	$('.data-table').dataTable();
</script>
	<script type="text/javascript">
		$("#branchmanagebtn").addClass("active");
	</script>
@endsection