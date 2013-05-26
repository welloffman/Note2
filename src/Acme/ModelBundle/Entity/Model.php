<?php
namespace Acme\ModelBundle\Entity;

/**
 * Родитель для моделей
 */
class Model {
	public function toArray() {
		return get_object_vars($this);
	}
}