<?php

namespace Models\ViewModels\ApiController;
class IndexViewModel
{
    private $_routes;
    function __construct(array $_routes)
    {
        $this->_routes = $_routes;
    }
    public function getRoutes()
    {
        return $this->_routes;
    }
}