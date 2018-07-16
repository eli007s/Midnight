<?php

    class Users_Model extends Jinxup {

        public function getUser($id = 0) {

            $user = $this->db->sample('select * from users u left join bio b on b.user_id = u.user_id where b.user_id = :id', ['id' => $id]);

            return count($user) > 0 ? $user[0] : false;
        }

        public function allUsers() {

            $user = $this->db->sample('select * from users u left join bio b on b.user_id = u.user_id');

            return count($user) > 0 ? $user : false;
        }
    }