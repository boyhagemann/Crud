
<ul>
    @foreach($controllers as $key => $class)
    <li><a href="{{ URL::action('Boyhagemann\Crud\Manager\ManagerController@manage', $key) }}">{{ $class->getName() }}</a></li>
    @endforeach
</ul>