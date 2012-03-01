<?php

interface Storage_Interface {
    public function getById($id);
    public function delete($id);
    public function save($content);
}
