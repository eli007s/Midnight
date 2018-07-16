<?php

	class JXP_View {

		private static $_engines = ['smarty'];
		private static $_engine  = 'smarty';

		public function __call($method, $params) {

		    return call_user_func_array([self::$_engine, $method], $params);
        }

		public static function engine($engine) {

		    if ($engine == 'smarty') {

		        $file = VENDOR_DIR . DS . 'smarty/smarty' . DS . 'libs' . DS . 'Smarty.class.php';

                $app = JXP_App::loaded();

		        if (file_exists($file)) {

		            require_once $file;

                    self::$_engine = new Smarty();

                    self::$_engine->left_delimiter = '{!';
                    self::$_engine->right_delimiter = '!}';
                    self::$_engine->setTemplateDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'templates');
                    self::$_engine->setCompileDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'compiled');
                    self::$_engine->setCacheDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'cache');
                    self::$_engine->setConfigDir(APPS_DIR . DS . $app . DS . 'views' . DS . 'config');
                }
            }

		    if (!class_exists($engine)) {

                throw new exception ('Template engine ' . $engine . ' doesn\'t exist');
            }

		    return self::$_engine;
        }
	}