<?php

namespace AdderPlugin;

/**
 * This class is a middleware between the \AdderPlugin implementation and caller
 */
class Interceptor extends \AdderPlugin {

	use \Om\DependencyInjection\InterceptorTrait;

	/**
	 * @inheritdoc
	 */
	public function afterAdd($subject, $result) {
		$arguments = $this->___execBefore(func_get_args(), "afterAdd");
		$result = $this->___execAround($arguments, "afterAdd");
		return $this->___execAfter($result, "afterAdd");
	}

}