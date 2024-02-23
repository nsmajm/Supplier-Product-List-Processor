<?php

class Product
{
    public $make;
    public $model;
    public $colour;
    public $capacity;
    public $network;
    public $grade;
    public $condition;

    public function __construct($make, $model, $colour, $capacity, $network, $grade, $condition)
    {
        $this->make = $make;
        $this->model = $model;
        $this->colour = $colour;
        $this->capacity = $capacity;
        $this->network = $network;
        $this->grade = $grade;
        $this->condition = $condition;
    }
}