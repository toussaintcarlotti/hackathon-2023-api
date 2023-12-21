<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class GetGbData
{
    use AsAction;

    public function handle($limit)
    {
        $data = Http::get(config('gb.sections_url'))->json();
        // get sections from the data
        $sections = $data['gbsettings']['sections'];

        // get all ['contentSource']['url'] values
        $urls = array_column($sections, 'contentSource');
        $urls = array_column($urls, 'url');

        array_shift($urls);
        foreach ($urls as $url) {
            $count = 0;
            $temp = Http::get(config('gb.content_base_url') . $url . '?local=1')->json();

            if (isset($temp['items'])) {
                foreach ($temp['items'] as $item) {
                    if ($item['type'] !== 'photo') {
                        $returnData[$item['id']]['type'] = $item['type'] ?? "";
                        $returnData[$item['id']]['title'] = $item['title'] ?? "";
                        $returnData[$item['id']]['summary'] = $item['summary'] ?? $item['leadin'] ?? str($item['content'])->words(150, '...') ?? "";
                    }
                }
            }


            while (isset($temp['next_page']) && $temp['next_page'] !== "" && $count < $limit) {
                $count++;
                $temp = Http::get($temp['next_page'] . '&local=1')->json();

                if (isset($temp['items'])) {
                    foreach ($temp['items'] as $item) {
                        if ($item['type'] !== 'photo') {
                            $returnData[$item['id']]['type'] = $item['type'] ?? "";
                            $returnData[$item['id']]['title'] = $item['title'] ?? "";
                            $returnData[$item['id']]['summary'] = $item['summary'] ?? $item['leadin'] ?? str($item['content'])->words(150, '...') ?? "";
                        }
                    }
                }
            }
        }

        return $returnData;
    }
}
