<?php
# models/Author.php

class Author extends Eloquent {

	public static $keys = array('name', 'authority');
    public static $pivotColumns = array();

    public function objects() {
        return $this->belongsToMany('Object');
    }

}
