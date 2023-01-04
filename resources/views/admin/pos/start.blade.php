@extends('master')
@section('title','กำหนดเงินทอนเริ่มต้น')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">กำหนดเงินทอน</h2>
	<div class="block">
		<div class="block-content">
			<div class="row">
				<div class="col-6">
					<h3>กำหนดเงินทอน</h3>
				</div>
				<div class="col-6 text-right">
					<h3>จำนวนเงินรวม: <font id="sum">0</font> บาท</h3>
				</div>
			</div>
			
			<form method="POST" action="{{isset($start)?'/admin/posstart/update':'/admin/posstart/add'}}">
				{{csrf_field()}}
				@if(isset($start))
				<input name="id" hidden="" value="{{$start->id}}">
				@endif

				<div class="form-group row">
					<label class="col-12">เลือกสาขา</label>
					<div class="col-12">
						<select class="form-control" name="branch_id">
							@if(isset($start))
							@foreach($branchs as $branch)
							<option value="{{$branch->id}}" {{$start->branch_id==$branch->id?'selected':''}}>{{$branch->name}}</option>
							@endforeach
							@else
							@foreach($branchs as $branch)
							<option value="{{$branch->id}}">{{$branch->name}}</option>
							@endforeach
							@endif
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-12">แบ๊ง 1,000 (ฉบับ)</label>
					<div class="col-12">
						<input data-cal="1000" value="{{isset($start)?$start->onethousand:''}}" name="onethousand" type="number" placeholder="กรอกจำนวนแบ๊ง 1,000" class="form-control calsum">
					</div>
				</div>

				<div class="form-group row">
					<label class="col-12">แบ๊ง 500 (ฉบับ)</label>
					<div class="col-12">
						<input data-cal="500" value="{{isset($start)?$start->fivehundred:''}}" name="fivehundred" type="number" placeholder="กรอกจำนวนแบ๊ง 500" class="form-control calsum">
					</div>
				</div>

				<div class="form-group row">
					<label class="col-12">แบ๊ง 100 (ฉบับ)</label>
					<div class="col-12">
						<input data-cal="100" value="{{isset($start)?$start->onehundred:''}}" name="onehundred" type="number" placeholder="กรอกจำนวนแบ๊ง 100" class="form-control calsum">
					</div>
				</div>

				<div class="form-group row">
					<label class="col-12">แบ๊ง 50 (ฉบับ)</label>
					<div class="col-12">
						<input data-cal="50" value="{{isset($start)?$start->fifty:''}}" name="fifty" type="number" placeholder="กรอกจำนวนแบ๊ง 50" class="form-control calsum">
					</div>
				</div>


				<div class="form-group row">
					<label class="col-12">แบ๊ง 20 (ฉบับ)</label>
					<div class="col-12">
						<input data-cal="20" value="{{isset($start)?$start->twenty:''}}" name="twenty" type="number" placeholder="กรอกจำนวนแบ๊ง 20" class="form-control calsum">
					</div>
				</div>


				<div class="form-group row">
					<label class="col-12">เหรียญ 10</label>
					<div class="col-12">
						<input data-cal="10" value="{{isset($start)?$start->ten:''}}" name="ten" type="number" placeholder="กรอกจำนวนเหรียญ 10" class="form-control calsum">
					</div>
				</div>


				<div class="form-group row">
					<label class="col-12">เหรียญ 5</label>
					<div class="col-12">
						<input data-cal="5" value="{{isset($start)?$start->five:''}}" name="five" type="number" placeholder="กรอกจำนวนเหรียญ 5" class="form-control calsum">
					</div>
				</div>


				<div class="form-group row">
					<label class="col-12">เหรียญ 2</label>
					<div class="col-12">
						<input data-cal="2" value="{{isset($start)?$start->two:''}}" name="two" type="number" placeholder="กรอกจำนวนเหรียญ 2" class="form-control calsum">
					</div>
				</div>


				<div class="form-group row">
					<label class="col-12">เหรียญ 1</label>
					<div class="col-12">
						<input data-cal="1" value="{{isset($start)?$start->one:''}}" name="one" type="number" placeholder="กรอกจำนวนเหรียญ 1" class="form-control calsum">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-12 text-right">
						@if(isset($start))
						<button type="submit" class="btn btn-success">บันทึก</button>
						@else
						<button type="submit" class="btn btn-primary">เพิ่ม</button>
						
						@endif
					</div>
				</div>

			</form>
		</div>
	</div>





	<h2 class="content-heading">เงินทอนเริ่มต้นทั้งหมด</h2>
	<div class="block">
		<div class="block-content">
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">วันที่</td>
							<td class="text-center">สาขา</td>
							<td class="text-center">รายละเอียด</td>
							<td class="text-center">รวมเงินเริ่มต้น</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($starts as $start)				
						<?php
						$sum = ($start->onethousand*1000)+($start->fivehundred*500)+($start->onehundred*100)+($start->fifty*50)+($start->twenty*20)+($start->ten*10)+($start->five*5)+($start->two*2)+($start->one);
						?>		

						<tr>
							<td class="text-center">{{$start->created_at}}</td>
							<td>{{$start->getBranch->name}}</td>
							<td>
								<b>1,000</b> : {{$start->onethousand}} ฉบับ<br>
								<b>500</b> : {{$start->fivehundred}} ฉบับ<br>
								<b>100</b> : {{$start->onehundred}} ฉบับ<br>
								<b>50</b> :  {{$start->fifty}} ฉบับ<br>
								<b>20</b> :  {{$start->twenty}} ฉบับ<br>
								<b>10</b> :  {{$start->ten}} เหรียญ<br>
								<b>5</b> :  {{$start->five}} เหรียญ<br>
								<b>2</b> :  {{$start->two}} เหรียญ<br>
								<b>1</b> :  {{$start->one}} เหรียญ<br>

							</td>
							<td class="text-center">
								{{number_format($sum,2)}}
							</td>
							<td class="text-center">
								<a style="margin-left:5px;" href="/admin/posstart/get/{{$start->id}}" class="btn btn-success"><i class="fa fa-cogs"></i> แก้ไข</a>

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
		"order": [[ 0, "desc" ]]
	} );
	$("#posstartbtn").addClass("active");
	$(".calsum").on('change',function(){
		fields = $(".calsum");
		sum = 0;
		$.each(fields, function (index, item) {
			
			sum = sum + ($(item).attr('data-cal')*$(item).val());
		});
		$("#sum").html(sum);
	});
</script>
@endsection