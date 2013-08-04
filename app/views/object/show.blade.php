@extends('master')
@section('header')
42 : Objekter
@stop
@section('container')

<p>
	<a href="{{URL::action('ObjectController@index')}}">
		<i class="halflings-icon list"></i>
		Alle objekter
	</a>
</p>

<h2>
	Objekt #{{$object->id}} / <a href="http://ask.bibsys.no/ask/action/show?pid={{$object->bibsys_id}}&amp;kid=biblio">{{$object->bibsys_id}}</a>
</h2>

<p>
	FA {{$object->location}} : 

	<em>{{$object->title}} {{$object->subtitle}}</em><br />
av 
@foreach ($object->authors()->get() as $author)
{{$author->name}}
@endforeach

<br />

@foreach ($object->isbns()->get() as $isbn)
{{$isbn->number}}
@endforeach

</p>

<p>
Emner:
<?php $last = ''; ?>
@foreach ($subjects as $subj)
	@if ($last != $subj->system)
		<h3>{{$subj->system}}</h3>
	@endif
	<a href="{{URL::action('SubjectController@show', $subj->id)}}" class="tag">{{$subj->label_nb}}</a>
	<?php $last = $subj->system; ?>
@endforeach
</p>


@stop
