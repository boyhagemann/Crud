<h1>
	@if($title)
	{{ $title }}
	@else
	Edit
	@endif
</h1>
<br>

<div class="col-lg-12">

{{ Form::model($model, array('route' => array($route . '.update', $model->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}
{{ Form::renderFields($form, $errors) }}
{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
{{ Form::close() }}

</div>
