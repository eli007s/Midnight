<?php

	class JXP_App {

		private static $_app    = null;
		private static $_apps   = [];
        private static $_routes = [];

		public static function set($app) {

			self::$_app = $app;
		}

		public static function setRoutes($c, $m, $a) {

		    self::$_routes = ['controller' => $c, 'action' => $m, 'params' => $a];
        }

        public static function getRoutes() {

            return self::$_routes;
        }

		public static function loaded() {

			return self::$_app;
		}

		public static function discover() {

			$apps = JXP_Directory::scan(APPS_DIR);

			foreach ($apps as $k => $v)  {

				if (is_dir($v['path'] . DS . 'controllers') || file_exists($v['path'] . DS . 'index.php')) {

                    unset($apps[$k]['ext']);
                    unset($apps[$k]['path']);
                    unset($apps[$k]['size']);

                    $apps[$k] = $v['name'];

				} else {

				    unset($apps[$k]);
                }
			}

			self::$_apps = $apps;

			return $apps;
		}

		public static function apps() {

			return self::$_apps;
		}

		public static function exists($app) {

			return in_array($app, self::$_apps);
		}
	}