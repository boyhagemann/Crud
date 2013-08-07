<h2>Overview</h2>

<table class="table table-striped">
	<tr>
		<thead>
		@foreach($overview->labels() as $label)
		<th>{{ $label }}</th>
		@endforeach
		<th></th>
		</thead>
	</tr>
	<tbody>
	@foreach($overview->rows() as $id => $row)
	<tr>
		@foreach($row->columns() as $column)
		<td>{{ $column }}</td>
		@endforeach
		<td class="col-2">
			{{ Form::open(array('route' => array($route . '.destroy', $id), 'method' => 'DELETE')) }}
			<a href="{{ URL::route($route . '.edit', $id) }}" class="btn btn-small btn-primary">Edit</a>
			{{ Form::submit('Delete', array('class' => 'btn btn-small')) }}
			{{ Form::close() }}
		</td>
	</tr>
	@endforeach
	</tbody>
</table>

{{ $overview->links() }}

<div>
	<a href="{{ URL::route($route . '.create') }}">Create</a>
</div>