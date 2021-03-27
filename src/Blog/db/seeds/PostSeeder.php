<?php

use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $data = [
            "title" =>  "The golden rules you need to know for a positive life",
            "slug"  =>  "the-golden-rules-you-need-to-know-for-a-positive-life",
            "content"   =>  "In lobortis pharetra mattis. Morbi nec nibh iaculis, 
            bibendum augue a, ultrices nulla. Nunc velit ante, lacinia id tincidunt eget, 
            faucibus nec nisl. In mauris purus, bibendum et gravida dignissim, 
             venenatis commodo lacus. Duis consectetur quis nisi nec accumsan. 
             Pellentesque enim velit, ut tempor turpis. Mauris felis neque, egestas 
             in lobortis et,iaculis at nunc ac, rhoncus sagittis ipsum. Maecenas non convallis 
             quam, eu sodales justo. Pellentesque quis lectus elit. Lorem ipsum dolor sit amet, 
             consectetur adipiscing elit. Donec nec metus sed leo sollicitudin 
             ornare sed consequat neque. Aliquam iaculis neque quis dui venenatis, 
             eget posuere felis viverra. Ut sit amet feugiat elit, nec elementum 
             velit. Sed eu nisl convallis, efficitur turpis eu, euismod nunc. Proin neque enim, 
             malesuada non lobortis nec, facilisis et lectus. Ie consectetur. 
             Nam eget neque ac ex fringilla dignissim eu ac est. Nunc et nisl vel 
             odio posuere. Vivamus non condimentum orci. Pellentesque venenatis nibh 
             sit amet est vehicula lobortis. Cras eget aliquet eros. Nunc lectus elit, 
             suscipit at nunc sed, finibus imperdiet ipsum. Maecenas dapibus neque 
             sodales nulla finibus volutpat. Integer pulvinar massa vitae ultrices 
             posuere. Proin ut tempor turpis. Mauris felis neque, egestas in lobortis et, 
             sodales non ante. Ut vestibulum libero quis luctus tempus. Nullam eget 
             dignissim massa. Vivamus id condimentum orci. Nunc ac sem urna. Aliquam et 
             hendrerit nisl massa nunc.",
            "created_date"  =>  date("Y-m-d H:i:s"),
            "apdated_date"   =>  date("Y-m-d H:i:s"),
            "view"  =>  0
        ];
        $this->table("posts")
            ->insert($data)
            ->save()
        ;
    }
}
