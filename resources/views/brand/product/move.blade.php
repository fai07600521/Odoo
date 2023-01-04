@extends('master')
@section('title','ดูความเคลื่อนไหวสินค้า')
@section('content')
<div class="content">
	<div class="col-12">
		<h2 class="content-heading">ความเคลื่อนไหวสินค้า</h2>
		<div class="col-12">
			<div class="block" style="padding: 20px;">
				<div class="row">

					<div class="col-12">
						<button>Export</button>
						<table id="table2excel" class="table">
							<tr><td class="text-center" colspan="5"><h3>ความเคลื่อนไหว: {{$product->name}} ({{$product_variant->variant}})</h3></td></tr>
							<tr>
								<td class="text-center">วันที่ดำเนินการ</td>
								<td class="text-center">สาขา</td>
								<td class="text-center">รายละเอียดความเคลื่อนไหว</td>
								<td class="text-center">จำนวน</td>
								<td class="text-center">คงเหลือ</td>
							</tr>
							<tr style="background-color: #d2ebff;">
								<td class="text-center">{{$product->created_at}}</td>
								<td></td>
								<td class="text-center">เพิ่มสินค้าเข้าระบบ</td>
								<td class="text-center">0</td>
								<td class="text-center">0</td>
							</tr>
							@foreach($product_variant->getStock as $stock)
							@if($stock->type=="add")
							<tr style="background-color: #eaffd2;">
								@elseif($stock->type=="adjust")
								<tr style="background-color: #fad2ff">
									@else
									<tr style="background-color: #ffd2d2;">
										@endif
										<td class="text-center">{{$stock->created_at}}</td>
										<td class="text-center">{{$stock->getBranch->name}}</td>
										<td class="text-center">{{$stock->remark}}</td>
										<td class="text-center">{{$stock->quantity}}</td>
										<td class="text-center">{{number_format($stock->sum)}}</td>
									</tr>
									@endforeach
								</table>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endsection
		@section('script')
		<script type="text/javascript">
			$("#reportstockbtn").addClass("active");
		</script>
		<script src="/assets/js/jquery.table2excel.js"></script>
<script type="text/javascript">
$("button").click(function(){
  $("#table2excel").table2excel({
    name:"Productmove-{{$product->name}} ({{$product_variant->variant}})",
    filename:"Productmove-{{$product->name}} ({{$product_variant->variant}}).xls",
    fileext:"" 
  });
});


</script>
		@endsection