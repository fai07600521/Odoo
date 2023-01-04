@extends('master')
@section('title','จัดการออเดอร์')
@section('content')
<div class="content">
	<h2 class="content-heading">ออเดอร์เลขที่# {{$invoice->tax_id}}</h2>
	<div class="col-12">
		<div class="block">
			<div class="block-content">
				<h4>ออเดอร์เลขที่# {{$invoice->tax_id}}
					@if($invoice->status==9)
					<span class="badge badge-danger">ยกเลิกบิลขาย</span>
					@elseif($invoice->status==1)
					<span class="badge badge-success">ชำระเงินเรียบร้อย</span>
					@else
					<span class="badge badge-warning">ไม่ทราบสถานะ</span>
					@endif
				</h4>
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<div class="row form-group">
							<label class="col-12">สาขาที่ขาย</label>
							<div class="col-12">
								<input class="form-control" type="text" disabled="" value="{{$invoice->getBranch->name}}">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-12">ผู้ขาย</label>
							<div class="col-12">
								<input class="form-control" type="text" disabled="" value="{{$invoice->getUser->name}}">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-12">สมาชิกที่ซื้อ</label>
							<div class="col-12">
								<input class="form-control" type="text" disabled="" value="{{$invoice->member_id!=0?$invoice->getMember->name.' ('.$invoice->getMember->detail.')':'ไม่เป็นสมาชิก'}}">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-12">รูปแบบการชำระเงิน</label>
							<div class="col-12">
								<input class="form-control" type="text" disabled="" value="{{$invoice->getPaymentType->name}}">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-12">วันเวลาที่ขาย</label>
							<div class="col-12">
								<input class="form-control" type="text" disabled="" value="{{$invoice->created_at}}">
							</div>
						</div>
						<div class="row form-group">
							<label class="col-12">หมายเหตุ</label>
							<div class="col-12">
								<input class="form-control" type="text" disabled="" value="{{$invoice->remark}}">
							</div>
						</div>

						<hr style="width: 100%; border: 1px solid #CCC">
						<h4>รายการสินค้า</h4>
						<table class="table table-vcenter">
							<thead>
								<tr>
									<th class="text-center">ชื่อสินค้า</th>
									<th class="text-center">จำนวน</th>
									<th class="text-center">ราคา</th>
									<th class="text-center">ส่วนลด</th>
									<th class="text-center">รวม</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$sum = 0;
								?>
								@foreach($invoice->getItem as $item)
								<tr>
									<td>{{$item->getProductVariant->getProduct->name}} ({{$item->getProductVariant->variant}})</td>
									<td class="text-center">{{$item->quantity}} {{$item->getProductVariant->getProduct->getUnit->name}}</td>
									<td class="text-center">
										{{number_format($item->price,2)}}
									</td>
									<td class="text-center">
										{{number_format(($item->price*$item->quantity)-$item->suminput,2)}}
									</td>
									<td class="text-center">
										{{number_format($item->suminput,2)}} บาท
									</td>
								</tr>
								<?php
								$sum +=  $item->suminput;
								?>
								@endforeach
							</tbody>
						</table>
						<?php
							$promotions = $invoice->getPromotion;
						?>
						@if(sizeOf($promotions)!=0)
						<hr style="width: 100%; border: 1px solid #CCC">
						<h4>ส่วนลด</h4>
						<table class="table table-vcenter">
							@foreach($promotions as $promotion)
							<tr>
								<td>{{$promotion->getPromotion->name}}</td>
								<td>{{number_format($promotion->discount,2)}} บาท</td>
							</tr>
							<?php
								$sum = $sum - $promotion->discount;
							?>
							@endforeach
						</table>
						@endif
						<hr style="width: 100%; border: 1px solid #CCC">
						<h4>รวมยอดใช้จ่าย</h4>
						<h3 class="pull-right">{{number_format($sum,2)}} บาท</h3><br><br>
						@if($invoice->status==1)
						<hr style="width: 100% border: 2px solid #ccc">
						<h4 style="color: red;">**หากต้องการยกเลิกบิลนี้กรุณากรอกเหตุผล</h4>
						<form method="POST" action="/admin/order/void">
							{{csrf_field()}}
							<input name="id" value="{{$invoice->id}}" hidden="">
							<div class="row form-group">
								<label class="col-12">เหตุผลในการยกเลิกบิล</label>
								<div class="col-12">
									<input class="form-control" type="text" required="" name="remark">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-12 text-right">
									<button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> ยกเลิกบิล</button>
								
							</div>
						</form>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	@endsection
	@section('script')
	<script type="text/javascript">
		$("#reportorderbtn").addClass("active");
	</script>
	@endsection