Edit

{{ Form::model($model, array('route' => array($route . '.update', $model->id), 'method' => 'PUT')) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save') }}
{{ Form::close() }}

<div>
    <a href="{{ URL::route($route . '.index') }}">To overview</a>
</div>