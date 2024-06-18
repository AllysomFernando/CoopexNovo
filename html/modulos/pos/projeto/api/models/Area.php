<?php

class Area
{
  public $id_area;
  public $area;
  public $cor;

  public function isSelected($id)
  {
    return isset($this->id_area) && $id != '' && $this->id_area == $id;
  }
}
