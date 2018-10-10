<?php
namespace Gbili\Tests\Miner\Blueprint\Action\GetContents\Contents;

class MockContentsFetcherWebPage implements \Gbili\Miner\Blueprint\Action\GetContents\Contents\ContentsFetcherInterface
{
    public function fetch(\Gbili\Url\UrlInterface $url)
    {
        if ($url->toString() !== 'http://somedomain.com') {
            return false;
        }
        return 'This mock webpage contains a title to extract and 3 links

                asdfasdfasdf asd fa sdf asd f asdf<title>The Title</title>
                then come the links 
                here they go <a class="some-class" aria-data="some-data" href="http://link1.com" aria-value="some-value">link content</a>
                here they go <a href="http://link2.com" class="some-class" aria-data="some-data" aria-value="some-value">link content</a>
                here they go <a class="some-class" aria-data="some-data" href="http://link3.com">link content</a>
                and there is also some more content';
    }
}
