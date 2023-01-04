@extends('master')
@section('title','รายงานสินค้าคงเหลือ')
@section('content')
<div class="content">
	<h2 class="content-heading">รายงานสินค้าคงเหลือ</h2>
	<div class="col-12">
		<div class="block" style="padding: 20px;">
			<div class="row">
				<div class="col-12 text-center">
					<h3>รายงานสินค้าคงเหลือ</h3>
				</div>
				<div class="col-12">
					<table class="table">

						<tr>
							<td class="text-center">สินค้า</td>
							<td class="text-center">คงเหลือ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
						@if(sizeOf($products)!=0)
						@foreach($products as $product)
						@foreach($product->getVariant as $variant)
						<?php
						$stock_count = \App\Http\Controllers\BrandController::getOnhand($variant,$branchs);
						?>
						<tr>
							<td>{{$product->name}} ({{$variant->variant}})</td>
							<td>
								{!!$stock_count!!}
							</td>
							<td class="text-center">
								<a href="/product/move/{{$variant->id}}" class="btn btn-info"><i class="si si-magnifier"></i> ดูความเคลื่อนไหว
								</a>
							</td>
						</tr>
						@endforeach
						@endforeach
						@else
						<tr>
							<td colspan="4" class="text-center">ยังไม่มีสินค้าในระบบ</td>
						</tr>
						@endif
					</table>
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
@endsection