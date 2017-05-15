<?php

namespace Roomrent\Posts\Repositories;

/**
 * interface for PostRepository
 */
interface PostRepositoryInterface
{
	public function create($post);

	public function getById($id);

	public function getByField($field, $data);

	public function createImage($data);
	
	public function getByLocation($data);

	public function appendQueryField($query, $field, $data);

	public function getAll();
}