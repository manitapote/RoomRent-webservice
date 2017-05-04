<?php

namespace Roomrent;

class Repository
{
	/**
	 * Object that binds to model
	 * @var Dynamic
	 */
	public $model;

	/**
	 * Creates new record
	 * @param  Array $data
	 * @return Object
	 */
	public function create($data)
	{
		return $this->model->create($data);
	}

	/**
	 * Updates the record
	 * @param  Object $model
	 * @param  Array $data
	 * @return Integer
	 */
	public function update($model, $data)
	{
		return $model->update($data);
	}

	/**
	 * Finds the record by given field
	 * @param  String $field
	 * @param  String $value
	 * @return Object
	 */
	public function findBy($field, $value)
	{
		return $this->model->where($field, $value);
	}

	/**
	 * Deletes the particuler record
	 * @param  Object $model 
	 * @return [type]        [description]
	 */
	public function delete($model)
	{

	}

	/**
	 * Updates or creates the record
	 * @param  Object $model 
	 * @param  Array $match  matching data
	 * @param  Array $update data to be updated
	 * @return Integer
	 */
	public function updateOrCreate($model, $match, $update)
	{
		return $model->updateOrCreate($match, $update);
	}

	/**
	 * Gets all record of the model
	 * @return Array array of Objects
	 */
	public function getAll()
	{
		return $this->model;
	}

	/**
	 * Gets the record within the range
	 * @param  String $field
	 * @param  Array $data
	 * @return Object
	 */
	public function getBetween($field, $data)
	{
		return $this->model->whereBetween($field, [$data[$field.'_min'], $data[$field.'_max']]);
	}

	/**
	 * Appends query for whereBetween query
	 * @param  Query  $query 
	 * @param  String $field 
	 * @param  Array  $data
	 * @return Query 
	 */
	public function appendWhereBetweenQuery($query, $field, $data)
	{
		return $query->whereBetween($field, [$data[$field."_min"], $data[$field.'_max']]);
	}

	/**
	 * Appends where query given query
	 * @param  Query  $query
	 * @param  String $field 
	 * @param  Array  $data
	 * @return Query
	 */
	public function appendQueryField($query, $field, $data)
	{
		return $query->where($field, $data);
	}

	/**
	 * Gets the record matching the values in array
	 * @param  String $field
	 * @param  Array  $array
	 * @return Query
	 */
	public function whereIn($field, $array)
	{
		return $this->model->whereIn($field, $array);
	}

}