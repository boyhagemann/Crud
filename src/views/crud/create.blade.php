<h2>Create</h2>

{{ Form::model($model, array('route' => $route . '.store', 'class' => 'form-horizontal')) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
{{ Form::close() }}

<div>
    <a href="{{ URL::route($route . '.index') }}">To overview</a>
</div>