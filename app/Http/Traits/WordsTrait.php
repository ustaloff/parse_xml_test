<?php

namespace App\Http\Traits;

use DOMDocument;

trait WordsTrait
{
	/**
     * Parse all words from xml
     *
     * @return Array
     */
	public function getPopularWords()
	{	
        $path = 'https://www.theregister.co.uk/software/headlines.atom';

        $xml = file_get_contents($path);

        $dom = new DOMDocument;
        $dom->loadXML($xml);

        //Get nodes only with words
        $titleNode = iterator_to_array($dom->getElementsByTagName('title'));
        $rightsNode = iterator_to_array($dom->getElementsByTagName('rights'));
        $nameNode = iterator_to_array($dom->getElementsByTagName('name'));
        $summaryNode = iterator_to_array($dom->getElementsByTagName('summary'));
        $nodes = array_merge($titleNode, $rightsNode, $nameNode, $summaryNode);

        //Remove all unneeded symbols from each node and convert its to string
        $str = '';
        foreach ($nodes as $node) {
            //remove html tags
            $temp = strip_tags($node->nodeValue);
            //split camel case
            $temp = join('#', preg_split('/(?=[A-Z])/', $temp));
            //remove spacial symbols and digits
            $temp = preg_replace('/[^A-Za-z\-]/', ' ', $temp);
            //remove small words
            $temp = preg_replace('/\b[a-z]{1,2}\b\s?/i', '', $temp);
            //remove spaces
            //$temp = trim(preg_replace('/\s+/', ' ', $temp));

            $temp = strtolower($temp);

            $str .= $temp;
        }

        //Count the number of each word
        $populare_arr = explode(' ', $str);
        $populare_arr = array_count_values($populare_arr);

        unset($populare_arr[null]);

        return $populare_arr;
	}

	/**
     * Parse top 50 popular words from html
     *
     * @return Array
     */
	public function getTopWords()     
	{               
		$path = 'https://en.wikipedia.org/wiki/Most_common_words_in_English';

        $html = file_get_contents($path);

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $table = $dom->getElementsByTagName('table')->item(0);

        //Get 1st 50 words
        $top_arr = [];
        $count = 0;
        
        foreach($table->getElementsByTagName('tr') as $tr)
        {
            $td = $tr->getElementsByTagName('td');

            if ($td->item(0))
            {
                $top_arr[] = $td->item(0)->nodeValue;
                $count++;
            }

            if ($count >= 50) break;
        }

        return $top_arr;
	}

	/**
     * Parse top 50 popular words from html
     *
     * @return Array
     */
	public function getWords()     
	{               
		$populare_arr = $this->getPopularWords();
        $top_arr = $this->getTopWords();

        //Remove different words
        $populare_arr = array_diff_key($populare_arr, array_fill_keys($top_arr, '#'));

        arsort($populare_arr);

        //Get top 10 words
        $populare_arr = array_slice($populare_arr, 0, 10);

        return $populare_arr;
	}
} 