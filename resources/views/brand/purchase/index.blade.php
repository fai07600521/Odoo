@extends('master')
@section('title','ใบนำเข้าสินค้าทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">ใบนำเข้าสินค้าทั้งหมด</h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
				<a href="/purchase/add" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มใบนำเข้าสินค้า</a><br><br>
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
						@foreach($purchases as $purchase)
						<tr>
							<td>{{$purchase->id}}</td>
							<td>
								<b>แบรนด์</b> : {{$purchase->getUser ? $purchase->getUser->brand_name : "ddd"}}<br>
								<b>เข้าสาขา</b> : {{$purchase->getBranch->name}}<br>
								<b>วันที่</b> : {{$purchase->shipdate}}
							</td>
							<td class="text-center">
								@if($purchase->status==9)
								<span class="badge badge-danger">ยกเลิกใบนำเข้า</span>
								@elseif($purchase->status==0)
								<span class="badge badge-primary">รอทางร้านตอบรับ</span>
								@else
								<span class="badge badge-success">นำเข้าเรียบร้อยแล้ว</span>
								@endif
							</td>
							<td class="text-center">
								@if($purchase->status==0)
								<a style="margin-left:5px;" href="/purchase/cancel/{{$purchase->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกใบนำเข้านี้ใช่หรือไม่? ')"   class="btn btn-danger"><i class="si si-ban"></i> ยกเลิกใบนำเข้า</a>
								@else
								@endif
								<a style="margin-left:5px;" href="/purchase/get/{{$purchase->id}}" class="btn btn-primary"><i class="fa fa-info"></i> ดูใบนำเข้า</a>
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