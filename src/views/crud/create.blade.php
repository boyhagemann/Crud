<div class="page-header">
	<h1>
		@if($title)
		{{{ $title }}} <small>Create</small>
		@else
		Create
		@endif
		<small class="pull-right"><a href="{{ URL::route($route . '.index') }}" class="btn-primary btn">Overview</a></small>
	</h1>
</div>

<div class="col-lg-12">

{{ Form::model($model, array('route' => $route . '.store', 'class' => 'form-horizontal')) }}
{{ Form::renderFields($form, $errors) }}
{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
{{ Form::close() }}

</div>