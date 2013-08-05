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
        <img src="http://folk.uio.no/umnbib/42/show_image.php?id={{ $object->bibsys_id }}" class="small-cover" />
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
      <br />
      FA {{$object->location}}
    </td>
  </tr>
  @endforeach
</table>

{{$objects->links()}}

@stop
