<?php

use PHPUnit\Framework\TestCase;
use Tal7aouy\Cart;

class CartTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cart = new Cart();
    }

    public function test_add_product(): void
  {
      $this->cart->add('123', 1, [
      'price'  => '5.99',
      'color'  => 'yellow',
      'size'   => 'SM'
    ]);
    $expected = [
      "id" => "123",
      "quantity" => 1,
      "hash" => "5d272e9dfb6fbbd44fa1ec3c64780003",
      "attributes" => [
        "price" => '5.99',
        "color" => "yellow",
        "size" => "SM"
      ]
    ];
    $this->assertSame($expected, $this->cart->getItem('123'));
  }
  public function test_has_item(): void
  {
      // without attributes
      $this->cart->add('123', 1);
    $expected = $this->cart->has('123');
    $this->assertSame(true,$expected);
    // with attributes
      $this->cart->add('123', 1,[
          "price" => '5.99',
          "color" => "yellow",
          "size" => "SM"
      ]);
      $expected = $this->cart->has('123',[
          "price" => '5.99',
          "color" => "yellow",
          "size" => "SM"
      ]);
      $this->assertSame(true,$expected);
  }

    public function test_update_item(): void
    {
        // add item first
        $this->cart->add('123', 1,[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $isUpdated = $this->cart->update('123', 4, [
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $this->assertEquals(true,$isUpdated);

    }

    public function test_remove_item(): void
    {
        // add item first
        $this->cart->add('123', 1,[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $this->cart->remove('123',[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $this->assertEmpty($this->cart->isEmpty());
    }

    public function test_get_item(): void
    {
        $this->cart->add('123', 1,[
            'price'=> '5.99',
            'color'=> 'yellow',
            'size'=> 'SM'
        ]);
        $item = $this->cart->getItem('123');
        $this->assertEquals($item, $this->cart->getItem($item['id'],$item['hash']));
    }

    public function test_get_items(): void
    {
        $this->cart->add('123', 1,[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $this->cart->add('456', 1,[
            "price" => '10.99',
            "color" => "green",
            "size" => "XS"
        ]);
        $this->assertCount(2,$this->cart->getItems());
    }

    public function test_is_empty(): void
    {
        $this->assertEmpty($this->cart->isEmpty());
    }

    public function test_get_total_items(): void
    {   // clean cart first
        $this->cart->clear();

        $this->cart->add('123', 2,[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $this->cart->add('456', 1,[
            "price" => '12.50',
            "color" => "green",
            "size" => "XS"
        ]);
        $this->assertEquals(2,$this->cart->getTotalItems());
    }
    public function test_get_total_quantity(): void
    {    // clean cart first
        $this->cart->clear();

        $this->cart->add('123', 4,[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);

        $this->assertSame(4,$this->cart->getTotalQuantity());
    }

    public function test_get_total_Attribute(): void
    {
        // clean cart first
        $this->cart->clear();

        $this->cart->add('123', 2,[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $this->cart->add('456', 3,[
            "price" => '12.50',
            "color" => "green",
            "size" => "XS"
        ]);

        $expected = 49.48;
        $this->assertEquals($expected, number_format($this->cart->getTotalAttribute(),2));
    }

    public function test_clear_cart(): void
    {
        $this->cart->add('123', 2,[
            "price" => '5.99',
            "color" => "yellow",
            "size" => "SM"
        ]);
        $this->cart->add('456', 3,[
            "price" => '12.50',
            "color" => "green",
            "size" => "XS"
        ]);
        $this->assertEmpty($this->cart->clear());
    }


}
