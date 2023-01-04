@extends('master')
@section('title','คู่มือการใช้งาน')
@section('content')
<div class="content">
	<h2 class="content-heading">{{$help->title}}</h2>
	<div class="col-12">
		<div class="block">
			<div class="block-content">
				<p>{!!$help->detail!!}</p>
			</div>
		</div>
	</div>
</div>


@endsection
@section('script')
<script type="text/javascript">
	$("#help{{$help->id}}btn").addClass("active");
</script>
@endsection