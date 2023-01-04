@extends('master')
@section('title','จัดการผู้ดูแลระบบทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">ผู้ดูแลระบบทั้งหมด <small>จัดการผู้ดูแลระบบ</small></h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
			<a href="/admin/admin/add" class="btn btn-success"><i class="fa fa-plus"></i> สร้างผู้ดูแลระบบ</a>
		</div>
			<br><br>
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รายละผู้ดูแลระบบ</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($admins as $key=> $admin)
						<tr>
							<td class="text-center">{{$key+1}}</td>
							<td>
								<b>ชื่อผู้ดูแล: </b>{{$admin->name}}<br>
								<b>อีเมลล์: </b>{{$admin->email}}<br>
								<b>สร้างเมื่อ: </b>{{$admin->created_at}}<br>
								<b>อัพเดทล่าสุดเมื่อ: </b>{{$admin->updated_at}}
							</td>
							<td>
								@if($admin->status==0)
								<span class="badge badge-danger">ระงับการใช้งาน</span>
								@else
								<span class="badge badge-success">ปกติ</span>
								@endif
							</td>
							<td class="text-center">
								@if($admin->status==1)
								<a href="/admin/admin/suspend/{{$admin->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับผู้ดูแลระบบ{{$admin->name}}? ')"   class="btn btn-danger"><i class="si si-ban"></i> ระงับ</a>
								@else
								<a href="/admin/admin/unsuspend/{{$admin->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับผู้ดูแลระบบ{{$admin->name}}? ')"   class="btn btn-success"><i class="si si-reload"></i> ยกเลิกระงับ</a>
								@endif

								<a href="/admin/admin/get/{{$admin->id}}" class="btn btn-primary"><i class="fa fa-info"></i> อัพเดทข้อมูล</a>
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
		$("#adminmanagebtn").addClass("active");
	</script>
@endsection