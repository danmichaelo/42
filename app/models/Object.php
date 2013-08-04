<?php
# models/Object.php

class Object extends Eloquent {

    public function isbns() {
        return $this->hasMany('Isbn');
    }
    
    public function authors() {
        return $this->belongsToMany('Author', 'object_authors');
    }

    public function subjects() {
        return $this->belongsToMany('Subject')
        	->withPivot('subdivision', 'time', 'place', 'form');
    }

}
