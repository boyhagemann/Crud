<h1>
	@if($title)
	{{ $title }}
	@else
	Create
	@endif
</h1>
<br>

<div class="col-lg-12">

{{ Form::model($model, array('route' => $route . '.store', 'class' => 'form-horizontal')) }}
{{ Form::renderFields($form, $errors) }}
{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
{{ Form::close() }}

</div>