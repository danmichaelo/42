@extends('master')
@section('header')
42 : Objekter
@stop
@section('container')
<h2>Objekter</h2>
<table cellspacing="0" cellpadding="3" class="objects">
  @foreach ($objects as $object)
  <tr>
    <td>
      <?php 
        $isbn = $object->isbns->first(); 
        $cover = $isbn ? '<img src="http://innhold.bibsys.no/bilde/forside/?size=stor&amp;id=' . $isbn->number . '.jpg" />' : '';
        echo $cover;
      ?>
    </td>
    <td>
      <a href="{{URL::action('ObjectController@show', $object->id)}}">
        {{$object->title}}         {{$object->subtitle}}
      </a><br />
      @foreach ($object->authors as $author)
        {{$author->name}}<br />
      @endforeach
      @foreach ($object->isbns as $isbn)
        <?php
          $number = $isbn->number;
          switch ($isbn->form) {
            case '(ib.)':
              echo "Innbundet (ISBN: $number)<br />\n";
              break;
            case '(h.)':
              echo "Heftet (ISBN: $number)<br />\n";
              break;
            default:
              echo "ISBN: $number<br />\n";
          }
        ?>
      @endforeach
    </td>
  </tr>
  @endforeach
</table>

{{$objects->links()}}

@stop
