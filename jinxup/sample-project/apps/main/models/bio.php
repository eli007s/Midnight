<?php

    class Bio_Model extends Jinxup {

        public function getBio($id = 0) {

            $bio = $this->db->sample('select * from bio where bio_id = :id', ['id' => $id]);

            return count($bio) > 0 ? $bio[0] : false;
        }

        public function allBios() {

            $bio = $this->db->sample('select * from bio');

            return count($bio) > 0 ? $bio[0] : false;
        }

        public function create($fname, $lname, $bio) {

            $id = $this->db->smaple('insert into users (fname, lname) values (:fname, :lname)', [
                'fname' => $fname,
                'lname' => $lname
            ]);

            $this->db->sample('insert into bio (user_id, bio) values (:id, :bio)', [
                'id' => $id,
                'bio' => $bio
            ]);
        }
    }