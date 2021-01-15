<?php

namespace Database\Seeders;

use App\Models\ListUrl;
use Illuminate\Database\Seeder;

class ListUrlSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = new ListUrl();
    $data->url = "https://www.999doge.com/api/web.aspx";
    $data->block = false;
    $data->save();

    $data = new ListUrl();
    $data->url = "https://corsdoge.herokuapp.com/doge";
    $data->block = false;
    $data->save();
  }
}
