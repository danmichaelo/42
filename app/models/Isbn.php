<?php
# models/Isbn.php

class Isbn extends Eloquent {

    public static $keys = array('number');
    public static $pivotColumns = array();

    public function objects() {
        return $this->belongsTo('Object');
    }

}
