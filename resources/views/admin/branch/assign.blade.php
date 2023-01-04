@extends('master')
@section('title','จัดการสาขา')
@section('style')
<link rel="stylesheet" href="/assets/js/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/assets/js/plugins/datatables/dataTables.bootstrap4.css">
@endsection
@section('content')
<div class="content">
	@if(isset($branch))
	<h2 class="content-heading">{{$branch->name}}<small> จัดการสาขา</small></h2>
	<div class="col-12">
		<div class="block">
			<form method="POST" action="/admin/branch/assign">
				<input hidden="" name="branch_id" value="{{$branch->id}}">
				{{csrf_field()}}
				<div class="block-content">
					<h4>เพิ่มผู้ใช้ไปยังสาขา: {{$branch->name}}</h4>
					<div class="row">
						<div class="col-12">
							<div class="form-group row">
								<label class="col-12" for="name">แบรนด์</label>
								<div class="col-md-12">
									<select id="product" class="js-select2 form-control" id="example-select2" style="width: 100%;" data-placeholder="เลือกแบรนด์" name="user_id">
										<option></option>
										@foreach($users as $user)
										@if(in_array($user->id, $checkhas))
										@continue
										@endif
										<option value="{{$user->id}}">{{$user->role==1?$user->brand_name:$user->name." (Admin)"}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-12">
									<div class="form-group row">
										<label class="col-12">GP</label>
										<div class="col-12">
											<input class="form-control" name="gp" value="0" required="">
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-12 ">
									<input style="margin-left:5px;" type="submit" value="เพิ่ม" class="btn btn-success pull-right">
									<br><br>
								</div>
							</div>

						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="col-12">
		<div class="block">
			<div class="block-content">
				<h4>แบรนด์ที่อยู่ในสาขา: {{$branch->name}}</h4>
				<table class="table table-hover data-table">
					<thead>
						<tr>
							<td class="text-center">#ID</td>
							<td class="text-center">รายละเอียด</td>
							<td class="text-center">การกระทำ</td>
						</tr>
					</thead>
					<tbody>
						@foreach($branch->getItem as $key=>$item)
						<tr>
							<td class="text-center">{{$item->user_id}}</td>
							<td>
								{{$item->getUser->role==1?"ชื่อแบรนด์: ".$item->getUser->brand_name:"ชื่อผู้ดูแล: ".$item->getUser->name." (Admin)"}}
								 <br>
								GP: {{$item->gp}}
							</td>
							<td>
								<form method="POST" action="/admin/branch/remove">
									<input hidden="" name="branch_id" value="{{$branch->id}}">
									<input hidden="" name="user_id" value="{{$item->user_id}}">
									<input type="submit" class="btn btn-danger" value="ลบ">
									{{csrf_field()}}
								</form> 
							</td>
						</tr>
						@endforeach
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
	@endif
</div>
@endsection
@section('script')
<script src="/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="/assets/js/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
	$("#branchmanagebtn").addClass("active");
</script>
<script type="text/javascript">
	$('.data-table').dataTable();
</script>
<script>jQuery(function(){ Codebase.helpers(['select2']); });</script>
@endsection