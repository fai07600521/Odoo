@extends('master')
@section('title','จัดการสมาชิก')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">สมาชิกทั้งหมด <small>จัดการสมาชิก</small></h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
			<a href="/admin/member/add" class="btn btn-success"><i class="fa fa-plus"></i> สร้างสมาชิก</a>
		</div>
			<br><br>
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รายละสมาชิก</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($members as $key=> $member)
						<tr>
							<td class="text-center">{{$key+1}}</td>
							<td>
								<b>ชื่อสมาชิก: </b>{{$member->name}}<br>
								<b>รายละเอียด: </b>{{$member->detail}}<br>
								<b>สร้างเมื่อ: </b>{{$member->created_at}}<br>
								<b>อัพเดทล่าสุดเมื่อ: </b>{{$member->updated_at}}
							</td>
							<td>
								@if($member->status==0)
								<span class="badge badge-danger">ระงับการใช้งาน</span>
								@else
								<span class="badge badge-success">ปกติ</span>
								@endif
							</td>
							<td class="text-center">
								@if($member->status==1)
								<a href="/admin/member/suspend/{{$member->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับสมาชิก{{$member->name}}? ')"   class="btn btn-danger"><i class="si si-ban"></i> ระงับ</a>
								@else
								<a href="/admin/member/unsuspend/{{$member->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับสมาชิก{{$member->name}}? ')"   class="btn btn-success"><i class="si si-reload"></i> ยกเลิกระงับ</a>
								@endif
								<a href="/admin/member/order/{{$member->id}}"   class="btn btn-warning"><i class="si si-info"></i> รายการซื้อ</a>

								<a href="/admin/member/get/{{$member->id}}" class="btn btn-primary"><i class="fa fa-info"></i> อัพเดทข้อมูล</a>
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
		$("#membermanagebtn").addClass("active");
	</script>
@endsection