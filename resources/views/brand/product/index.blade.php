@extends('master')
@section('title','สินค้าทั้งหมด')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">สินค้าทั้งหมด <small>All Products</small></h2>
	<div class="block">
		<div class="block-content">
			<div style="width: 100%; text-align: right;">
				<a href="/products/add" class="btn btn-success"><i class="fa fa-plus"></i> เพิ่มสินค้า</a><br><br>
			</div>
			<form method="POST" action="/products">
				{{csrf_field()}}
			<div class="form-group row">
				<label class="col-12">เลือกแบรนด์</label>
				<div class="col-12">
					<select id="brandselect" name="brand_id" class="form-control">
						@foreach($users as $user)
						<option value="{{$user->id}}">{{$user->brand_name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-12 text-right">
					<br>
					<button class="btn btn-primary" type="submit">เรียกดู</button>
				</div>
			</div>
		</form>
			@if(isset($products))
			<div class="table-responsive">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#</td>
							<td class="text-center">รูปสินค้า</td>
							<td class="text-center">ข้อมูลสินค้า</td>
							<td class="text-center">สถานะ</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($products as $product)
						<?php
							$variants = $product->getVariant;
						?>
						<tr>
							<td>{{$product->id}}</td>
							<td><img src="{{$product->pic_url}}" style="width: 200px;"></td>
							<td>
								<b>ชื่อ: </b> {{$product->name}} <br>
								<b>แบรนด์: </b> {{$product->getUser->brand_name}} <br>
								<b>ราคา: </b> {{number_format($product->price,2)}} บาท<br>
								<b>หน่วยนับหลัก: {{$product->getUnit->name}}</b><br>
								<b>Tag</b>: <font style="color:blue;"> @foreach($product->getTags as $tag){{$tag->getTagDetail->name}} @endforeach</font><br>
								<b>Variant:</b><br> 
									@foreach($variants as $var)
									-  {{$var->variant}}<br>
									@endforeach
								</b>
							</td>
							<td>
								@if($product->status==0)
								<span class="badge badge-danger">ระงับการขาย</span>
								@else
								<span class="badge badge-success">ปกติ</span>
								@endif
							</td>
							<td class="text-center">
								@if($product->status==1)
								<a style="margin-left:5px;" href="/products/suspend/{{$product->id}}" onclick="return confirm('คุณมั่นใจที่จะระงับการขายสินค้า {{$product->name}}? ')"   class="btn btn-danger"><i class="si si-ban"></i> ระงับ</a>
								@else
								<a style="margin-left:5px;" href="/products/unsuspend/{{$product->id}}" onclick="return confirm('คุณมั่นใจที่จะยกเลิกการระงับการขายสินค้า {{$product->name}}? ')"   class="btn btn-success"><i class="si si-reload"></i> ยกเลิกระงับ</a>
								@endif
								<a style="margin-left:5px;" href="/products/get/{{$product->id}}" class="btn btn-primary"><i class="fa fa-info"></i> ดูสินค้า</a>
							</td>
						</tr>
						@endforeach
					</tbody>
					
				</table>
			</div>
			@endif
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
<script src="/assets/js/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
	$('.data-table').dataTable({
		dom: 'Bfrtip',
		buttons: [
            'excelHtml5'
        ]}
		);
	$("#productbtn").addClass("active");
	$("#brandselect").select2();
</script>
@endsection