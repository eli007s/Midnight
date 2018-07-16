<?php

    class Bio_Controller extends Jinxup {

        public function indexAction() { }

        public function showAction($id = 0) {

            $bio = $this->model->users->getUser($id);

            $this->view->assign('bio', $bio);
            $this->view->display('extends:_include/parent.tpl|bio.tpl');
        }

        public function createAction() {

            $this->model->bio->create($_POST['fname'], $_POST['lname'], $_POST['bio']);

            header('Location: /');
        }
    }