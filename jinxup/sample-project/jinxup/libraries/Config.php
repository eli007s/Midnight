<?php

	class JXP_Config {

		private static $_config    = [];
		private static $_namespace = '\\';
		private static $_queue     = [];

		public static function get($key = null) {

		    return $key == null ? self::$_config : self::$_config[$key];
        }

        public static function load($config = null) {

		    if (!is_null($config)) {

		        self::$_queue = [$config];
            }

            foreach (self::$_queue as $k => $v) {

                if (!is_array($v) && !is_file($v) && is_dir($v)) {

                    $scan = JXP_Directory::scan($v);

                    foreach ($scan as $_k => $_v) {

                        self::load($_v['path']);
                    }
                }

                $contents = [];

                if (is_array($v)) {

                    self::$_config = array_unique(array_merge_recursive(self::$_config, $v), SORT_REGULAR);
                    //self::$_config = array_map("unserialize", array_unique(array_map("serialize", self::$_config)));
                }

                if (strpos($v, '{') !== false) {

                    $v = json_decode($v, true);

                    if (is_array($v)) {

                        self::$_config = array_unique(array_merge_recursive(self::$_config, $v), SORT_REGULAR);
                        //self::$_config = array_map("unserialize", array_unique(array_map("serialize", self::$_config)));
                    }
                }

                if (is_file($v)) {

                    if (strpos($v, '.json') !== false || strpos($v, '.tell') !== false) {

                        $contents = json_decode(self::_cleanCommentsFromJson($v), true);
                    }

                    if (strpos($v, '.php') !== false) {

                        require_once $v;
                    }

                    if (is_array($contents)) {

                        self::$_config = array_merge_recursive(self::$_config, $contents);
                        //self::$_config = array_map("unserialize", array_unique(array_map("serialize", self::$_config)));
                    }
                }
            }

			return self::_translate(self::$_config);
		}

		public static function setNamespace($ns) {

			self::$_namespace = '\\' . ltrim($ns, '\\');
		}

		public static function getNamespace() {

			return self::$_namespace == '\\' ? self::$_namespace : self::$_namespace . '\\';
		}

		public static function set($key, $val = null) {

		    if (is_string($key) && !is_null($val)) {

		        self::$_config[$key] = $val;

		    } else {

		        $config = $key;

                self::$_config = array_unique(array_merge_recursive(self::$_config, $config), SORT_REGULAR);
                //self::$_config = array_map("unserialize", array_unique(array_map("serialize", self::$_config)));
            }
        }

		public static function getSettings() {

			return isset(self::$_config['settings']) ? self::$_config['settings'] : [];
		}

		private static function _cleanCommentsFromJson($file) {

			return preg_replace('@(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|((?<!:)//.*)|[\t\r\n]@i', '', file_get_contents($file));
		}

		private static function _translate($config) {

			if (isset(self::$_config['apps'])) {

				foreach (self::$_config['apps'] as $k => $v) {

					if (isset($v['import'])) {

						if (isset(self::$_config['settings']['setting'][$v['import']])) {

							self::$_config['apps'][$k];
						}
					}
				}
			}

			return $config;
		}

		private static function _array_change_key_case_recursive($arr, $case = CASE_LOWER) {

			return array_map(function($item) use($case) {

				if (is_array($item)) {

					$item = self::_array_change_key_case_recursive($item, $case);
                }

				return $item;

			}, array_change_key_case($arr, $case));
		}
	}