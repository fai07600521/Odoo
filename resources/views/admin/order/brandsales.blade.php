@extends('master')
@section('title','รายงานยอดขายแบรนด์รวม')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css">
<style type="text/css">
	@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
	}
</style>
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">ยอดขายแบรนด์ <small>ทั้งหมด</small></h2>
	<div class="block no-print">
		<div class="block-content">
			<form method="POST" action="/admin/brandsales">
				{{csrf_field()}}
				<div class="block-content">
					<div class="form-group row">
						
							<label class="col-12">สาขาที่ต้องการเรียกดูยอด</label>
							<div class="col-12">
								<select class="form-control" name="branch_id">
									<option value="">ทั้งหมด</option>
									@foreach($branchs as $branch)

									<option value="{{$branch->id}}">{{$branch->name}}</option>

									@endforeach
								</select>
								<br>
							</div>
						
						<div class="col-6">
							<div class="row">
								<label class="col-12">วันที่เริ่มต้น</label>
								<div class="col-12">
									<input type="text" class="js-datepicker form-control" name="startdate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required="" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="row">
								<label class="col-12">วันที่สิ้นสุด</label>
								<div class="col-12">
									<input type="text" class="js-datepicker form-control" name="enddate" data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" required="" autocomplete="off">
								</div>
							</div>
						</div>

					</div>
					<div class="form-group row">
						<div class="col-12 text-right">
							<a href="/admin/brandsales" class="btn btn-warning"><i class="si si-magnifier"></i> เรียกดูข้อมูลทั้งหมด</a>
							<button type="submit" class="btn btn-info"><i class="si si-magnifier"></i> เรียกดูข้อมูล</button>
						</div>
						<br><br>
					</div>
				</div>
			</form>
		</div>
	</div>
	@if(isset($branch_id))
	<div class="block">
		<div class="block-content">
			<div class="row">

				<div class="col-12 text-center">
					<h2>ยอดขายรายแบรนด์ สาขา {{$branchname}}</h2>
					<p>{{$startdate}} {{$enddate!=""?'-':''}} {{$enddate}}</p>
				</div>
				<div class="col-12">
					<div class="table-responsive">
						<table class="table table-hover data-table">
							<thead>
								<tr>
									<td class="text-center">ชื่อแบรนด์</td>
									<td class="text-center">GP</td>
									<td class="text-center">ยอดขาย</td>
									<td class="text-center">ส่วนลด</td>
									<td class="text-center">ยอดขายหลังหักส่วนลด</td>
									<td class="text-center">คำนวณ GP</td>
									<td class="text-center">ยอดหลังหัก GP</td>
									<td class="text-center">การกระทำ</td>
								</tr>
							</thead>
							<tbody>
								@foreach($brands as $brand)
								<?php
									$discount = $discountreport[$brand->id];

								?>
								<tr style="background-color:{{$brand->vat==1?'#e7fff4':'#e7e7ff'}};">
									<td>{{$brand->brand_name}} {{$brand->vat==1?'(VAT)':''}}</td>
									<td class="text-center">{{$gp[$brand->id]}}</td>
									<td class="text-right">{{number_format($report[$brand->id],2)}}</td>
									<td class="text-right">{{number_format($discount,2)}}</td>
									<td class="text-right">{{number_format($report[$brand->id]-$discount,2)}}</td>
									<td class="text-right">
										@if($brand->vat==1)
										{{number_format((($report[$brand->id]-$discount)/1.07)*$gp[$brand->id]/100,2)}}
										@else
										{{number_format(($report[$brand->id]-$discount)*$gp[$brand->id]/100,2)}}
										@endif

									</td>
									</td>
									<td class="text-right">
										@if($brand->vat==1)
										{{number_format(($report[$brand->id]-$discount)-((($report[$brand->id]-$discount)/1.07)*$gp[$brand->id]/100),2)}}
										@else
										{{number_format(($report[$brand->id]-$discount)-(($report[$brand->id]-$discount)*$gp[$brand->id]/100),2)}}
										@endif
									</td>
									<td class="text-center">
										<form method="POST" action="/admin/report" target="_blank">
											{{csrf_field()}}
											<input hidden="" name="branch_id" value="{{$branch_id}}">
											<input hidden="" name="start_date" value="{{$startdate}}">
											<input hidden="" name="end_date" value="{{$enddate}}">
											<input hidden="" name="brand_id" value="{{$brand->id}}">
											<button class="btn btn-info">เรียกดูรายละเอียด</button>
										</form>
									</td>
								</tr>
								@endforeach
							</tbody>

						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
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

<script src="/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
	var table = $('.data-table').dataTable({
		"order": [[ 3, "desc" ]],
		dom: 'Bfrtip',
		buttons: [
            'excelHtml5'
        ]
	});

	$("#reportbrandbtn").addClass("active");
</script>
<script>jQuery(function(){ Codebase.helpers(['datepicker']); });</script>
@endsection