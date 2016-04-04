@extends('back')

@section('content')

	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h3 class="page-header">{{ $title }}</h3>
				</div>
			</div>
			<!-- Dashboard top panels -->
			@if(Entrust::hasRole('admin'))
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-group fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div class="huge">{{ $numberUsers }}</div>
									<div>{{ trans('back.headers.users') }}</div>
								</div>
							</div>
						</div>
						<a href="{{ route('users.index') }}">
							<div class="panel-footer">
								<span class="pull-left">{{ trans('back.dashboard.viewUsers') }}</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
			</div>

			@endif

		</div>
	</div>

@stop
