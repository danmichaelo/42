@extends('master')
@section('header')
42 : Ontosaur
@stop
@section('container')

<p>
    Kilde: <a href="{{ $src_url }}">{{{$src_url}}}</a>
</p>
@foreach ($warnings as $warn)
<div class="alert alert-error">
    {{$warn}}
</div>
@endforeach
<p>
    Språk: 
    @foreach ($supported_languages as $code => $label)
    <a href="{{ action('OntosaurController@index', array('lang' => $code)) }}">{{ $label }}</a>
    @endforeach
</p>

<div>
<div class="object-list" style="float:right;width:500px;background:white;border-radius:4px;padding:8px;">

</div>
</div>

<div class="subject-tree">
<?php

function make_ul($tree, $labels, $lang, $label_map) {
    $o = "";
    $o .= "<ul>\n";
    foreach ($tree as $key => $children) {
    	$prefLabel = isset($labels[$key][$lang])
            ? $labels[$key][$lang]['prefLabel']
            : '&lt;' . $key . '&gt;';

        // Get object count for prefLabel
        if (isset($labels[$key]) && isset($labels[$key]['nb'])) {
            $nb = $labels[$key]['nb']['prefLabel'];
            $nb = mb_strtoupper(mb_substr($nb, 0, 1)) . mb_substr($nb, 1); // Normalize            
        } else {
            $nb = "xxxx";
        }
        $id = isset($label_map[$nb])
            ? $label_map[$nb]['id']
            : 0;

        $object_count = 0;
        if (isset($label_map[$nb])) {
            $object_count += $label_map[$nb]['object_count'];
        }

        // Get object count for altLabel
        $altLabels = array();
        if (isset($labels[$key]) && isset($labels[$key]['nb']) && isset($labels[$key]['nb']['altLabel'])) {
            foreach ($labels[$key]['nb']['altLabel'] as $altLabel) {
                $altLabel = mb_strtoupper(mb_substr($altLabel, 0, 1)) . mb_substr($altLabel, 1); // Normalize

                $altId = isset($label_map[$altLabel])
                    ? $label_map[$altLabel]['id']
                    : 0;


                $altCount = '';
                if (isset($label_map[$altLabel])) {
                    $object_count += $label_map[$altLabel]['object_count'];
                    $altCount = ' (' . $label_map[$altLabel]['object_count'] . ')';
                }
    
                $altLabels[] = $altLabel . $altCount;
            }
        }


        $uri = ($id != 0) 
            ? '/emner/' . $id 
            : '#';

        $o .= '<li>';
        $o .= '<a href="' . $uri . '" data-id="' . $id . '">';
        if (is_array($children) && count($children) > 0) {
            $o .= '<i class="halflings-icon chevron-down"></i>';
        }
        $o .= ($id == 0) ? "<em class='notfound'>$prefLabel</em>" : $prefLabel;
        $o .= '</a>';
        
        if ($object_count != 0) {
            $o .= ' (' . $object_count . ')';
        }

        if (count($altLabels) != 0) {
            $o .= '<small> inkluderer ' . implode(', ', $altLabels) . '</small>';
        }

        if (is_array($children) && count($children) > 0) {
            $o .= make_ul($children, $labels, $lang, $label_map);
        }
        $o .= "</li>\n";
    }
    $o .= "</ul>\n";
    return $o;
}
echo make_ul($tree, $labels, $lang, $label_map);

?>
</div>

{{--
@foreach ($labels as $key => $val)
	{{ $val['nb'] }}
@endforeach
--}}

<script type="text/javascript">

$(document).ready(function() {
	$('.subject-tree a').on('click', function(e) {
		e.preventDefault();
        var a = $(e.currentTarget),
		    ul = a.next('ul'),
            i = a.children('i'),
            id = a.attr('data-id');

        //console.log('Clicked #' + id);

        if ($(e.target).is(i)) {
            ul.toggleClass('collapsed');
            if (ul.hasClass('collapsed')) {
                i.removeClass('halflings-icon chevron-down').addClass('halflings-icon chevron-right');
            } else {
                i.removeClass('halflings-icon chevron-right').addClass('halflings-icon chevron-down');
            }

        } else {

            /*if (id == 0) {
                $('.object-list').html('Termen ble ikke funnet i BIBSYS'); 
            } else {
                */
                //$.getJSON('/emner/' + id + '?format=json', function(response) {
                $.getJSON('/emner/noubomn/' + a.text() + '?format=json', function(response) {
                    console.log(response);
                    var subject = response.subjects[0],
                        objects = response.objects,
                        html = '<h3>Emne #' + subject.id + '</h3>';
                    @foreach ($supported_languages as $code => $label)                
                        html += '<div>{{$label}}: ' + (subject.label_{{$code}} 
                            ? subject.label_{{$code}} 
                            : '<em>Ikke definert</em>') + '</div>';
                    @endforeach

                    html += '<ul>';
                    for (var i=0; i < objects.length; i++) {
                        var o = objects[i];
                        console.log(o);
                        html += '<li><i class="halflings-icon book"></i>';
                        html += '<a href="/objekter/' + o.bibsys_id + '.bibsys">' + o.title + ' ' 
                            + (o.subtitle ? o.subtitle : '') 
                            + '</a>';
                        html += '</li>';
                    }                    
                    html += '</ul>';

                    $('.object-list').html(html);
                }) 
                .error(function(e) {
                   $('.object-list').html('Huff, det oppsto en kommunikasjonsfeil..'); 
                });
            //}
        }
	});

    $( '.object-list' ).scrollFollow(); 

});

</script>

{{--
<pre>
    <?php

    var_dump(mb_internal_encoding());

    var_dump($label_map);
    ?>
</pre>
--}}

@stop
