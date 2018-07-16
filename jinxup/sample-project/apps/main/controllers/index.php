<?php

    class Index_Controller extends Jinxup {

        public function indexAction() {

            $bio = $this->model->users->allUsers();

            $this->view->assign('bio', $bio);
            $this->view->display('extends:_include/parent.tpl|index.tpl');
        }
    }