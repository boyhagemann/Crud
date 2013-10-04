<div class="page-header">
	<h1>Create a new resource</h1>
</div>

<div class="col-12">
{{ Form::render($form) }}
</div>

<hr>

<a href="{{ URL::action('Boyhagemann\Crud\ManagerController@index') }}" class="">Back to the resource manager</a>
