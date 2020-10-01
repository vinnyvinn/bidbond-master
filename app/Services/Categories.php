<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class Categories
{
	use ApiResponser;

	public static function index()
	{
		return self::__success(Category::get(['secret', 'name']));
	}

	public static function show($secret)
	{
		$query = Category::secret($secret);

		if ($query->exists()) {
			return self::__success($query->first());
		}

		return self::__error('Not found', 404);
	}

	public static function store($name)
	{
		$validator = self::_validator($name);

		if ($validator->fails()) {
			return self::__error($validator->errors()->all(), 422);
		}

		return self::__success(self::_store($name));
	}

	protected static function _validator($name)
	{
		return Validator::make(['name' => $name], [
			'name' => 'bail|required|max:255'
		]);
	}

	protected static function _store($name)
	{
		return Category::create([
			'name' => $name
		]);
	}

	public static function update($secret, $name)
	{
		$query = Category::secret($secret);

		$validator = self::_validator($name);

		if ($validator->fails()) {
			return self::__error($validator->errors()->all());
		}

		if ($query->exists()) {
			$data = $query->first();
			$data->update([
				'name' => $name
			]);

			return self::__success($data, 201);
		}

		return self::__error('Not Found', 404);
	}

	public static function delete($secret)
	{
		$query = Category::secret($secret);

		if ($query->exists()) {
			$query->first()->delete();

			return self::__success('Deleted', 201);
		}

		return self::__error('Not Found', 404);
	}
}
