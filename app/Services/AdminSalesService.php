<?php

namespace App\Services;

use App\Repositories\AdminSalesRepository;

class AdminSalesService
{
    protected AdminSalesRepository $repo;

    public function __construct(AdminSalesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getProducts()
    {
        return $this->repo->getProducts();
    }

    public function createSale(array $data)
    {
        return $this->repo->createSale($data);
    }

    public function getAllSales()
    {
        return $this->repo->getAllSales();
    }
    public function getSale($id)
    {
        return $this->repo->getSale($id);
    }
    public function updateSale($id, array $data)
    {
        return $this->repo->updateSale($id, $data);
    }
    public function deleteSale($id)
    {
        return $this->repo->deleteSale($id);
    }
} 