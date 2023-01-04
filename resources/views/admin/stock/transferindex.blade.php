@extends('master')
@section('title','ใบย้ายสินค้าทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">ใบย้ายสินค้าทั้งหมด</h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
				<a href="/admin/stock/create" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มใบย้ายสินค้า</a><br><br>
			</div>
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รายละเอียด</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($stockadjs as $stockadj)
						<tr>
							<td>{{$stockadj->id}}</td>
							<td>
								<b>ต้นทาง</b> : {{$stockadj->getSource->name}}<br>
								<b>ปลายทาง</b> : {{$stockadj->getDestination->name}}<br>
								<b>เหตุผล</b> : {{$stockadj->remark}}<br>
								<b>วันที่สร้าง</b> : {{$stockadj->created_at}}
							</td>
							<td class="text-center">
								@if($stockadj->status==9)
								<span class="badge badge-danger">ยกเลิกใบย้ายสินค้า</span>
								@elseif($stockadj->status==0)
								<span class="badge badge-primary">รอย้ายสินค้า</span>
								@else
								<span class="badge badge-success">ย้ายสินค้าเรียบร้อยแล้ว</span>
								@endif
							</td>
							<td class="text-center">
								@if($stockadj->status==0)
								<a style="margin-left:5px;" href="/admin/stock/transfer/cancel/{{$stockadj->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกใบย้ายสินค้านี้ใช่หรือไม่? ')"   class="btn btn-danger"><i class="si si-ban"></i> ยกเลิกใบย้ายสินค้า</a>
								@else
								@endif
								<a style="margin-left:5px;" href="/admin/stock/transfer/get/{{$stockadj->id}}" class="btn btn-primary"><i class="fa fa-info"></i> ดูใบย้ายสินค้า</a>
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
	$('.data-table').dataTable({
        "order": [[ 2, "desc" ]]
    } );
	$("#poallbtn").addClass("active");
</script>
@endsection