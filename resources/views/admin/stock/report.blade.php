@extends('master')
@section('title','รายงานสต๊อกสินค้า')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">รายงานสต๊อกสินค้า2</h2>
	<div class="col-12">
		<div class="block">
			<form method="POST" action="/admin/stock/report">
				{{csrf_field()}}
				<div class="block-content">
					<h4>เลือกดูสต๊อกรายแบรนด์</h4>
					<div class="row">
						<div class="col-12">
							<div class="form-group row">
								<label class="col-12" for="name">แบรนด์</label>
								<div class="col-md-12">
									<select id="product" class="js-select2 form-control" id="example-select2" style="width: 100%;" data-placeholder="เลือกแบรนด์" name="user_id">
										<option></option>
										<option value="0">ดูทั้งหมด</option>
										@foreach($users as $tmp)
										<option value="{{$tmp->id}}">{{$tmp->brand_name}}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-12">
									<input style="margin-left:5px;" type="submit" value="เรียกดู" class="form-control btn btn-info pull-right">
									<br><br>
								</div>
							</div>

						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	@if($flag!=0)
	<div class="col-12">
		<div class="block">
			<div class="block-content">
				@if($flag==1)
				<h4>สต๊อกสินค้า: {{$user->brand_name}}</h4>
				<table class="table table-responsive table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">แบรนด์</td>
							<td class="text-center">รายละเอียดสินค้า</td>
							@foreach($branchs as $branch)
							<td class="text-center">{{$branch->name}}</td>
							@endforeach
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($user->getProduct as $product)
						@if($product->status!=1)
						@continue
						@endif
						@foreach($product->getVariant as $variant)
						<?php
						$stock_count = \App\Http\Controllers\AdminController::getOnhand($variant->id);
						?>
						<tr>
							<td>{{$product->getUser->brand_name}}</td>
							<td>
								<b>รหัสสินค้า: </b> {{$variant->id}}
								<b>ชื่อสินค้า:</b> {{$product->name}} ({{$variant->variant}})<br>
								
								<b>ราคา: </b> {{$product->price}} บาท

							</td>
							@foreach($branchs as $branch)

							<td>{{$stock_count[$branch->id]}} </td>

							@endforeach
							<td>
								<a href="/product/move/{{$variant->id}}" class="btn btn-info"><i class="si si-magnifier"></i> ดูความเคลื่อนไหว
								</a>
							</td>
						</tr>
						@endforeach
						@endforeach
					</tbody>
					
				</table>
				@else
				<h4>สต๊อกสินค้าทั้งหมด</h4>
				<table class="table table-responsive table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">แบรนด์</td>
							<td class="text-center">รายละเอียดสินค้า</td>
							@foreach($branchs as $branch)
							<td class="text-center">{{$branch->name}}</td>
							@endforeach
							
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($results as $result)
							@if(empty($result["product_id"]))
							@continue
							@endif
						<tr>
							<td>{{$result["brand_name"]}}</td>
							<td>
								<b>รหัสสินค้า: </b> {{$result["product_id"]}}
								<b>ชื่อสินค้า:</b> {{$result["product_name"]}}<br>
								<b>ราคา: </b>  {{$result["price"]}} บาท
							</td>
							@foreach($branchs as $branch)

							<td> {{$result["stock"][$branch->id]}}</td>

							@endforeach
							<td class="text-center">
								<a href="/product/move/{{$result['product_id']}}" class="btn btn-info"><i class="si si-magnifier"></i> ดูความเคลื่อนไหว
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
					
				</table>
				@endif

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
<script src="/assets/js/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
	$("#reportstockbtn").addClass("active");
</script>
<script type="text/javascript">
	$('.data-table').dataTable({
		dom: 'Bfrtip',
		buttons: [
            'excelHtml5'
        ],
		"responsive": true,  
		"autoWidth": false,
	});
</script>
<script>jQuery(function(){ Codebase.helpers(['select2']); });</script>
@endsection