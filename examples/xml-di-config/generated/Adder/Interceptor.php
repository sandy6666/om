<?php

namespace Adder;

/**
 * This class is a middleware between the \Adder implementation and caller
 */
class Interceptor extends \Adder {

	use \Om\DependencyInjection\InterceptorTrait;

	/**
	 * @inheritdoc
	 */
	public function add($a, $b) {
		$arguments = $this->___execBefore(func_get_args(), "add");
		$result = $this->___execAround($arguments, "add");
		return $this->___execAfter($result, "add");
	}

	/**
	 * @inheritdoc
	 */
	public function substract($a, $b) {
		$arguments = $this->___execBefore(func_get_args(), "substract");
		$result = $this->___execAround($arguments, "substract");
		return $this->___execAfter($result, "substract");
	}

	/**
	 * @inheritdoc
	 */
	public function multiply($a, $b) {
		$arguments = $this->___execBefore(func_get_args(), "multiply");
		$result = $this->___execAround($arguments, "multiply");
		return $this->___execAfter($result, "multiply");
	}

	/**
	 * @inheritdoc
	 */
	public function divide($a, $b) {
		$arguments = $this->___execBefore(func_get_args(), "divide");
		$result = $this->___execAround($arguments, "divide");
		return $this->___execAfter($result, "divide");
	}

}