<?php
namespace App\Controller;

use App\Entity\Brands;
use App\Entity\Inventory;
use App\Entity\Productgroups;
use App\Entity\Products;
use App\Entity\Storages;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Common\Collections\Collection;


class Site extends AbstractController
{

    private $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    public function index()
    {
        
    }

    /**
     * Véletlen számú raktárak létrehozása
     *
     * @return JsonResponse
     */
    public function createStorage()
    {

        /*
        $post_name = filter_var(trim($this->request->get('name')), FILTER_SANITIZE_STRING);
        $post_address = filter_var(trim($this->request->get('address')), FILTER_SANITIZE_STRING);
        $post_capacity = filter_var(trim($this->request->get('capacity')), FILTER_SANITIZE_STRING);

        print_R($post_name);
        */

        $random = rand(2, 8);
        for($i = 0; $i < $random; $i++) {

            $post_name = "raktár " . rand(1, 30);
            $post_address = "Budapest " . rand(1, 23) . ".";
            $post_capacity = rand(3, 400);

            $new_storage = new Storages();
            $new_storage->setName($post_name);
            $new_storage->setAddress($post_address);
            $new_storage->setCapacity($post_capacity);
            $this->getDoctrine()->getManager()->persist($new_storage);
            $this->getDoctrine()->getManager()->flush();
        }

        //$post = json_decode(file_get_contents('php://input'), true);

        return new JsonResponse(["success" => 1, "message" => "raktár létrehozva", "new_storages" => $random]);
    }


    /**
     * Leltárak kilistázása
     * minden raktár vagy adott raktár tartalma
     *
     * @param int $storage_id
     * @return JsonResponse
     */
    public function inventory($storage_id = 0)
    {

        $inventory_model = new Inventory();
        $inventory = $inventory_model->getInventoryListByStorage($storage_id);

        $array = [];
        if(!empty($inventory)) {
            foreach ($inventory as $key => $inv) {

                if (!empty($inv["storageName"])) {
                    $array[$inv["storageName"]][] = [
                        "product" => $inv["productName"],
                        "sku" => $inv["sku"]
                    ];
                } else {
                    $array[$key]["product"] = $inv["productName"];
                    $array[$key]["sku"] = $inv["sku"];
                }
            }
        }

        //print_R($array);
        //die();

        return new JsonResponse($array);

    }

    /**
     * Raktárak tartalmának mozgatása vagy ürítése
     * attól függően, hogy van-e célraktár
     *
     * @param $from_id
     * @param string $to_id
     * @return JsonResponse
     */
    public function moveInventory($from_id, $to_id = "")
    {

        $message = "";
        $inventory_model = new Inventory();

        if(!empty($to_id)) {

            $inventory_to = $this->getDoctrine()
                ->getRepository(Storages::class)
                ->find($to_id);

            $capacity_main = $inventory_to->getCapacity();

            $inventory_from = $inventory_model->inventoryItemsFrom($from_id);

            if (count($inventory_from) > $capacity_main) {

                // következő raktár megkeresése, ha nincs elég hely az elsőben
                $found = false;
                $i = 0;
                do {

                    $i++;

                    $inventory_to = $this->getDoctrine()
                        ->getRepository(Storages::class)
                        ->find($to_id + $i);

                    if(!empty($inventory_to)) {
                        $found = true;
                    }

                } while (!$found);

                // ha a második raktárba befér, akkor oda is rakjon
                if($inventory_to->getCapacity() > count($inventory_from) - $capacity_main ) {

                    $result_inventory_qb = $inventory_model->selectInventory($from_id);

                    $index = 0;
                    if(!empty($result_inventory_qb)) {

                        foreach($result_inventory_qb as $item) {


                            if($index <= $capacity_main) {
                                $to__ = $to_id;
                            } else {
                                $to__ = $to_id + $i;
                            }


                            $inventory_model->moveItemById($from_id, $to__, $item->getId());

                            $index++;
                        }
                    }

                } else {
                    // ha nem fér be kettőbe, akkor üzenet

                    $message = "két raktár sem elég az átpakoláshoz, úgyhogy inkább nem csináltam semmit";
                }




            } else {

                $inventory_model->moveItemsBetweenStorages($from_id, $to_id);

                $message = "tételek átmozgatva!";
            }

        } else {

            $inventory_model->removeItemsFromStorage($from_id);

            $message = "tételek törölve!";
        }

        return new JsonResponse(["success" => 1, "message" => $message]);
    }

