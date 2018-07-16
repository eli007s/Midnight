<?php

    class JXP_Model {

        public function __get($name) {

            $model = null;
            $name  = $name . '_model';

            if (class_exists($name)) {

                $model = new $name();
            }

            return $model;
        }
    }