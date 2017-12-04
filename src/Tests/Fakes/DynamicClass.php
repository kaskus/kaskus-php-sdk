<?php

/*
Because stdClass can't add dynamic method
 */

class DynamicClass extends stdClass
{
	public function __call($key, $params)
	{
		if (!isset($this->{$key})) {
			return; // silent of the fake
			// throw new Exception("Call to undefined method ".get_class($this)."::".$key."()");
		}
		$subject = $this->{$key};

		return call_user_func_array($subject, $params);
	}
}
