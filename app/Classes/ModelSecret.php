<?php

namespace App\Classes;

use Illuminate\Support\Facades\Schema;

trait ModelSecret
{
	public static function bootModelSecret()
	{
		static::creating(function ($model) {
			$model->saveSecret($model);
		});
	}

	protected function saveSecret($model)
	{
		if (in_array('secret', Schema::getColumnListing($model->getTable()))) {
		    $model->secret = $this->generate($model);
		}
	}

	protected function generate($model)
	{
		$secret = uniqid();

		if ($model->all()->pluck('secret')->contains($secret)) {
			return $this->generate($model);
		}

		return $secret;
	}
}
