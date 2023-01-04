@extends('master')
@section('title','ส่วนลดเงิน')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
@endsection
@section('content')
<div class="content">
	<h2 class="content-heading">ปรับปรุงยอดสต๊อกสินค้า</h2>
	<div class="col-12">
		<div class="block">
			<div class="block-content">
				<form method="POST" action="/admin/promotions/discountprice">
					{{csrf_field()}}
					<div class="form-group row">
							<label class="col-12">เลือกสินค้า</label>
							<div class="col-12">
							<select id="product" class="js-select2 form-control"  style="width: 100%;" data-placeholder="เลือกสินค้า" name="products[]" multiple="multiple" required="">
								<option value="">เลือกสินค้า</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
							<label class="col-12">กรอกราคา</label>
							<div class="col-12">
							<input type="number" class="form-control" name="price" required="" placeholder="กรุณากรอกราคาเช่นต้องการลดจาก 150 เหลือ 100 ให้กรอก 100">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-12 text-right">
							<button type="submit" class="btn btn-success">บันทึก</button>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
		<h2 class="content-heading">สินค้าที่เล่นตัวเลือกส่วนลดนี้</h2>
	<div class="col-12">
		<div class="block">
			<div class="block-content">
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">ชื่อสินค้า</td>
							<td class="text-center">ราคาปกติ</td>
							<td class="text-center">ราคาโปรโมชั่น</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($targetproducts as $product)

						<tr>
							<td>{{$product->name}}</td>
							<td>{{$product->price}}</td>
							<td>{{$product->discount_price}}</td>
							<td class="text-center">
								<a style="margin-left:5px;" href="/admin/promotions/discountprice/print/{{$product->id}}" class="btn btn-warning">พิมพ์ป้าย</a>
								<a style="margin-left:5px;" href="/admin/promotions/discountprice/{{$product->id}}" class="btn btn-danger">ลบ</a>
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
<script src="/assets/js/plugins/select2/js/select2.full.min.js"></script>
<script src="/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript">
	$("#promotionbtn").addClass("active");
	$('#product').select2({
		placeholder: 'เลือกสินค้า',
		ajax: {
			dataType: 'json',
			method: 'post',
			url: '/admin/promotions/discountprice/apigetproduct',
			delay: 200,
			data: function(params) {
				return {
					term: params.term
				}
			},
			processResults: function (data, page) {
				return {
					results: data
				};
			},
		}
	});	
	$('.data-table').dataTable();
</script>
@endsection