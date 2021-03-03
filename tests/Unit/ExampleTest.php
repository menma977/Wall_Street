<?php

namespace Tests\Unit;

use App\Models\BillCamel;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ExampleTest extends TestCase
{
  /**
   * A basic test example.
   *
   * @return void
   */
  public function testBasicTest()
  {
    (new BillCamel([
      "user" => 1,
      "value" => 1,
      "last_try" => Carbon::now(),
      "status" => false
    ]))->save();
    $this->assertTrue(true);
  }
}
