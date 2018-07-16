<?php

	class JXP_DB extends Jinxup
	{
		/**
		 * @var array
		 * @access private
		 */
		private static $_database = [];

		/**
		 * @var null
		 * @access private
		 */
		private static $_alias = null;

		/**
		 * @var null
		 * @access public
		 */
		public $alias = 1;

		/**
		 * @var null
		 * @access private
		 */
		private $_init = null;

        /**
         * @var string
         * @access private
         */
		private static $_env = 'live';

        /**
         * @var string
         * @access private
         */
        private static $_mode = 'read';

		/**
		 * Return the alias used for this instance
		 */
		public function __construct() {

			$this->alias = self::$_alias;

			self::$_env = $this->config->get('env');
		}

		/**
		 * @return object
		 */
		public function init() {

			return $this;
		}

		/**
		 * @param string $alias
		 * @param string $host
		 * @param string $name
		 * @param string $user
		 * @param string $pass
         * @param string $env
         * @param string $driver
		 * @param int $port
		 * @return object
		 */
		public static function fuel($alias, $host = 'localhost', $name = null, $user = 'root', $pass = null, $env = null, $port = 3306, $driver = 'PDO') {

		    if (is_null($env)) {

		        $env = self::$_env;
            }

			$fuel = ['host' => $host, 'name' => $name, 'user' => $user, 'pass' => $pass, 'port' => $port, 'driver' => $driver, 'env' => $env];

			return self::ignite($alias, $fuel);
		}

		/**
		 * @param string $storage
		 * @param string $alias
		 * @return object
		 */
		public static function sqlite($storage = null, $alias = null) {

			$fuel = ['storage' => $storage, 'driver' => 'sqlite'];

			return self::ignite($alias, $fuel);
		}

		private static function _using($alias) {

			return self::ignite($alias);
		}

		/**
		 * @param string $alias
		 * @param array $fuel
         * @return object
		 */
		public static function ignite($alias = null, $fuel = []) {

			$config  = !empty($fuel) ? [$alias => $fuel] : [];
			$host    = [];
			$name    = '';
			$user    = '';
			$pass    = '';
			$env     = '';
			$file    = '';
			$port    = 3306;
			$store   = '';
			$driver  = 'PDO';
			$_alias  = null;
			$_driver1 = null;
			$_driver2 = null;

			if (!isset($config[$alias])) {

			    $c = JXP_Config::get('database');
                //echo '<pre>', print_r(JXP_Config::get('env'), true), '</pre>';
                //$c = $c[JXP_Config::get('env')];
                //echo '<pre>', print_r($c, true), '</pre>';
			    $alias = $c['alias'];

			    $host = [
			        'read' => isset($c['host']['read']) ? $c['host']['read'] : $c['host'],
                    'write' => isset($c['host']['write']) ? $c['host']['write'] : $c['host']
                ];

			    $config[$c['alias']] = [
			        'alias' => $c['alias'],
                    'host'  => $host,
                    'name'  => $c['name'],
                    'user'  => $c['user'],
                    'pass'  => $c['pass'],
                    'env'   => self::$_env,
                    'port'  => isset($c['port']) ? $c['port'] : 3306,
                    'driver' => isset($c['driver']) ? $c['driver'] : 'PDO'
                ];

			} else {

                $config[$alias]['host'] = [
                    'read' => isset($config[$alias]['host']['read']) ? $config[$alias]['host']['read'] : $config[$alias]['host'],
                    'write' => isset($config[$alias]['host']['write']) ? $config[$alias]['host']['write'] : $config[$alias]['host']
                ];
            }

            extract($config[$alias]);

			if (!is_null($driver)) {

				switch (strtolower($driver)) {

					case 'sqlite':

						if (extension_loaded('sqlite3') || extension_loaded('pdo_sqlite')) {

							$file    .= !empty($store) || !is_null($store) ? getcwd() . DS . trim($store, '/') : ':memory:';
							$_driver1  = "sqlite:{$file}";
                            $_driver2  = "sqlite:{$file}";

						} else {

							echo 'SQLite is not loaded';
						}

						break;

					case 'pdo':
					default:

						$_driver1 = "mysql:host=" . $host['read'] . ";port=" . $port . ";dbname=" . $name;
                        $_driver2 = "mysql:host=" . $host['write'] . ";port=" . $port . ";dbname=" . $name;

						break;
				}
			}

            self::$_alias = $alias;

            $dbObj['read'] = new JXP_DB_PDO($alias, $_driver1, $user, $pass);

			if ($config[$alias]['host']['read'] =! $config[$alias]['host']['write']) {

                $dbObj['write'] = new JXP_DB_PDO($alias, $_driver2, $user, $pass);

			} else {

                $dbObj['write'] = $dbObj['read'];
            }

			self::$_database[$alias] = $dbObj;

			return self::$_database[$alias];
		}

		/**
		 * @param string $alias
		 * @param array $params
		 * @return object
		 */
		public static function __callStatic($alias, $params) {

            $return = null;

            if (empty(self::$_database)) {

                self::ignite();
            }

            switch ($alias) {

                case 'log':

                    $alias = 'log';

                    break;

                default:

                    self::$_alias = $alias;

                    $alias = 'query';

                    self::_mode($params[0]);

                    break;
            }

            $dbObj = self::$_database[self::$_alias][self::$_mode];

            if (method_exists($dbObj, $alias))  {

                if (count($params) == 4) {

                    $return = $dbObj->{$alias}($params[0], $params[1], $params[2], $params[3]);

                } else if (count($params) == 3) {

                    $return = $dbObj->{$alias}($params[0], $params[1], $params[2]);

                } else if (count($params) == 2) {

                    $return = $dbObj->{$alias}($params[0], $params[1]);

                } else if (count($params) == 1) {

                    $return = $dbObj->{$alias}($params[0]);

                } else {

                    $return = $dbObj->{$alias}();
                }
            }

			return $return;
		}

		/**
		 * @param string $alias
		 * @param array $params
		 * @return object
		 */
		public function __call($alias, $params) {

			$return = null;

            if (empty(self::$_database)) {

                self::ignite();
            }

            switch ($alias) {

                case 'log':

                    $alias = 'log';

                    break;

                default:

                    $alias = 'query';

                    self::_mode($params[0]);

                    break;
            }

            $dbObj = self::$_database[self::$_alias][self::$_mode];

            if (method_exists($dbObj, $alias))  {

                if (count($params) == 4) {

                    $return = $dbObj->{$alias}($params[0], $params[1], $params[2], $params[3]);

                } else if (count($params) == 3) {

                    $return = $dbObj->{$alias}($params[0], $params[1], $params[2]);

                } else if (count($params) == 2) {

                    $return = $dbObj->{$alias}($params[0], $params[1]);

                } else if (count($params) == 1) {

                    $return = $dbObj->{$alias}($params[0]);

                } else {

                    $return = $dbObj->{$alias}();
                }
            }

			return $return;
		}

		/**
		 * @desc Destroy connection
		 * @param string $alias
		 */
		public static function destroy($alias = null) {

			if (is_null($alias)) {

				self::$_database = null;

			} else {

				unset(self::$_database[$alias]);
            }
		}

		private static function _mode($query) {

            if (preg_match('/^(select|describe|desc|call|show)/im', $query)) {

                self::$_mode = 'read';
            }

            if (preg_match('/^(insert|delete|update|drop|create)/im', $query)) {

                self::$_mode = 'write';
            }
        }
	}