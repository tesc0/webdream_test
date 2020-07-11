<?php
namespace App\Tests;

use App\Controller\Site;
use PHPUnit\Framework\TestCase;

class InventoryTest extends TestCase
{
    public function testAddProducts()
    {
        $site = new Site();
        $result = $site->newProducts(3);

        $result = json_decode($result, true);

        $this->assertArrayHasKey('success', $result) && $this->assertArrayHasKey('message', $result);

    }

    public function testInventoryList()
    {
        $site = new Site();
        $result = $site->inventory(1);

        $result_r = json_decode($result, true);

        $this->assertIsArray($result_r);
    }

    public function testStorageCreation()
    {
        $site = new Site();
        $result = $site->createStorage();
        $result_r = json_decode($result, true);

        $this->assertIsArray($result_r);
    }
}