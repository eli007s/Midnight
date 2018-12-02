<?php

    /**
     * Class JXP_View
     * * @method static $this display
     */
	class JXP_View extends Jinxup {

		private static $_engines = ['smarty'];
		private static $_engine  = 'smarty';

		public function __call($method, $params) {

		    $this->_setup();

		    return call_user_func_array([self::$_engine, $method], $params);
        }

		public static function engine($engine) {

		    self::$_engine = $engine;

		    if ($engine == 'smarty') {

		        $file = VENDOR_DIR . DS . 'smarty/smarty' . DS . 'libs' . DS . 'Smarty.class.php';

		        if (file_exists($file)) {

		            require_once $file;

                    self::$_engine = new Smarty();
                }
            }

		    if (!class_exists($engine)) {

                throw new exception ('Template engine ' . $engine . ' doesn\'t exist');
            }

		    return self::$_engine;
        }

        private function _setup() {

            $app = JXP_App::loaded();

            if (get_class(self::$_engine) == 'Smarty') {

                self::$_engine->left_delimiter  = '{!';
                self::$_engine->right_delimiter = '!}';
                self::$_engine->debugging       = false;
                self::$_engine->caching         = false;
                self::$_engine->cache_lifetime  = 1;
                self::$_engine->setTemplateDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'templates');
                self::$_engine->setCompileDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'compiled');
                self::$_engine->setCacheDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'cache');
                self::$_engine->setConfigDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'config');
            }
        }
	}