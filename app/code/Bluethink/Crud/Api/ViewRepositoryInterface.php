<?php

namespace Bluethink\Crud\Api;

interface ViewRepositoryInterface
{
    /**
     * @param Data\ViewInterface $view
     * @return mixed
     */
    public function save(Data\ViewInterface $view);

    /**
     * @param $viewId
     * @return mixed
     */
    public function getById($viewId);

    /**
     * @param Data\ViewInterface $view
     * @return mixed
     */
    public function delete(Data\ViewInterface $view);

    /**
     * @param $viewId
     * @return mixed
     */
    public function deleteById($viewId);
}
