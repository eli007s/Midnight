<?php

    class JXP_Helper {

        public function __get($name) {

            $helper = null;

            if (class_exists($name . '_helper')) {

                $name  = $name . '_helper';
                $helper = new $name();
            }

            return $helper;
        }
    }