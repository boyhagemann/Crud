<h2>Used Crud Controllers</h2>

<ul>
    @foreach($controllers as $key => $class)
    <li><a href="{{ URL::action('Boyhagemann\Crud\ManagerController@manage', $key) }}">{{ $class->getName() }}</a></li>
    @endforeach
</ul>

<a href="{{ URL::action('Boyhagemann\Crud\ManagerController@scan') }}">Scan for new controllers</a>