<?php

interface IAbstractRepository {
  public function getAll();
  public function getById($id);
  public function create($data);
  public function updateById($id, $data);
  public function deleteById($id);
  public function existsById($id);
}