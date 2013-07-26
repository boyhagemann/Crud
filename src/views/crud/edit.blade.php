Edit

{{ Form::model($model, array('action' => array($action, $model->id), 'method' => 'PUT')) }}
{{ Form::renderFields($form, $errors) }}

{{ Form::submit('Save') }}
{{ Form::close() }}