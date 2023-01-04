@extends('master')
@section('title','รายการซื้อสินค้า')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">รายการซื้อสินค้า <small>{{$member->name}}</small></h2>
	<div class="block">
		<div class="block-content">
			<br><br>
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">รายละเอียดรายการขาย</td>
							<td class="text-center">รายการ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($member->getOrder as $key => $order)
						<tr>
							<td>
								เลขที่ออเดอร์ : {{$order->tax_id}}<br>
								สาขา : {{$order->getBranch->name}}<br>
								ผู้ขาย: {{$order->getUser->name}}
							</td>
							<td>
								@foreach($order->getItem as $item)
								- {{$item->getProductVariant->getProduct->name}} จำนวน {{$item->quantity}} ชิ้น <br>
								@endforeach
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