    /**
     * Új termékek felvitele
     *
     * @return JsonResponse
     */
    public function newProducts($storage_to = 0)
    {
        if(empty($storage_to)) {
            $storage_to = 1;
        }


        $products[0] = [
            "name" => 'matchbox (volvo)',
            "brand_id" => 1,
            "color" => 'red',
            "price" => 300,
            "barcode" => '1125654FG',
            "sku" => 'MB-VOLVO112',
            "width" => 3,
            "length" => 6,
            "storage" => $storage_to
        ];

        $products[1] = [
            "name" => 'farmernadrág, női, s',
            "brand_id" => 2,
            "color" => 'kék',
            "price" => 1200,
            "barcode" => '556318934',
            "sku" => 'JNOISMALL',
            "width" => 8,
            "length" => 9,
            "storage" => $storage_to
        ];

        $products[2] = [
            "name" => 'FIFA 2020',
            "brand_id" => 3,
            "color" => 'white',
            "price" => 9300,
            "barcode" => '0983701223FG',
            "sku" => 'FIFA2020_01',
            "width" => 23,
            "length" => 7,
            "storage" => $storage_to
        ];

        $products[3] = [
            "name" => 'elnöki lakosztály',
            "brand_id" => 4,
            "color" => '-',
            "price" => 300,
            "barcode" => '8488458444HG',
            "sku" => 'TRUMPHOTELPRES',
            "width" => 6,
            "length" => 100,
            "storage" => $storage_to
        ];

        $products[4] = [
            "name" => 'motor',
            "brand_id" => 5,
            "color" => '-',
            "price" => 12300,
            "barcode" => '894385JSD',
            "sku" => 'HYUNDAI-500',
            "width" => 98,
            "length" => 34,
            "storage" => $storage_to
        ];

        $products[5] = [
            "name" => 'sajtburger',
            "brand_id" => 1,
            "color" => 'multi',
            "price" => 500,
            "barcode" => '43246766',
            "sku" => 'SJTBURGER3456',
            "width" => 7,
            "length" => 66,
            "storage" => $storage_to
        ];

        $inventory_to = $this->getDoctrine()
            ->getRepository(Storages::class)
            ->find($storage_to);

        $capacity = $inventory_to->getCapacity();

        $i = 0;
        foreach($products as $product) {

            $new_productg = new Productgroups();
            $new_productg->setName($product["name"]);
            $new_productg->setPrice($product["price"]);
            $new_productg->setBrandId($product["brand_id"]);
            $this->getDoctrine()->getManager()->persist($new_productg);
            $this->getDoctrine()->getManager()->flush();

            $new_product = new Products();
            $new_product->setSku($product["sku"]);
            $new_product->setDimensionLength($product["length"]);
            $new_product->setDimensionWidth($product["width"]);
            $new_product->setBarcode($product["barcode"]);
            $new_product->setGroupId($new_productg->getId());

            $this->getDoctrine()->getManager()->persist($new_product);
            $this->getDoctrine()->getManager()->flush();

            if(!empty($product["storage"])) {

                if($i < $capacity) {

                    $new_inventory = new Inventory();
                    $new_inventory->setStorageId($product["storage"]);
                    $new_inventory->setProductId($new_product->getId());

                    $this->getDoctrine()->getManager()->persist($new_inventory);
                    $this->getDoctrine()->getManager()->flush();

                } else {

                    // következő raktár megkeresése, ha nincs elég hely az elsőben
                    $found = false;
                    $j = 0;
                    do {

                        $j++;

                        $storage2 = $this->getDoctrine()
                            ->getRepository(Storages::class)
                            ->find($storage_to + $j);



                        if(!empty($storage2)) {
                            $found = true;
                        }

                    } while (!$found);


                    $capacity_2 = $storage2->getCapacity();

                    // ha a második raktárba befér, akkor oda is rakjon
                    if($capacity_2->getCapacity() > $i - $capacity ) {

                        $new_inventory = new Inventory();
                        $new_inventory->setStorageId($storage2->getId());
                        $new_inventory->setProductId($new_product->getId());

                        $this->getDoctrine()->getManager()->persist($new_inventory);
                        $this->getDoctrine()->getManager()->flush();


                        $message = "tételek hozzáadva és betöltve";

                    } else {
                        // ha nem fér be kettőbe, akkor üzenet

                        $message = "két raktár sem elég az átpakoláshoz, úgyhogy inkább nem csináltam semmit";
                    }
                }

                $i++;
            }

        }

        return new JsonResponse(["success" => 1, "message" => $message]);

    }

}