@extends('master')
@section('title','ออเดอร์ทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">ออเดอร์ทั้งหมด</h2>
	<div class="block">
		<div class="block-content">
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">เลขที่บิล</td>
							<td class="text-center">รายละเอียด</td>
							<td class="text-center">รูปแบบการชำระเงิน</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($invoices as $invoice)
						<?php
							$sumorder = \App\Http\Controllers\AdminController::getSumOrder($invoice->id);
						?>
						<tr>
							<td class="text-center">{{$invoice->tax_id}}</td>
							<td>
								<b>สาขาที่ทำรายการ</b> : {{$invoice->getBranch->name}}<br>
								<b>ผู้ขาย</b> : {{$invoice->getUser->name}}<br>
								<b>ยอดรวม</b> : {{number_format($sumorder ,2)}} บาท<br>
								<b>วันที่</b> : {{$invoice->created_at}}
							</td>
							<td class="text-center">
								{{$invoice->getPaymentType->name}}
							</td>
							<td class="text-center">
								@if($invoice->status==9)
								<span class="badge badge-danger">ยกเลิกบิลขาย</span>
								@elseif($invoice->status==1)
								<span class="badge badge-primary">ชำระเงินเรียบร้อย</span>
								@else
								<span class="badge badge-warning">ไม่ทราบสถานะ</span>
								@endif
							</td>
							<td class="text-center">
								<a target="_blank" style="margin-left:5px;" href="/admin/pos/slip/{{$invoice->id}}" class="btn btn-success"><i class="fa fa-print"></i> พิมพ์ใบเสร็จ</a>
								<a style="margin-left:5px;" href="/admin/order/get/{{$invoice->id}}" class="btn btn-primary"><i class="fa fa-info"></i> ดูรายการนี้</a>
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
<script src="//cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.colVis.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.flash.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.html5.min.js"></script>
<script src="/assets/js/plugins/datatables/buttons/buttons.print.min.js"></script>
<script type="text/javascript">
	$('.data-table').dataTable({
        "order": [[ 0, "desc" ]],
		dom: 'Bfrtip',
		buttons: [
            'excelHtml5'
        ]
    } );
	$("#reportorderbtn").addClass("active");
</script>
@endsection