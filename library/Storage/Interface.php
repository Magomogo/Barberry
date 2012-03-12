<?php

interface Storage_Interface {

    /**
     * @param string $id
     * @return string
     * @throws Storage_NotFoundException
     */
    public function getById($id);

    /**
     * @param string $id
     * @throws Storage_NotFoundException
     */
    public function delete($id);

    /**
     * @param string $content
     * @return string content id
     * @throws Storage_WriteException
     */
    public function save($content);
}
