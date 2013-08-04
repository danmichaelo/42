@extends('master')
@section('header')
42 : Emner
@stop
@section('container')
Systemer:
<ul class="flat">
  <li> 
      <a href="{{URL::action('SubjectController@index')}}" class="tag<?php echo (is_null($system)) ? ' selected' : ''; ?>">alle</a>
  </li>
  @foreach ($systems as $sys)
  <li>
    <a href="{{URL::action('SubjectController@index') . '?system=' . $sys->system . '&amp;threshold=' . Input::get('threshold', '1') }}" class="tag<?php echo ($system == $sys->system) ? ' selected' : ''; ?>">{{$sys->system}}</a>
  </li>
  @endforeach
</ul>
Emneord:
<ul class="flat">
  @foreach ($subjects as $subject)
    <li>
      <a href="{{URL::action('SubjectController@show', $subject->id)}}" class="tag">{{$subject->label_nb}} ({{$subject->object_count}})</a>
    </li>
  @endforeach
</ul>
@stop
