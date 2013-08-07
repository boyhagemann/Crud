<h2>Edit</h2>

{{ Form::model($model, array('route' => array($route . '.update', $model->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
{{ Form::close() }}

<div>
    <a href="{{ URL::route($route . '.index') }}">To overview</a>
</div>