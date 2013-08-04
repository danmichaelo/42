@extends('master')
@section('container')
<h2>Subject #{{$subjects[0]->id}}</h2>

<p>
Tittel: {{$subjects[0]->label_nb}}
</p>

<p>
Emner:
<?php $last = ''; ?>
@foreach ($subjects[0]->objects()->get() as $obj)
	<a href="/objects/{{$obj->id}}" class="tag">{{$obj->title}}</a>
@endforeach
</p>


@stop
