@extends('master')
@section('title','จัดการแบรนด์ทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">แบรนด์ทั้งหมด <small>จัดการแบรนด์</small></h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
			<a href="/admin/brand/add" class="btn btn-success"><i class="fa fa-plus"></i> สร้างแบรนด์</a>
		</div>
			<br><br>
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รายละเอียดแบรนด์</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($brands as $key=> $brand)
						<tr>
							<td class="text-center">{{$key+1}}</td>
							<td>
								<b>ชื่อแบรนด์: </b>{{$brand->brand_name}}<br>
								<b>ผู้ดูแลแบรนด์: </b>{{$brand->name}}<br>
								<b>อีเมลล์: </b>{{$brand->email}}<br>
								<b>Line: </b>{{$brand->line}}<br>
								<b>REF: </b>{{$brand->ref}}<br>
								<b>สร้างเมื่อ: </b>{{$brand->created_at}}<br>
								<b>อัพเดทล่าสุดเมื่อ: </b>{{$brand->updated_at}}
							</td>
							<td>
								@if($brand->status==0)
								<span class="badge badge-danger">ระงับการใช้งาน</span>
								@else
								<span class="badge badge-success">ปกติ</span>
								@endif
							</td>
							<td class="text-center">
								@if($brand->status==1)
								<a href="/admin/brand/suspend/{{$brand->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับแบรนด์{{$brand->brand_name}}? ')"   class="btn btn-danger"><i class="si si-ban"></i> ระงับ</a>
								@else
								<a href="/admin/brand/unsuspend/{{$brand->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับแบรนด์{{$brand->brand_name}}? ')"   class="btn btn-success"><i class="si si-reload"></i> ยกเลิกระงับ</a>
								@endif

								<a href="/admin/brand/get/{{$brand->id}}" class="btn btn-primary"><i class="fa fa-info"></i> อัพเดทข้อมูล</a>
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
		$("#brandmanagebtn").addClass("active");
	</script>
@endsection