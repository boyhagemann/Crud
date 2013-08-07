<div class="page-header">
	<h1>
		@if($title)
		<a href="{{ URL::route($route . '.index') }}">{{{ $title }}}</a> <small>Create</small>
		@else
			<a href="{{ URL::route($route . '.index') }}">Create</a>
		@endif
		<small class="pull-right"><a href="{{ URL::route($route . '.create') }}" class="btn-primary btn">Create</a></small>
	</h1>
</div>

{{ Form::model($model, array('route' => $route . '.store', 'class' => 'form-horizontal')) }}
{{ Form::renderFields($form, $errors) }}
{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
{{ Form::close() }}