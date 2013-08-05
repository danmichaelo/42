@extends('master')
@section('header')
42 : Emner
@stop
@section('container')

<p>
  <a href="{{URL::action('SubjectController@index')}}"><i class="halflings-icon list"></i> Alle emner</a>

</p>

<h2>
  Emne #{{$subject->id}} / {{$subject->label('nb')}}
</h2>

<p>
  {{$subject->label('nb')}} <span class="langname">(Norsk bokmål)</span><br />
  {{$subject->label('en')}} <span class="langname">(Engelsk)</span><br />
</p>


<h3>Objekter ({{ count($objects) }})</h3>
<table cellspacing="0" class="simple">
  <thead>
    <tr>
      <th>
        Tittel
      </th>
      <th>
        REALORD
      </th>
      <th>
        Andre emneordssystemer
      </th>
    </tr>
  </thead>
  <tbody>
@foreach ($objects as $obj)
    <tr>
      <td>
        <a href="{{URL::action('ObjectController@show', $obj->id)}}">{{trim($obj->title, ': ')}} </a>
      </td>
      <td>
@foreach ($obj->subjects()->where('system', '=', 'noubomn')->get() as $subj)
        <a href="/emner/{{$subj->label('nb')}}" class="tag">{{$subj->label('nb')}}</a>
@endforeach
      </td>
      <td>
@foreach ($obj->subjects()->where('system', '!=', 'noubomn')->get() as $subj)
        <a href="/emner/{{$subj->label('nb')}}" class="tag">{{$subj->label('nb')}}</a>
@endforeach
      </td>
    </tr>
@endforeach
  </tbody>
</table>

@stop
