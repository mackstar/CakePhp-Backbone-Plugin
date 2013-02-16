<?php

App::uses('Inflector', 'Utility');

/*
 *	A compoonent to make sure JSON gets returned in the way that backbone likes it
 */
Class BackboneComponent extends Component {
	
	private $_backbone = false;

	/*
	 * Sets the view to that of the json type
	 *
	 * @param $controller object The controller object
	 * @return void
	 */
	public function startup(Controller $controller) {
		if (!$controller->RequestHandler->isAjax() && !$this->_isJsonRequest($controller)) {
			return;
		}
		$controller->view = 'Backbone./Backbone/json';
	}

	protected function _isJsonRequest(Controller $controller) {
		$extensionSet = (isset($controller->request->params['ext']));
		if ($extensionSet) {
			return ($controller->request->params['ext'] == 'json');
		}
		return false;
	}

	/*
	 * Sets the parameters of the view escaping cake's default of
	 * assigning them as a keyed multi-dimensional array.
	 *
	 * @param $controller object The controller object
	 * @return void
	 */
	public function beforeRender(Controller $controller) {
		if (!$controller->RequestHandler->isAjax() && !$this->_isJsonRequest($controller)) {
			return;
		}
		$controllerName = $controller->request->params['controller'];
		$action = $controller->request->params['action'];
		$singular = Inflector::singularize($controllerName);
		$modelName = Inflector::camelize($singular);
		switch ($action) {
			case 'index': 
				$param = $controllerName;
				break;
			case 'add':
				$param = $singular;
				break;
			case 'edit':
				$param = $singular;
				break;
			case 'delete':
				return;
				break;
			case 'view':
				$object = $singular;
				break;
		}
		if (!isset($object) && isset($param)) {
			if (isset($controller->viewVars[$param][0][$modelName])) {
				$controller->set('object', array_map(function($row) use ($modelName) {
					return $row[$modelName];
				}, $controller->viewVars[$param]));
			}
			elseif (isset($controller->viewVars[$param][$modelName])) {
				$controller->set('object', $controller->viewVars[$param][$modelName]);
			} else {
				$controller->set('object', $controller->viewVars[$param]);
			}
		} elseif(isset($object)) {
			$controller->set('object', $object);
		}
	}
}
