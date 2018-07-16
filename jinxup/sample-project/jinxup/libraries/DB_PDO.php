<?php

	class JXP_DB_PDO {

		private $_con        = null;
		private $_log        = [];
		private $_driver     = null;
		private $_hash       = null;
		private $_alias      = null;
		private $_fetchMode  = PDO::FETCH_ASSOC;
		private $_mute       = true;
		private $_connError  = [];

		public function __construct($alias, $driver, $user = null, $pass = null) {

			$this->_alias  = $alias;
			$this->_driver = $driver;

            $starTime = microtime(true);

			if (is_null($this->_con)) {

				try {

					if (strpos($driver, 'sqlite') !== false) {

						$this->_con = new PDO($driver);

                    } else {

						$this->_con = new PDO($driver, $user, $pass);
                    }

					$this->_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->_con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
					$this->_con->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

				} catch (PDOException $e) {

				    $this->_connError = $e;

                    $endTime = microtime(true);
                    $debug = debug_backtrace();

                    $this->_log['alias'] = $this->_alias;
                    $this->_log['hash']  = $this->getHash('connection', $e->getMessage());
                    $this->_log['time']  = $endTime - $starTime;

                    $this->_log['error'] = [
                        'file'    => $debug[2]['file'],
                        'line'    => $debug[2]['line'],
                        'message' => $e->getMessage()
                    ];

                    $this->_log['query'] = [];
				}
			}
		}

		public function mute($mute = false) {

			$this->_mute = $mute;
		}

		public function getDSN() {

			return $this->_driver;
		}

		public function setFetchMode($mode = 'assoc') {

			$mode = strtolower($mode);

			$this->_fetchMode = PDO::FETCH_ASSOC;

			if ($mode == 'object') {

				$this->_fetchMode = PDO::FETCH_OBJ;
            }

			return $this;
		}

		public function getConnection() {

			return isset($this->_con) ? $this->_con : null;
		}

		public function getHash($query, $bind) {

			return md5($query . json_encode($bind));
		}

		public function results() {

			return isset($this->_log['results']) ? $this->_log['results'] : [];
		}

		public function log($hash = null) {

		    if (is_null($hash)) {

		        $log = end($this->_log);

		    } else {

		        if (isset($this->_log[$hash])) {

		            $log = $this->_log[$hash];

		        } else {

		            $log = $this->_log;
                }
            }

    		return $log;
		}

		public function clearLog($hash = null) {

			$this->_log = [];
		}

		public function trimQuery($query) {

			return trim(preg_replace('/(\r\n|\s{2,})/m', ' ', $query));
		}

		public function previewQuery($query = null, $params = []) {

			$query  = $this->trimQuery($query);
			$keys   = [];
			$values = [];

			if (!empty($params)) {

				foreach ($params as $key => $value) {

					if (!is_array($value)) {

						$keys[]   = is_string($key) ? '$:' . $key . '\b$' : '$[?]$';
						$values[] = is_numeric($value) ? intval($value) : '"' . $value . '"';
					}
				}

				$query = preg_replace($keys, $values, $query);
			}

			return $query;
		}

		public function query($query, $bind = []) {

			$this->_hash = $this->getHash($query, $bind);

			$query  = $this->trimQuery($query);
			$return = $this->_runQuery($query, $bind, $this->_hash);

			return $return;
		}

		public function beginTransaction() {

			$this->_con->beginTransaction();
		}

		public function commit() {

			$this->_con->commit();
		}

		private function _runQuery($query, $bind, $hash) {

			$results  = null;
			$starTime = microtime(true);
			$endTime  = 0;

			$this->_log['alias']  = $this->_alias;
			$this->_log['hash']   = $this->_hash;
            $this->_log['time']   = 0;
			$this->_log['error']  = null;
			$this->_log['query']  = ['raw' => $query, 'preview' => $this->previewQuery($query, $bind)];

			if (!empty($this->_con))  {

				$this->_con->beginTransaction();

				try {

					$stmt = $this->_con->prepare($query);

					if (count($bind) > 0) {

						$this->_log['tokens']['total'] = count($bind);

						preg_match_all('/(?<=\:)\w*/im', $query, $params);

						$params = array_map('array_values', array_map('array_filter', $params));

						$this->_prepareParameters($stmt, $bind, $params, $hash);
					}

					$execute = $stmt->execute();

					if ($execute !== false) {

						if (preg_match('/^(select|describe|desc|call|drop|create|show)/im', $query)) {

							$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }

						if (preg_match('/^(delete|update)/im', $query)) {

							$results = $stmt->rowCount();
                        }

						if (preg_match('/^insert/im', $query)) {

							$results = $this->_con->lastInsertId();
                        }

						$this->_con->commit();

					} else {

						$this->_log['error']['message'] = 'There was an error executing your query';
					}

				} catch (PDOException $e) {

                    $endTime = microtime(true);
                    $debug   = debug_backtrace();

                    $this->_log['alias'] = $this->_alias;
                    $this->_log['hash']  = $this->_hash;
                    $this->_log['time']  = $endTime - $starTime;

                    $this->_log['error'] = [
                        'file'    => $debug[2]['file'],
                        'line'    => $debug[2]['line'],
                        'message' => $e->getMessage()
                    ];

                    $this->_log['query'] = ['raw' => $query, 'preview' => $this->previewQuery($query, $bind)];

					$this->_errorLog($this->_log);

					$this->_con->rollBack();
				}

			}  else {

                $endTime = microtime(true);
                $debug   = debug_backtrace();

                $this->_log['alias'] = $this->_alias;
                $this->_log['hash']  = $this->_hash;
                $this->_log['time']  = $endTime - $starTime;

                $this->_log['error'] = [
                    'file'    => $debug[2]['file'],
                    'line'    => $debug[2]['line'],
                    'message' => $this->_connError->getMessage()
                ];

                $this->_log['query'] = ['raw' => $query, 'preview' => $this->previewQuery($query, $bind)];

				$this->_errorLog($this->_log);
			}

			if (is_null($this->_log['error'])) {

				unset($this->_log['error']);
            }

			$this->_log['results'] = $results;

			return $results;
		}

		private function _errorLog($log) {

			echo '<pre>', print_r($log, true), '</pre>';
		}

		private function _prepareParameters($stmt, $bind, $params, $hash) {

			foreach ($params as $key) {

				foreach ($key as $value) {

					if (isset($bind[$value])) {

						$param = null;
						$type  = null;
						if (is_null($bind[$value]) || empty($bind[$value])) {

							$type  = 'NULL';
							$param = PDO::PARAM_NULL;
						}

						if (is_numeric($bind[$value])) {

							$type  = 'INTEGER';
							$param = PDO::PARAM_INT;
						}

                        if (is_string($bind[$value])) {

                            $type  = 'STRING';
                            $param = PDO::PARAM_STR;
                        }


                        if (is_bool($bind[$value])) {

							$type  = 'BOOLEAN';
							$param = PDO::PARAM_BOOL;
						}

						$arr = [
							'name'  => $value,
							'value' => $bind[$value],
							'type'  => $type
						];

						$this->_log['tokens']['bound'][] = $arr;

						$stmt->bindValue(":{$value}", $bind[$value], $param);

					} else {

						$this->_log['tokens']['unknown'][] = $value;
					}
				}
			}
		}
	}