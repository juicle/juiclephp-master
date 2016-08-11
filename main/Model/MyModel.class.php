<?php

/**
 * Default Model of webapp.
 */
class MyModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    public function yourFunction()
    {
        echo "this is your funciton";

    }

}