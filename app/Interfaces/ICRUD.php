<?php
    namespace App\Interfaces;
    
    interface ICRUD{
        public function index();
        public function list();
        public function show(number $id);
        public function create();
        public function update();
        public function delete(number $id);
    }
?>